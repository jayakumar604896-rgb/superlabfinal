<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\Customer;
use App\Services\BookingCreationService;
use Illuminate\Http\JsonResponse;

class BookingApiController extends Controller
{
    public function __construct(private BookingCreationService $bookingService)
    {
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var Customer|null $customer */
        $customer = $request->attributes->get('customer');

        $booking = $this->bookingService->createFromCheckout($data, $customer);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking submitted successfully!',
            'data' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'customer_id' => $booking->customer_id,
            ],
        ]);
    }
}
