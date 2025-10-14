<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resourcefile', function (Blueprint $table) {
            $table->datetime('publish_at')->nullable()->after('content');
            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft')->after('publish_at');
            $table->index(['status', 'publish_at'], 'resourcefile_status_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::table('resourcefile', function (Blueprint $table) {
            $table->dropIndex('resourcefile_status_publish_idx');
            $table->dropColumn(['publish_at', 'status']);
        });
    }
};
