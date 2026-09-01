<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterLocation;
use Illuminate\Http\JsonResponse;

class FooterLocationApiController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = FooterLocation::where('status', 'active')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (FooterLocation $location) => [
                'id' => $location->id,
                'location_name' => $location->location_name,
                'map_link' => $location->map_link,
                'status' => $location->status,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $locations,
        ]);
    }
}
