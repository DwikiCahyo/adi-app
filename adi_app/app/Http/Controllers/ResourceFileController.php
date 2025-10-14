<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\ResourceFile;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResourceFileController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $resourceFiles = ResourceFile::with(['creator', 'updater'])
                    ->active()
                    ->published()
                    ->orderBy('publish_at', 'desc')
                    ->get();

        Log::info("ResourceFile index request", [
            'total_resourcefile' => $resourceFiles->count(),
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $resourceFiles, 'total' => $resourceFiles->count(), 'message' => 'Data Resource File berhasil ditampilkan']);
        }

        return view('resource.file', compact('resourceFiles'));
    }

    public function show(Request $request){
        $resourcefile = ResourceFile::with(['creator', 'updater'])
                    ->active()
                    ->published()
                    ->orderBy('publish_at', 'desc')
                    ->get();

        Log::info("ResourceFile show (list) request", [
            'total_resourcefile' => $resourcefile->count(),
            'expects_json' => $request->expectsJson(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'data' => $resourcefile, 
                'total' => $resourcefile->count(), 
                'message' => 'Data Resource File berhasil ditampilkan'
            ]);
        }

        // View untuk menampilkan LIST Good News
        return view('resourcefile.show', compact('resourcefile'));
    }
    
    public function showfile($id)
    {
        $resourcefile = ResourceFile::findOrFail($id);
        if (!$resourcefile->isPublished()) {
            abort(404);
        }
        return view('resourcefile.showfile', compact('resourcefile'));
    }

    public function ResourceFileAdmin()
    {
        $resourcefile = ResourceFile::with(['creator', 'updater'])->active()->orderBy('publish_at', 'desc')->get();
        return view('admin.resource.file', compact('resourcefile'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255', 'refleksi_diri' => 'required|string',
            'pengakuan_iman' => 'required|string', 'bacaan_alkitab' => 'required|string',
            'content' => 'required|string', 'tanggal' => 'required|date',
        ]);

        $now = now('Asia/Jakarta');
        $selectedDate = Carbon::parse($validatedData['tanggal'])->setTimezone('Asia/Jakarta');
        
        // =================================================================
        // PERUBAHAN LOGIKA UTAMA ADA DI SINI
        // =================================================================
        $publishDate;
        if ($selectedDate->isToday()) {
            // Jika pilih hari ini, publish SEKARANG JUGA.
            $publishDate = $now;
        } else {
            // Jika pilih tanggal lain (masa depan/lalu), set ke jam 00:00.
            $publishDate = $selectedDate->startOfDay();
        }

        $status = $publishDate->lte($now) ? 'published' : 'scheduled';
        
        $dataToSave = array_merge($validatedData, [
            'publish_at' => $publishDate,
            'status' => $status,
            'created_at' => $now,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        unset($dataToSave['tanggal']); // Hapus 'tanggal' karena sudah dihandle oleh 'publish_at'

        $resourceFile = ResourceFile::create($dataToSave);
        Log::info("ResourceFile created", ['resourcefile_id' => $resourceFile->id, 'title' => $resourceFile->title, 'status' => $status]);

        $message = $status === 'scheduled' 
            ? "Resource File berhasil dibuat dan dijadwalkan publish pada {$publishDate->format('d M Y, 00:00 WIB')}!"
            : "Resource File berhasil dibuat dan langsung dipublish!";

        return redirect()->route('admin.resourcefile.file')->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $resourceFile = ResourceFile::findOrFail($id);
        $validatedData = $request->validate([
            'title' => 'required|string|max:255', 'refleksi_diri' => 'required|string',
            'pengakuan_iman' => 'required|string', 'bacaan_alkitab' => 'required|string',
            'content' => 'required|string', 'tanggal' => 'required|date',
        ]);

        $now = now('Asia/Jakarta');
        $selectedDate = Carbon::parse($validatedData['tanggal'])->setTimezone('Asia/Jakarta');
        
        // =================================================================
        // PERUBAHAN LOGIKA UTAMA ADA DI SINI
        // =================================================================
        $publishDate;
        if ($selectedDate->isToday()) {
            // Jika pilih hari ini, publish SEKARANG JUGA.
            $publishDate = $now;
        } else {
            // Jika pilih tanggal lain (masa depan/lalu), set ke jam 00:00.
            $publishDate = $selectedDate->startOfDay();
        }

        $status = $publishDate->lte($now) ? 'published' : 'scheduled';

        DB::beginTransaction();
        try {
            $resourceFile->fill($validatedData);
            $resourceFile->publish_at = $publishDate;
            $resourceFile->status = $status;
            $resourceFile->updated_by = auth()->id();
            if ($resourceFile->isDirty('title')) {
                $resourceFile->slug = $validatedData['title'];
            }
            $resourceFile->save();
            DB::commit();

            Log::info("ResourceFile updated", ['resourcefile_id' => $resourceFile->id, 'new_status' => $status]);

            $message = $status === 'scheduled' 
                ? "Resource File berhasil diupdate dan dijadwalkan publish pada {$publishDate->format('d M Y, 00:00 WIB')}!"
                : "Resource File berhasil diupdate dan dipublish!";
            
            return back()->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ResourceFile update failed", ['resourcefile_id' => $id, 'error' => $e->getMessage()]);
            return back()->withErrors('Terjadi kesalahan saat update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $resourceFile = ResourceFile::findOrFail($id);
        $resourceFile->delete();
        Log::info("ResourceFile deleted", ['resourcefile_id' => $id, 'deleted_by' => auth()->id()]);
        return back()->with('success', 'Resource File berhasil dihapus!');
    }
}