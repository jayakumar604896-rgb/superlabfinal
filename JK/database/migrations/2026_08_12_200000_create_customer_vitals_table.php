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
        Schema::create('customer_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('metric_name'); // e.g. Haemoglobin, Blood Sugar
            $table->string('metric_value'); // e.g. 14.2, 92
            $table->string('unit')->nullable(); // e.g. g/dL, mg/dL
            $table->string('normal_range')->nullable(); // e.g. 13.5 - 17.5
            $table->string('status')->default('Normal'); // Normal, High, Low
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_vitals');
    }
};
