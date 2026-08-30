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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('fasting_condition')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('fasting_condition')->nullable();
            $table->json('test_components')->nullable();
            $table->json('faqs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('fasting_condition');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['fasting_condition', 'test_components', 'faqs']);
        });
    }
};
