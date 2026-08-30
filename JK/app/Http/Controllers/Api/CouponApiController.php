<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ValidateCouponRequest;
use App\Models\Customer;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;

class CouponApiController extends Controller
{
    public function __construct(private CouponService $couponService)
    {
    }

    public function validateCode(ValidateCouponRequest $request): JsonResponse
    {
        /** @var Customer|null $customer */
        $customer = $request->attributes->get('customer');

        $result = $this->couponService->validateForCheckout(
            $request->input('code'),
            (int) round($request->input('subtotal')),
            $customer
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully.',
            'data' => $result,
        ]);
    }

    public function available(): JsonResponse
    {
        /** @var Customer|null $customer */
        $customer = request()->attributes->get('customer');

        return response()->json([
            'status' => 'success',
            'data' => $this->couponService->listVisibleForCustomer($customer),
        ]);
    }
}
