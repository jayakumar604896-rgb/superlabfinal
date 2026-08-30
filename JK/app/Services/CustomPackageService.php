<?php

namespace App\Services;

use App\Models\CustomPackage;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CustomPackageService
{
    public static function discountPercentageForSubtotal(int $subtotal): int
    {
        if ($subtotal >= 4000) {
            return 30;
        }
        if ($subtotal >= 3000) {
            return 25;
        }
        if ($subtotal >= 2000) {
            return 20;
        }
        if ($subtotal >= 1500) {
            return 10;
        }

        return 0;
    }

    /**
     * @param  array<int, array{id?: int|string}>  $requestedItems
     */
    public function createFromRequestedItems(array $requestedItems, ?Customer $customer = null, ?string $name = null): CustomPackage
    {
        $serviceIds = collect($requestedItems)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($serviceIds->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => ['Select at least one test for your package.'],
            ]);
        }

        /** @var Collection<int, Service> $services */
        $services = Service::query()
            ->with('category:id,name')
            ->whereIn('id', $serviceIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        if ($services->count() !== $serviceIds->count()) {
            throw ValidationException::withMessages([
                'items' => ['One or more selected tests are no longer available.'],
            ]);
        }

        $items = $serviceIds->map(function (int $serviceId) use ($services) {
            $service = $services->get($serviceId);

            return [
                'id' => $service->id,
                'name' => $service->title,
                'price' => (int) round($service->price),
                'category' => $service->category?->name,
            ];
        })->values()->all();

        $subtotal = (int) array_sum(array_column($items, 'price'));
        $discountPercentage = self::discountPercentageForSubtotal($subtotal);
        $discountAmount = (int) round($subtotal * $discountPercentage / 100);
        $totalPrice = max(0, $subtotal - $discountAmount);
        $testCount = count($items);

        return CustomPackage::create([
            'customer_id' => $customer?->id,
            'name' => $name ?: "Custom Package ({$testCount} Tests)",
            'items' => $items,
            'subtotal_amount' => $subtotal,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
            'status' => 'active',
        ]);
    }

    public function formatForApi(CustomPackage $package): array
    {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'items' => $package->items,
            'subtotal_amount' => $package->subtotal_amount,
            'discount_percentage' => $package->discount_percentage,
            'discount_amount' => $package->discount_amount,
            'total_price' => $package->total_price,
            'test_count' => count($package->items ?? []),
            'created_at' => $package->created_at?->toIso8601String(),
        ];
    }
}
