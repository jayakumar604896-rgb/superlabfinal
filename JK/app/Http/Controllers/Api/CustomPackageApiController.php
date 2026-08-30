<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomPackageRequest;
use App\Models\CustomPackage;
use App\Models\Customer;
use App\Services\CustomPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomPackageApiController extends Controller
{
    public function __construct(private CustomPackageService $customPackageService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var Customer|null $customer */
        $customer = $request->attributes->get('customer');

        if (! $customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required.',
            ], 401);
        }

        $packages = CustomPackage::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CustomPackage $package) => $this->customPackageService->formatForApi($package));

        return response()->json([
            'status' => 'success',
            'data' => $packages,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $package = CustomPackage::where('status', 'active')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $this->customPackageService->formatForApi($package),
        ]);
    }

    public function store(StoreCustomPackageRequest $request): JsonResponse
    {
        /** @var Customer|null $customer */
        $customer = $request->attributes->get('customer');

        $package = $this->customPackageService->createFromRequestedItems(
            $request->validated('items'),
            $customer,
            $request->validated('name')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Custom package saved.',
            'data' => $this->customPackageService->formatForApi($package),
        ], 201);
    }
}
