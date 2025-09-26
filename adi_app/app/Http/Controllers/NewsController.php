<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\News;
use App\Models\NewsImage;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller {

    public function index(Request $request): View|JsonResponse{
        $news = News::with(['creator', 'updater', 'images'])
                    ->active()
                    ->get()
                    ->map(function ($item) {
                        $item->thumbnail_url = $this->getVideoThumbnail($item->url);
                        // Add first image as featured image if exists
                        $item->featured_image = $item->images->first()?->image 
                            ? Storage::url($item->images->first()->image) 
                            : null;
                        return $item;
                    });

        Log::info("News index request", [
            'total_news' => $news->count(),
            'expects_json' => $request->expectsJson(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => NewsResource::collection($news),
                'total'   => $news->count(),
                'message' => 'Data news berhasil ditampilkan'
            ]);
        }

        return view('news.index', compact('news'));
    }

    /**
     * Ambil thumbnail dari URL video
     */
     private function getVideoThumbnail($url){
        if (empty($url)) return asset('images/default-thumbnail.jpg');
        
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([^\&\?]+)/', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://vumbnail.com/' . $matches[1] . '.jpg';
        }

        return asset('images/default-thumbnail.jpg');
    }

    public function show(Request $request, News $news){
        $news->load(['creator', 'updater', 'images']);

        Log::info("News show request", [
            'news_id'      => $news->id,
            'news_slug'    => $news->slug,
            'title'        => $news->title,
            'expects_json' => $request->expectsJson(),
            'ip'           => $request->ip(),
            'user_agent'   => $request->userAgent()
        ]);

        $news->embed_url = $news->url
            ? $this->convertVideoToEmbed($news->url)
            : null;

        // Add image URLs for display
        $news->image_urls = $news->images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => Storage::url($image->image),
                'filename' => basename($image->image)
            ];
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => new NewsResource($news),
                'message' => 'Data news berhasil ditampilkan'
            ]);
        }

        return view('news.show', compact('news'));
    }

    /**
     * Convert video link to embeddable link.
     */
    private function convertVideoToEmbed($url){
        if (empty($url)) return null;
        
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([^\&\?]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $url; 
    }

    //ADMIN
    public function NewsAdmin(){
        $news = News::with(['creator', 'updater', 'images'])
            ->active()
            ->get();

        // tambahin embed & thumbnail
        $news->transform(function ($item) {
            $item->embed_url = $item->url ? $this->convertVideoToEmbed($item->url) : null;
            $item->thumbnail_url = $item->url
                ? $this->getVideoThumbnail($item->url)
                : asset('images/default-thumbnail.jpg');
            
            // Add featured image
            $item->featured_image = $item->images->first()?->image 
                ? Storage::url($item->images->first()->image) 
                : null;
            
            // Add all image URLs
            $item->image_urls = $item->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => Storage::url($image->image),
                    'filename' => basename($image->image)
                ];
            });
            
            return $item;
        });

        return view('admin.dashboard', compact('news'));
    }

    public function store(StoreNewsRequest $request){
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
            $validatedData['created_by'] = auth()->id();
            $validatedData['updated_by'] = auth()->id();
        
            $news = News::create($validatedData);
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($request->file('images'), $news);
            }
        
            Log::info("News created", [
                'news_id' => $news->id,
                'title' => $news->title,
                'created_by' => auth()->id(),
                'images_count' => $news->fresh()->images->count()
            ]);
            
            DB::commit();
        
            return redirect()->route('admin.dashboard')->with('success', 'News berhasil dibuat!');
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            Log::error("News creation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'created_by' => auth()->id()
            ]);
            
            return back()->withErrors('Terjadi kesalahan saat membuat news: ' . $e->getMessage())->withInput();
        }
    }
    
    public function update(UpdateNewsRequest $request, News $news)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $validatedData['updated_by'] = auth()->id();

            $oldData = $news->only(['title', 'url', 'content', 'slug']);

            // Update the news record
            $news->update($validatedData);

            Log::info("News update process started", [
                'news_id' => $news->id,
                'has_remove_images' => $request->filled('remove_images'),
                'remove_images_value' => $request->get('remove_images', ''),
                'has_new_images' => $request->hasFile('images'),
                'new_images_count' => $request->hasFile('images') ? count($request->file('images')) : 0
            ]);
            
            // STEP 1: Handle image removal FIRST
            if ($request->filled('remove_images')) {
                $removeImageIds = array_filter(explode(',', $request->remove_images));
                if (!empty($removeImageIds)) {
                    Log::info("Processing image removals", [
                        'news_id' => $news->id,
                        'image_ids_to_remove' => $removeImageIds
                    ]);
                    
                    $this->removeImages($removeImageIds);
                }
            }
            
            // STEP 2: Handle new image uploads AFTER removing old ones
            if ($request->hasFile('images')) {
                Log::info("Processing new image uploads", [
                    'news_id' => $news->id,
                    'images_count' => count($request->file('images'))
                ]);
                
                $this->handleImageUploads($request->file('images'), $news);
            }

            // Refresh the model to get updated image count
            $news = $news->fresh(['images']);

            Log::info("News updated successfully", [
                'news_id'   => $news->id,
                'old_data'  => $oldData,
                'new_data'  => $news->only(['title', 'url', 'content', 'slug']),
                'updated_by'=> auth()->id(),
                'removed_images' => $request->get('remove_images', 'none'),
                'new_images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
                'final_images_count' => $news->images->count()
            ]);

            DB::commit();

            return back()->with('success', 'News berhasil diedit! Total gambar: ' . $news->images->count());
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            Log::error("News update failed", [
                'news_id' => $news->id,
                'error'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => [
                    'remove_images' => $request->get('remove_images'),
                    'has_new_images' => $request->hasFile('images')
                ]
            ]);
            
            return back()->withErrors('Terjadi kesalahan saat update news: ' . $e->getMessage());
        }
    }

    public function destroy(News $news){
        DB::beginTransaction();
        try {
            // Delete associated images from storage
            foreach ($news->images as $image) {
                if (Storage::exists($image->image)) {
                    Storage::delete($image->image);
                }
                $image->delete();
            }
            
            $news->delete();
            
            DB::commit();
            
            Log::info("News deleted", [
                'news_id' => $news->id,
                'deleted_by' => auth()->id()
            ]);
            
            return back()->with('success', 'News berhasil didelete!');
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            Log::error("News deletion failed", [
                'news_id' => $news->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors('Terjadi kesalahan saat menghapus news');
        }
    }
    
    /**
     * Handle multiple image uploads
     */
    private function handleImageUploads($images, News $news)
    {
        $maxImages = 10; // Set maximum images per news
        $currentImageCount = $news->images()->count();
        
        Log::info("Handling image uploads", [
            'news_id' => $news->id,
            'current_image_count' => $currentImageCount,
            'new_images_count' => count($images),
            'max_images' => $maxImages
        ]);
        
        $uploadedCount = 0;
        foreach ($images as $index => $image) {
            if ($currentImageCount + $uploadedCount >= $maxImages) {
                Log::info("Max images reached, skipping remaining uploads", [
                    'news_id' => $news->id,
                    'current_count' => $currentImageCount + $uploadedCount,
                    'max_images' => $maxImages
                ]);
                break; // Stop if max images reached
            }
            
            if ($image->isValid()) {
                try {
                    $filename = $this->generateImageFilename($image, $news);
                    $path = $image->storeAs('news-images', $filename, 'public');
                    
                    NewsImage::create([
                        'news_id' => $news->id,
                        'image' => $path
                    ]);
                    
                    $uploadedCount++;
                    
                    Log::info("Image uploaded successfully", [
                        'news_id' => $news->id,
                        'filename' => $filename,
                        'path' => $path,
                        'uploaded_count' => $uploadedCount
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("Failed to upload image", [
                        'news_id' => $news->id,
                        'index' => $index,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                Log::warning("Invalid image file", [
                    'news_id' => $news->id,
                    'index' => $index,
                    'error' => $image->getError()
                ]);
            }
        }
        
        Log::info("Image upload process completed", [
            'news_id' => $news->id,
            'uploaded_count' => $uploadedCount,
            'total_images_now' => $news->images()->count()
        ]);
    }
    
    /**
     * Generate unique filename for image
     */
    private function generateImageFilename($image, News $news)
    {
        $extension = $image->getClientOriginalExtension();
        $slug = Str::slug($news->title ?? 'news', '-');
        $timestamp = now()->timestamp;
        $random = Str::random(6);
        
        return "{$slug}-{$timestamp}-{$random}.{$extension}";
    }
    
    /**
     * Remove specific images
     */
    private function removeImages(array $imageIds)
    {
        Log::info("Starting image removal process", ['image_ids' => $imageIds]);
        
        $images = NewsImage::whereIn('id', $imageIds)->get();
        
        if ($images->isEmpty()) {
            Log::warning("No images found for deletion", ['image_ids' => $imageIds]);
            return;
        }
        
        $deletedCount = 0;
        foreach ($images as $image) {
            try {
                // Delete from storage
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                    Log::info("Image file deleted from storage", [
                        'image_id' => $image->id,
                        'path' => $image->image
                    ]);
                } else {
                    Log::warning("Image file not found in storage", [
                        'image_id' => $image->id,
                        'path' => $image->image
                    ]);
                }
                
                // Delete from database
                $image->delete();
                $deletedCount++;
                
                Log::info("Image record removed from database", [
                    'image_id' => $image->id,
                    'news_id' => $image->news_id,
                    'path' => $image->image
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to delete image", [
                    'image_id' => $image->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::info("Image removal process completed", [
            'requested_count' => count($imageIds),
            'deleted_count' => $deletedCount
        ]);
    }
    
    /**
     * Remove single image (for AJAX calls)
     */
    public function removeImage($imageId)
    {
        try {
            $image = NewsImage::findOrFail($imageId);
            $newsId = $image->news_id;
            
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            
            $image->delete();
            
            Log::info("Image removed via AJAX", [
                'image_id' => $imageId,
                'news_id' => $newsId,
                'removed_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus'
            ]);
            
        } catch (\Throwable $e) {
            Log::error("Failed to remove image via AJAX", [
                'image_id' => $imageId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar'
            ], 500);
        }
    }
}