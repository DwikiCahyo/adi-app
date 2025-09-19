<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\EventImage;
use App\Models\EventTopic;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    public function index()
    {
        $event = Events::with(['creator', 'updater', 'images', 'topics'])
            ->active()
            ->get();

        return view('admin.event.index', compact('event'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agenda'            => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'topics.*.topic'    => 'nullable|string|max:255',
            'topics.*.content'  => 'required|string',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png|max:40048',
        ], [
            'agenda.required' => 'Agenda wajib diisi.',
            'title.required' => 'Judul wajib diisi.',
            'topics.*.topic.required' => 'Judul topik wajib diisi.',
            'topics.*.content.required' => 'Isi konten wajib diisi.',
            'images.*.image' => 'File harus berupa gambar.',
        ]);
        

        // === Buat event utama ===
        $event = Events::create([
            'agenda' => $request->agenda,
            'title'  => $request->title,
            // slug otomatis di-handle oleh mutator setSlugAttribute
        ]);

        // === Insert topics ===
        if ($request->has('topics')) {
            foreach ($request->topics as $topic) {
                $event->topics()->create([
                    'topic'   => $topic['topic'],
                    'content' => $topic['content'],
                ]);
            }
        }

        // === Upload images ===
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->storeAs('public/events', $imageName);

                $event->images()->create([
                    'image' => $imageName,
                ]);
            }
        }

        return back()->with('success', 'Event berhasil ditambah!');
    }

    public function update(Request $request, $id){
        $event = Events::findOrFail($id);
        
        $request->validate([
            'agenda'            => 'nullable|string|max:255',
            'title'             => 'required|string|max:255',
            'topics.*.topic'    => 'nullable|string|max:255',
            'topics.*.content'  => 'required|string',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:40048',
        ]);

        // Update event utama
        $event->update([
            'agenda' => $request->agenda,
            'title'  => $request->title,
        ]);

        // Hapus semua topic lama
        $event->topics()->delete();

        // Tambahkan topic baru (jika ada)
        foreach($request->topics ?? [] as $topic){
            if(!empty($topic['topic']) && !empty($topic['content'])){
                $event->topics()->create([
                    'topic'   => $topic['topic'],
                    'content' => $topic['content'],
                ]);
            }
        }

        // Hapus semua gambar lama sebelum upload baru
        if ($request->hasFile('images')) {
            // 1. Hapus record database
            foreach ($event->images as $img) {
                // 2. Hapus file fisik
                $path = storage_path('app/public/events/' . $img->image);
                if (file_exists($path)) {
                    unlink($path);
                }
                $img->delete();
            }

            // 3. Upload gambar baru
            foreach ($request->file('images') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->storeAs('public/events', $imageName);

                EventImage::create([
                    'event_id' => $event->id,
                    'image'    => $imageName,
                ]);
            }
        }

        return back()->with('success', 'Event berhasil diperbarui!');
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
