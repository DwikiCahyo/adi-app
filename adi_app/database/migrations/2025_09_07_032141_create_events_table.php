<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->text('agenda');
            $table->text('title');
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

            $table->index('slug', 'events_slug_idx');
            $table->index('deleted_at', 'events_deleted_at_idx');     
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
