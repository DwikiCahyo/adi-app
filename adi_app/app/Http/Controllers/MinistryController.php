<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ministry;
use App\Models\MinistryImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MinistryController extends Controller
{
    public function ministryAdmin()
    {
        $ministry = Ministry::with(['creator', 'updater'])
            ->active()
            ->get();

        return view('admin.ministry.index', compact('ministry'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'category'  => 'required|string',
            'images.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4048'
        ]);

        $ministry = Ministry::create([
            'title'      => $request->title,
            'content'    => $request->content,
            'category'   => $request->category,
            'slug'       => Str::slug($request->title) . '-' . uniqid(),
            'created_by' => Auth::id(),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('ministry', 'public');
                MinistryImage::create([
                    'ministry_id' => $ministry->id,
                    'image'       => $path
                ]);
            }
        }        

        return redirect()->route('admin.ministry.index')->with('success', 'Ministry berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
{
    $ministry = Ministry::findOrFail($id);

    $request->validate([
        'title'     => 'required|string|max:255',
        'content'   => 'required|string',
        'category'  => 'required|string',
        'images.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4048',
    ]);

    // Update data ministry
    $ministry->update([
        'title'      => $request->title,
        'content'    => $request->content,
        'category'   => $request->category,
        'updated_by' => Auth::id(),
    ]);

    // Hapus semua gambar lama jika ada gambar baru
    if ($request->hasFile('images')) {
        // Hapus record database & file fisik
        foreach ($ministry->images as $img) {
            $path = storage_path('app/public/' . $img->image);
            if (file_exists($path)) {
                unlink($path);
            }
            $img->delete();
        }

        // Upload gambar baru
        foreach ($request->file('images') as $file) {
            $imageName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('public/ministry', $imageName);

            MinistryImage::create([
                'ministry_id' => $ministry->id,
                'image'       => 'ministry/' . $imageName,
            ]);
        }
    }

    return back()->with('success', 'Ministry berhasil diperbarui!');
}

    
    public function destroy($id)
    {
        $ministry = Ministry::findOrFail($id);

        foreach ($ministry->images as $img) {
            if (file_exists(storage_path('app/public/' . $img->image))) {
                unlink(storage_path('app/public/' . $img->image));
            }
            $img->delete();
        }

        $ministry->delete();

        return redirect()->route('admin.ministry.index')->with('success', 'Ministry berhasil dihapus.');
    }

    public function indexUser($category = null)
    {
        $query = Ministry::with(['images', 'creator'])
            ->active();
    
        if ($category && in_array($category, ['Kids', 'Youth Generation', 'General'])) {
            $query->where('category', $category);
        }
    
        $ministry = $query->get();
    
        return view('ministry.index', compact('ministry', 'category'));
    }
    

    public function showUser(Ministry $ministry)
{
    $ministry->load(['images', 'topics']);
    return view('ministry.show', compact('ministry'));
}


}
