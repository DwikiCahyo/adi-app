<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PublishScheduledResources extends Command
{
    protected $signature = 'resource:publish-scheduled';
    protected $description = 'Publish scheduled Latest Sermons that have reached their publish date';

    public function handle()
    {
        $this->info('🔍 Checking for scheduled Latest Sermons to publish...');
        
        $now = now('Asia/Jakarta');
        
        $scheduledResources = Resource::where('status', 'scheduled')
            ->where('publish_at', '<=', $now)
            ->get();
        
        if ($scheduledResources->isEmpty()) {
            $this->info('✅ No scheduled Latest Sermons to publish at this time.');
            return 0;
        }
        
        $this->info("📋 Found {$scheduledResources->count()} Latest Sermon(s) to publish.");
        
        $publishedCount = 0;
        $failedCount = 0;
        
        foreach ($scheduledResources as $resource) {
            try {
                $resource->status = 'published';
                $resource->save();
                
                $publishedCount++;
                
                $this->info("✅ Published: {$resource->title} (ID: {$resource->id})");
                
                Log::info("Resource auto-published", [
                    'resource_id' => $resource->id,
                    'title' => $resource->title,
                    'publish_at' => $resource->publish_at->format('Y-m-d H:i:s'),
                    'published_at' => $now->format('Y-m-d H:i:s')
                ]);
                
            } catch (\Exception $e) {
                $failedCount++;
                
                $this->error("❌ Failed to publish: {$resource->title} (ID: {$resource->id})");
                $this->error("   Error: {$e->getMessage()}");
                
                Log::error("Resource auto-publish failed", [
                    'resource_id' => $resource->id,
                    'title' => $resource->title,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("   ✅ Published: {$publishedCount}");
        if ($failedCount > 0) {
            $this->error("   ❌ Failed: {$failedCount}");
        }
        
        return 0;
    }
}