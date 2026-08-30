<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreServiceReviewRequest;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceReviewApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceReview::query()
            ->with(['service:id,title,slug', 'package:id,name,slug'])
            ->where('status', 'active');

        if ($request->filled('service_id')) {
            $query->where('service_id', (int) $request->input('service_id'));
        } elseif ($request->filled('service_slug')) {
            $service = Service::where('slug', $request->input('service_slug'))->first();
            if (! $service) {
                return response()->json(['status' => 'success', 'data' => []]);
            }
            $query->where('service_id', $service->id);
        } elseif ($request->filled('package_id')) {
            $query->where('package_id', (int) $request->input('package_id'));
        } elseif ($request->filled('package_slug')) {
            $package = Package::where('slug', $request->input('package_slug'))->first();
            if (! $package) {
                return response()->json(['status' => 'success', 'data' => []]);
            }
            $query->where('package_id', $package->id);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'service_id, service_slug, package_id, or package_slug is required.',
            ], 422);
        }

        $reviews = $query->orderByDesc('created_at')->get()->map(fn (ServiceReview $review) => [
            'id' => $review->id,
            'name' => $review->reviewer_name,
            'rating' => $review->rating,
            'text' => $review->message,
            'created_at' => $review->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ]);
    }

    public function store(StoreServiceReviewRequest $request): JsonResponse
    {
        $data = $request->validated();
        /** @var Customer|null $customer */
        $customer = $request->attributes->get('customer');

        $serviceId = $data['service_id'] ?? null;
        $packageId = $data['package_id'] ?? null;

        if (! $serviceId && ! empty($data['service_slug'])) {
            $service = Service::where('slug', $data['service_slug'])->firstOrFail();
            $serviceId = $service->id;
        }

        if (! $packageId && ! empty($data['package_slug'])) {
            $package = Package::where('slug', $data['package_slug'])->firstOrFail();
            $packageId = $package->id;
        }

        $review = ServiceReview::create([
            'service_id' => $serviceId,
            'package_id' => $packageId,
            'customer_id' => $customer?->id,
            'reviewer_name' => $data['reviewer_name'],
            'rating' => (int) $data['rating'],
            'message' => $data['message'],
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you! Your review has been submitted and will appear after approval.',
            'data' => [
                'id' => $review->id,
                'status' => $review->status,
            ],
        ], 201);
    }
}
