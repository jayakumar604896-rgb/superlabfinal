<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('test_category_id')->nullable()->unique()->after('id');
        });

        Schema::table('test_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->unique()->after('id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('test_id')->nullable()->unique()->after('id');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('test_category_id');
        });

        Schema::table('test_categories', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('test_id');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('service_id');
        });
    }
};
