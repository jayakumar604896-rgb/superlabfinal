<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentGateway;
use App\Services\BookingCreationService;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class PaymentApiController extends Controller
{
    public function __construct(private BookingCreationService $bookingService)
    {
    }

    public function index(): JsonResponse
    {
        $gateways = PaymentGateway::where('status', 'active')
            ->select('id', 'name', 'slug', 'additional_settings')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $gateways,
        ]);
    }

    public function createOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'gateway' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $service = PaymentGatewayFactory::make($data['gateway']);
            $orderData = $service->createOrder((float) $data['amount'], $request->all());

            return response()->json($orderData);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'gateway' => 'required|string',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'mobile' => 'required|string',
            'age' => 'nullable',
            'gender' => 'nullable|string',
            'address' => 'nullable|string',
            'booking_date' => 'nullable',
            'total_price' => 'required|numeric',
            'subtotal' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable',
            'items.*.name' => 'required_with:items|string',
            'items.*.price' => 'nullable|numeric',
            'mock' => 'nullable|boolean',
        ]);

        try {
            $service = PaymentGatewayFactory::make($data['gateway']);

            if (!$service->verifyPayment($request->all())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment signature verification failed.',
                ], 400);
            }

            /** @var Customer|null $customer */
            $customer = $request->attributes->get('customer');

            $checkoutData = array_merge($data, [
                'payment_method' => 'Online (' . ucfirst($data['gateway']) . ')',
                'payment_status' => 'paid',
            ]);

            $booking = $this->bookingService->createFromCheckout($checkoutData, $customer);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified and booking created!',
                'data' => [
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'customer_id' => $booking->customer_id,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
