<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('categories', 'test_category_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropUnique(['test_category_id']);
            });
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('test_category_id');
            });
        }

        if (Schema::hasColumn('services', 'test_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropUnique(['test_id']);
            });
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('test_id');
            });
        }

        Schema::dropIfExists('tests');
        Schema::dropIfExists('test_categories');
    }

    public function down(): void
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable()->unique();
            $table->string('category_name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable()->unique();
            $table->foreignId('category_id')->constrained('test_categories')->onDelete('cascade');
            $table->string('test_name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('test_category_id')->nullable()->unique()->after('id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('test_id')->nullable()->unique()->after('id');
        });
    }
};
