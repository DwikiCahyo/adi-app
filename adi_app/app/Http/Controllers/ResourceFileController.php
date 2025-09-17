<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\ResourceFile;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResourceFileController extends Controller
{
    public function index(Request $request): View|JsonResponse{
        $resourceFiles = ResourceFile::with(['creator', 'updater'])
                    ->active()
                    ->recent()
                    ->get()
                    ->map(function ($item) {
                        $item->file_url = $item->file_path
                            ? asset('storage/' . $item->file_path)
                            : null;
                        return $item;
                    });

        Log::info("ResourceFile index request", [
            'total_resourcefile' => $resourceFiles->count(),
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $resourceFiles, // kalau ada ResourceFileResource, pakai itu
                'total'   => $resourceFiles->count(),
                'message' => 'Data Resource File berhasil ditampilkan'
            ]);
        }

        return view('resource.file', compact('resourceFiles'));
    }

    public function show(Request $request, ResourceFile $resourceFile){
        $resourceFile->load(['creator', 'updater']);

        Log::info("ResourceFile show request", [
            'resourcefile_id' => $resourceFile->id,
            'resourcefile_slug' => $resourceFile->slug,
            'title' => $resourceFile->title,
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // buat URL file kalau ada path
        $resourceFile->file_url = $resourceFile->file_path
            ? asset('storage/' . $resourceFile->file_path)
            : null;

        // 🔹 Ambil semua data untuk archive (kecuali current)
        $archives = ResourceFile::orderBy('created_at', 'desc')
            ->where('id', '!=', $resourceFile->id)
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $resourceFile,
                'message' => 'Data resource file berhasil ditampilkan'
            ]);
        }

        return view('resourcefile.show', compact('resourceFile', 'archives'));
    }

    public function showfile($id){
        $resourcefile = ResourceFile::findOrFail($id);
        return view('resourcefile.showfile', compact('resourcefile'));
    }


    public function download($id){
        $resourcefile = ResourceFile::findOrFail($id);

        if ($resourcefile->file_path && Storage::disk('public')->exists($resourcefile->file_path)) {
            // Nama file asli (kalau ada di DB, fallback ke basename path)
            $filename = $resourcefile->nama_file ?? basename($resourcefile->file_path);

            return Storage::disk('public')->download($resourcefile->file_path, $filename);
        }

        return back()->with('error', 'File tidak ditemukan.');
    }



    public function ResourceFileAdmin(){
        $resourcefile = ResourceFile::with(['creator', 'updater'])
            ->active()
            ->get();

        return view('admin.resource.file', compact('resourcefile'));
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'nama_file' => 'required|string|max:255',
            'file_path' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,png,jpeg|max:20480', 
            'content' => 'nullable|string',
        ]);

        // Simpan file ke storage/app/public/resourcefile
        $filePath = $request->file('file_path')->store('resourcefile', 'public');

        $validatedData['file_path'] = $filePath;
        $validatedData['created_by'] = auth()->id();
        $validatedData['updated_by'] = auth()->id();

        $resourceFile = ResourceFile::create($validatedData);

        Log::info("ResourceFile created", [
            'resourcefile_id' => $resourceFile->id,
            'title' => $resourceFile->title,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('admin.resourcefile.file')->with('success', 'Resource File berhasil dibuat!');
    }

    public function update(Request $request, $id){
        $resourceFile = ResourceFile::findOrFail($id);

        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'nama_file' => 'required|string|max:255',
                'file_path' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,png,jpeg|max:20480',
                'content' => 'nullable|string',
            ]);

            $validatedData['updated_by'] = auth()->id();

            // simpan data lama buat log
            $oldData = $resourceFile->only(['title', 'nama_file', 'file_path', 'content', 'slug']);

            // kalau ada file baru, hapus file lama lalu upload baru
            if ($request->hasFile('file_path')) {
                if ($resourceFile->file_path && Storage::disk('public')->exists($resourceFile->file_path)) {
                    Storage::disk('public')->delete($resourceFile->file_path);
                }
                $validatedData['file_path'] = $request->file('file_path')->store('resourcefile', 'public');
            }

            $resourceFile->fill($validatedData);

            // update slug kalau judul berubah
            if (!empty($validatedData['title']) && $validatedData['title'] !== $oldData['title']) {
                $resourceFile->slug = $validatedData['title'];
            }

            $resourceFile->save();

            Log::info("ResourceFile updated", [
                'resourcefile_id' => $resourceFile->id,
                'old_data' => $oldData,
                'new_data' => $resourceFile->only(['title', 'nama_file', 'file_path', 'content', 'slug']),
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return back()->with('success', 'Resource File berhasil diedit!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ResourceFile update failed", [
                'resourcefile_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors('Terjadi kesalahan saat update Resource File');
        }
    }

    public function destroy($id){
        $resourceFile = ResourceFile::findOrFail($id);

        // hapus file fisik kalau ada
        if ($resourceFile->file_path && Storage::disk('public')->exists($resourceFile->file_path)) {
            Storage::disk('public')->delete($resourceFile->file_path);
        }

        $resourceFile->delete();

        return back()->with('success', 'Resource File berhasil dihapus!');
    }
}
