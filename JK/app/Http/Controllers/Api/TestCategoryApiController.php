<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class TestCategoryApiController extends Controller
{
    /**
     * Unified catalog: reads from categories table (same source as CRM + /api/v1/categories).
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'image'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'category_name' => $category->name,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->image,
                'icon' => null,
                'sort_order' => 0,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    /**
     * Unified catalog: tests for a category come from services table.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (! $category && $slug === 'all') {
            $category = Category::where('status', 'active')->orderBy('name')->first();
        }

        if (! $category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        $tests = Service::where('category_id', $category->id)
            ->where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'category_id', 'title', 'slug', 'short_description', 'price', 'original_price', 'status'])
            ->map(fn (Service $service) => [
                'id' => $service->id,
                'category_id' => $service->category_id,
                'test_name' => $service->title,
                'name' => $service->title,
                'slug' => $service->slug,
                'short_description' => $service->short_description,
                'price' => $service->price,
                'original_price' => $service->original_price,
                'status' => $service->status,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'category_name' => $category->name,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->image,
                    'icon' => null,
                ],
                'tests' => $tests,
            ],
        ]);
    }
}
