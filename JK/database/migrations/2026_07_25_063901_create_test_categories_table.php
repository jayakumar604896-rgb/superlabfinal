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
        Schema::create('test_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active'); // active, inactive
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_categories');
    }
};
