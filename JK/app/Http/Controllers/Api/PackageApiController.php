<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;

class PackageApiController extends Controller
{
    public static function formatPackage(Package $pkg): array
    {
        $discount = $pkg->discount_percentage;
        if ($discount === null && $pkg->original_price > 0 && $pkg->offer_price < $pkg->original_price) {
            $discount = (int) round((1 - $pkg->offer_price / $pkg->original_price) * 100);
        }

        return [
            'id' => $pkg->id,
            'name' => $pkg->name,
            'slug' => $pkg->slug,
            'badge' => $pkg->badge,
            'tests_included_count' => $pkg->tests_included_count ?: 90,
            'category' => 'Full Body Health',
            'type' => 'package',
            'offer_price' => $pkg->offer_price,
            'original_price' => $pkg->original_price,
            'discount' => $discount,
            'popular' => ! empty($pkg->badge),
            'hash' => '#/package/' . $pkg->slug,
            'description' => $pkg->description,
            'test_components' => $pkg->test_components,
            'faqs' => $pkg->faqs,
            'fasting_condition' => $pkg->fasting_condition,
        ];
    }

    public function index(): JsonResponse
    {
        $packages = Package::where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn (Package $pkg) => self::formatPackage($pkg));

        return response()->json([
            'status' => 'success',
            'data' => $packages,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $package = Package::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (! $package) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => self::formatPackage($package),
        ]);
    }
}
