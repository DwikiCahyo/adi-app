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
        Schema::create('news', function (Blueprint $table) {

            $table->id();
            $table->text('title');
            $table->text('content');
            $table->text('url')->nullable();
            $table->string('slug' , 255)->unique();
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

            $table->index('slug', 'news_slug_idx');
            $table->index('deleted_at', 'news_deleted_at_idx');      
            
                  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
