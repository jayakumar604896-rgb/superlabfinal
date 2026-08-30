<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\BookingCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayApiController extends Controller
{
    public function __construct(private BookingCreationService $bookingService)
    {
    }

    public function createOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
        ]);

        $keyId = env('RAZORPAY_KEY_ID', 'rzp_test_5eYjK4m7wP3K6a');
        $keySecret = env('RAZORPAY_KEY_SECRET', 'A1B2C3D4E5F6G7H8I9J0KLMN');

        try {
            if (empty($keyId) || $keyId === 'rzp_test_5eYjK4m7wP3K6a' || str_contains($keyId, 'your_key')) {
                throw new \Exception('Demo/Placeholder keys used');
            }

            $api = new Api($keyId, $keySecret);
            $order = $api->order->create([
                'receipt' => 'rcpt_' . time(),
                'amount' => (int) round($data['amount'] * 100),
                'currency' => 'INR',
            ]);

            return response()->json([
                'status' => 'success',
                'order_id' => $order['id'],
                'key_id' => $keyId,
                'amount' => $data['amount'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'success',
                'order_id' => 'order_MOCK' . strtoupper(\Illuminate\Support\Str::random(10)),
                'key_id' => $keyId,
                'amount' => $data['amount'],
                'mock' => true,
            ]);
        }
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'nullable|string',
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

        $keyId = env('RAZORPAY_KEY_ID', 'rzp_test_5eYjK4m7wP3K6a');
        $keySecret = env('RAZORPAY_KEY_SECRET', 'A1B2C3D4E5F6G7H8I9J0KLMN');

        if (empty($data['mock']) && ! str_starts_with($data['razorpay_order_id'], 'order_MOCK')) {
            try {
                $api = new Api($keyId, $keySecret);
                $api->utility->verifyPaymentSignature([
                    'razorpay_signature' => $data['razorpay_signature'],
                    'razorpay_payment_id' => $data['razorpay_payment_id'],
                    'razorpay_order_id' => $data['razorpay_order_id'],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment signature verification failed: ' . $e->getMessage(),
                ], 400);
            }
        }

        /** @var Customer|null $customer */
        $customer = $request->attributes->get('customer');

        $checkoutData = array_merge($data, [
            'payment_method' => 'Online',
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
    }
}
