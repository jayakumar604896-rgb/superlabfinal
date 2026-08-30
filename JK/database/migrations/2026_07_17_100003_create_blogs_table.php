<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('image')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, published
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
