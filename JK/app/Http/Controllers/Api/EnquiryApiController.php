<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEnquiryRequest;
use App\Models\ContactEnquiry;
use Illuminate\Http\JsonResponse;

class EnquiryApiController extends Controller
{
    public function store(StoreEnquiryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $enquiry = ContactEnquiry::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'],
            'subject' => $data['subject'] ?? 'Callback Request',
            'message' => $data['message'] ?? 'Customer requested a callback.',
            'status' => 'unread',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Enquiry submitted successfully! Our representative will call you back.',
            'data' => [
                'id' => $enquiry->id,
            ],
        ]);
    }
}
