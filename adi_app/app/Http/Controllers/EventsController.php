<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\EventImage;
use App\Models\EventTopic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventsController extends Controller
{
    public function index()
    {
        $event = Events::with(['creator', 'updater', 'images', 'topics'])
            ->active()
            ->get();

        return view('admin.event.index', compact('event'));
    }

   public function store(StoreEventRequest $request)
    {
        try {

            DB::beginTransaction();

            $validatedData = $request->validated();

            $event = Events::create([
                'agenda' => $validatedData['agenda'] ?? null,
                'title'  => $validatedData['title'],
            ]);

            // insert topics
            if (isset($validatedData['topics'])) {
                foreach ($validatedData['topics'] as $topic) {
                    Log::debug($topic);
                    EventTopic::create([
                        'event_id' => $event->id,
                        'topic'    => $topic['topic'],
                        'content'  => $topic['content'],
                    ]);
                }
            }

            // upload images
            if (isset($validatedData['images'])) {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->storeAs('public/events', $imageName);

                    EventImage::create([
                        'event_id' => $event->id,
                        'image'    => $imageName,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Event berhasil ditambah!');
        } catch (\Throwable $e) {
            Log::error('Event store failed', [
                'error'   => $e->getMessage(),
                'request' => $request->all()
            ]);

            return back()->withErrors(['general' => 'Terjadi kesalahan saat menyimpan event, error : ' .$e->getMessage()])->withInput();
        }
    }


    public function update(Request $request, $id){
        

        DB::beginTransaction();

         $request->validate([
            'agenda'            => 'nullable|string|max:255',
            'title'             => 'required|string|max:255',
            'topics.*.topic'    => 'required|string|max:255',
            'topics.*.content'  => 'required|string',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);


        try{
            $event = Events::findOrFail($id);
            //dd($request -> all());

            $event->update([
                'agenda' => $request->agenda,
                'title'  => $request->title,
            ]);


            Log::debug('request topics', collect($request->topics)->filter()->toArray());
            Log::debug('get existing topic', $event->topics->toArray());

            $existingTopicIds = $event->topics->pluck('id')->toArray();
            $sentTopicIds = collect($request->topics)->pluck('id')->filter()->toArray();
            $toDelete = array_diff($existingTopicIds, $sentTopicIds);

            if(isset($toDelete)){
                Log::debug('must delete topic : ', $toDelete);
                EventTopic::whereIn('id', $toDelete)->where('event_id', $event->id)->delete();
            }
            
            if($request->has('topics') && is_array($request->topics)){
                Log::debug('add topics');
                foreach ($request->topics as $topicData) {
                    if (!empty($topicData['id'])) {
                        $topic = EventTopic::find($topicData['id']);
                        $topic->update([
                            'topic'   => $topicData['topic'],
                            'content' => $topicData['content'],
                        ]);
                    } else {
                        $event->topics()->create([
                            'topic'   => $topicData['topic'],
                            'content' => $topicData['content'],
                        ]);
                    }
               }
            }

            DB::commit();
            return back()->with('succsess', 'Event berhasil diperbarui!');
            

        }catch(\Throwable $e){
            DB::rollback();

             Log::error('Event store failed', [
                'error'   => $e->getMessage(),
                'request' => $request->all()
            ]);

            return back()->withErrors(['general' => 'Terjadi kesalahan saat menyimpan event, error : ' .$e->getMessage()])->withInput();
        }

        // $topics = $event->topics;
        
        // $request->validate([
        //     'agenda'            => 'nullable|string|max:255',
        //     'title'             => 'required|string|max:255',
        //     'topics.*.topic'    => 'required|string|max:255',
        //     'topics.*.content'  => 'required|string',
        //     'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        // ]);

        // // Update event utama
        // $event->update([
        //     'agenda' => $request->agenda,
        //     'title'  => $request->title,
        // ]);

        // // Update topics (hapus lama, insert baru)
        // //$event->topics()->delete();
        // if ($request->has('topics')) {
        //     foreach ($request->topics as $topic) {
        //         $event->topics()->create([
        //             'topic'   => $topic['topic'],
        //             'content' => $topic['content'],
        //         ]);
        //     }
        // }

        // // Hapus semua gambar lama sebelum upload baru
        // if ($request->hasFile('images')) {
        //     // 1. Hapus record database
        //     foreach ($event->images as $img) {
        //         // 2. Hapus file fisik
        //         $path = storage_path('app/public/events/' . $img->image);
        //         if (file_exists($path)) {
        //             unlink($path);
        //         }
        //         $img->delete();
        //     }

        //     // 3. Upload gambar baru
        //     foreach ($request->file('images') as $file) {
        //         $imageName = time() . '_' . uniqid() . '.' . $file->extension();
        //         $file->storeAs('public/events', $imageName);

        //         EventImage::create([
        //             'event_id' => $event->id,
        //             'image'    => $imageName,
        //         ]);
        //     }
        // }

        //  Log::debug('Event updated', [
        //         'request' => $request->all(),
        //         'event' => $event,
        //         'topics' => $topics
        //     ]);

        // return back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy($id){
        $event = Events::findOrFail($id);
        // Hapus images
        foreach ($event->images as $img) {
            if (Storage::exists('public/events/' . $img->image)) {
                Storage::delete('public/events/' . $img->image);
            }
            $img->delete();
        }

        // Hapus topics
        $event->topics()->delete();

        // Hapus event
        $event->delete();

        return back()->with('success', 'Event berhasil dihapus!');
    }


    //User
    public function indexUser()
    {
        $event = Events::with(['creator', 'updater', 'images', 'topics'])
            ->active()
            ->get();

        return view('eventUser.index', compact('event'));
    }

    public function showUser(Events $event)
    {
        $event->load(['images', 'topics']);
        return view('eventUser.show', compact('event'));
    }
    


}
