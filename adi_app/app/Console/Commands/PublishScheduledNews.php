<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PublishScheduledNews extends Command
{
    protected $signature = 'news:publish-scheduled';
    protected $description = 'Publish scheduled news that have reached their publish date';

    public function handle()
    {
        $this->info('🔍 Checking for scheduled news to publish...');
        
        $now = now('Asia/Jakarta');
        
        $scheduledNews = News::where('status', 'scheduled')
            ->where('publish_at', '<=', $now)
            ->get();
        
        if ($scheduledNews->isEmpty()) {
            $this->info('✅ No scheduled news to publish at this time.');
            return 0;
        }
        
        $this->info("📋 Found {$scheduledNews->count()} news to publish.");
        
        $publishedCount = 0;
        $failedCount = 0;
        
        foreach ($scheduledNews as $news) {
            try {
                $news->status = 'published';
                $news->save();
                
                $publishedCount++;
                
                $this->info("✅ Published: {$news->title} (ID: {$news->id})");
                
                Log::info("News auto-published", [
                    'news_id' => $news->id,
                    'title' => $news->title,
                    'publish_at' => $news->publish_at->format('Y-m-d H:i:s'),
                    'published_at' => $now->format('Y-m-d H:i:s')
                ]);
                
            } catch (\Exception $e) {
                $failedCount++;
                
                $this->error("❌ Failed to publish: {$news->title} (ID: {$news->id})");
                $this->error("   Error: {$e->getMessage()}");
                
                Log::error("News auto-publish failed", [
                    'news_id' => $news->id,
                    'title' => $news->title,
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