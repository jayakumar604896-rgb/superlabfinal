<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('reviewer_name');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('message');
            $table->string('status')->default('pending'); // pending, active, rejected
            $table->timestamps();
            $table->softDeletes();

            $table->index(['service_id', 'status']);
            $table->index(['package_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_reviews');
    }
};
