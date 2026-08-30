<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class TestimonialApiController extends Controller
{
    public function index(): JsonResponse
    {
        $testimonials = Testimonial::where('status', 'active')
            ->get()
            ->map(fn (Testimonial $testimonial) => [
                'id' => $testimonial->id,
                'name' => $testimonial->client_name,
                'rating' => $testimonial->rating,
                'text' => $testimonial->message,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $testimonials,
        ]);
    }
}
