<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginCustomerRequest;
use App\Http\Requests\Api\RegisterCustomerRequest;
use App\Http\Requests\Api\UpdateCustomerProfileRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\CustomerVital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerApiController extends Controller
{
    public static function formatCustomerData(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'mobile' => $customer->mobile,
            'age' => $customer->age,
            'gender' => $customer->gender,
            'blood_group' => $customer->blood_group,
            'address' => $customer->address,
            'emergency_contact_name' => $customer->emergency_contact_name,
            'emergency_contact_phone' => $customer->emergency_contact_phone,
        ];
    }

    public static function formatCustomerProfile(Customer $customer): array
    {
        return array_merge(self::formatCustomerData($customer), [
            'member_since' => $customer->created_at?->format('F Y') ?? null,
            'member_since_iso' => $customer->created_at?->toDateString(),
        ]);
    }

    public static function issueToken(Customer $customer): string
    {
        $plainToken = Str::random(64);
        $customer->api_token = hash('sha256', $plainToken);
        $customer->save();

        return $plainToken;
    }

    public static function authResponse(Customer $customer, string $message): JsonResponse
    {
        $token = self::issueToken($customer);

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => array_merge(self::formatCustomerData($customer), [
                'token' => $token,
            ]),
        ]);
    }

    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();

        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'age' => $data['age'] ?? null,
            'gender' => $data['gender'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => 'active',
        ]);

        return self::authResponse($customer, 'Registration successful! Welcome to Super Lab.');
    }

    public function login(LoginCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $query = Customer::query();

        if (! empty($data['mobile'])) {
            $query->where('mobile', $data['mobile']);
        } elseif (! empty($data['email'])) {
            $query->where('email', $data['email']);
        }

        $customer = $query->first();

        if (! $customer || ! Hash::check($data['password'], $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid mobile number or password.',
            ], 401);
        }

        return self::authResponse($customer, 'Login successful!');
    }

    public function profileData(Request $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');

        $vitals = CustomerVital::where('customer_id', $customer->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->metric_name,
                'value' => $v->metric_value,
                'unit' => $v->unit ?: '',
                'range' => $v->normal_range ?: '',
                'status' => $v->status,
                'color' => $v->status === 'Normal' ? '#10b981' : ($v->status === 'High' ? '#ef4444' : '#f59e0b'),
            ]);

        $bookings = Booking::queryForCustomer($customer)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->booking_number,
                'testName' => $b->lineItemsLabel(),
                'items' => $b->lineItemsForDisplay(),
                'date' => $b->booking_date ? $b->booking_date->format('F d, Y') : 'N/A',
                'amount' => '₹' . number_format($b->total_price),
                'status' => ucfirst($b->status),
                'badgeColor' => $b->status === 'completed' ? '#10b981' : '#f59e0b',
                'description' => $b->notes ?: 'Home sample collection & report verification.',
                'canDownload' => ! empty($b->report_file),
                'reportUrl' => $b->report_file ? asset('storage/' . $b->report_file) : null,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'customer' => self::formatCustomerProfile($customer),
                'vitals' => $vitals,
                'bookings' => $bookings,
            ],
        ]);
    }

    public function updateProfile(UpdateCustomerProfileRequest $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        $customer->update($request->validated());
        $customer->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => self::formatCustomerProfile($customer),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');
        $customer->api_token = null;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }
}
