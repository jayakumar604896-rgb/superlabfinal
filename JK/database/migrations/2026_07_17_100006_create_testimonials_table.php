<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_designation')->nullable();
            $table->text('message');
            $table->integer('rating')->default(5);
            $table->string('client_image')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
