<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuthenticateCustomerApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if ($bearerToken) {
            $customer = Customer::where('api_token', hash('sha256', $bearerToken))
                ->where('status', 'active')
                ->first();

            if ($customer) {
                $request->attributes->set('customer', $customer);
            }
        }

        return $next($request);
    }
}
