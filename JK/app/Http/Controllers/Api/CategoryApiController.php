<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryApiController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
}
