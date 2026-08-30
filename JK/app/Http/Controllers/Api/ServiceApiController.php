<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public static function formatService(Service $service): array
    {
        return [
            'id' => $service->id,
            'name' => $service->title,
            'title' => $service->title,
            'slug' => $service->slug,
            'category' => $service->category ? $service->category->name : 'General',
            'category_id' => $service->category_id,
            'type' => 'test',
            'price' => $service->price,
            'original_price' => $service->original_price,
            'short_description' => $service->short_description,
            'description' => $service->description,
            'image' => $service->image ? asset($service->image) : null,
            'popular' => (bool) $service->popular,
            'home_collection_available' => (bool) $service->home_collection_available,
            'hash' => '#/test/' . $service->slug,
            'fasting_condition' => $service->fasting_condition,
            'test_components' => $service->test_components,
            'faqs' => $service->faqs,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = Service::with('category')->where('status', 'active');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('title')->get()->map(fn (Service $service) => self::formatService($service));

        return response()->json([
            'status' => 'success',
            'data' => $services,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::with('category')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (! $service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => self::formatService($service),
        ]);
    }
}
