<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResourceFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PublishScheduledResourceFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resourcefile:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled resource files that have reached their publish date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for scheduled resource files to publish...');
        
        $now = now('Asia/Jakarta');
        
        // Cari semua resource file yang:
        // 1. Status = scheduled
        // 2. publish_at sudah lewat atau sama dengan waktu sekarang
        $scheduledFiles = ResourceFile::where('status', 'scheduled')
            ->where('publish_at', '<=', $now)
            ->get();
        
        if ($scheduledFiles->isEmpty()) {
            $this->info('✅ No scheduled resource files to publish at this time.');
            return 0;
        }
        
        $this->info("📝 Found {$scheduledFiles->count()} resource file(s) to publish.");
        
        $publishedCount = 0;
        $failedCount = 0;
        
        foreach ($scheduledFiles as $file) {
            try {
                $file->status = 'published';
                $file->save();
                
                $publishedCount++;
                
                $this->info("✅ Published: {$file->title} (ID: {$file->id})");
                
                Log::info("ResourceFile auto-published", [
                    'resourcefile_id' => $file->id,
                    'title' => $file->title,
                    'publish_at' => $file->publish_at->format('Y-m-d H:i:s'),
                    'published_at' => $now->format('Y-m-d H:i:s')
                ]);
                
            } catch (\Exception $e) {
                $failedCount++;
                
                $this->error("❌ Failed to publish: {$file->title} (ID: {$file->id})");
                $this->error("   Error: {$e->getMessage()}");
                
                Log::error("ResourceFile auto-publish failed", [
                    'resourcefile_id' => $file->id,
                    'title' => $file->title,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        // Summary
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("   ✅ Published: {$publishedCount}");
        if ($failedCount > 0) {
            $this->error("   ❌ Failed: {$failedCount}");
        }
        
        return 0;
    }
}