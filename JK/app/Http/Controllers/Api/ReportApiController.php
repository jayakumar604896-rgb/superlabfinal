<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LookupGuestReportRequest;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ReportApiController extends Controller
{
    public function lookup(LookupGuestReportRequest $request): JsonResponse
    {
        $mobile = $request->input('mobile');

        $booking = Booking::query()
            ->whereNotNull('report_file')
            ->where('report_file', '!=', '')
            ->where(function ($query) use ($mobile) {
                $query->where('customer_phone', $mobile)
                    ->orWhere('customer_phone', '+91' . $mobile)
                    ->orWhere('customer_phone', '91' . $mobile);
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if (! $booking) {
            throw ValidationException::withMessages([
                'mobile' => 'No report is available for this mobile number yet.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Report found.',
            'data' => [
                'booking_number' => $booking->booking_number,
                'patient_name' => $booking->customer_name,
                'booking_date' => $booking->booking_date?->format('d M Y'),
                'report_url' => asset('storage/' . $booking->report_file),
                'file_name' => basename($booking->report_file),
            ],
        ]);
    }
}
