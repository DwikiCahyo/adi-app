<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resourcefile', function (Blueprint $table) {
            $table->id();
            $table->string('title'); 
            $table->string('nama_file'); 
            $table->string('file_path');
            $table->longText('content')->nullable(); 
            $table->string('slug', 255)->unique();
            $table->timestamps();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->index('slug', 'resourcefile_slug_idx');
            $table->index('deleted_at', 'resourcefile_deleted_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resourcefile');
    }
};
