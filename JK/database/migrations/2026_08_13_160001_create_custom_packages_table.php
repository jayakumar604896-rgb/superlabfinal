<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('name');
            $table->json('items');
            $table->unsignedInteger('subtotal_amount')->default(0);
            $table->unsignedTinyInteger('discount_percentage')->default(0);
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('total_price')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_packages');
    }
};
