<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (! $bearerToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required.',
            ], 401);
        }

        $customer = Customer::where('api_token', hash('sha256', $bearerToken))
            ->where('status', 'active')
            ->first();

        if (! $customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired session.',
            ], 401);
        }

        $request->attributes->set('customer', $customer);

        return $next($request);
    }
}
