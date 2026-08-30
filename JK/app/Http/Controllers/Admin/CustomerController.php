<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerVital;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage customers', 'activity_log'])
            ->only(['store', 'update', 'destroy', 'restore', 'forceDelete', 'addVital', 'deleteVital', 'uploadReport']);
        $this->middleware(['auth', 'permission:manage customers']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = Customer::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $records = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.customers.index', compact('records', 'search', 'status', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'mobile' => 'required|string|unique:customers,mobile',
            'password' => 'required|string|min:6',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:100',
            'status' => 'required|string',
        ]);

        $data['password'] = Hash::make($data['password']);

        Customer::create($data);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $bookings = Booking::queryForCustomer($customer)
            ->orderBy('id', 'desc')
            ->get();

        $vitals = CustomerVital::where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.customers.show', compact('customer', 'bookings', 'vitals'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $vitals = CustomerVital::where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get();

        $bookings = Booking::queryForCustomer($customer)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.customers.edit', compact('customer', 'vitals', 'bookings'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'mobile' => 'required|string|unique:customers,mobile,' . $id,
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:100',
            'status' => 'required|string',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $customer->update($data);

        return redirect()->route('admin.customers.show', $customer->id)->with('success', 'Customer updated successfully.');
    }

    public function addVital(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'metric_name' => 'required|string|max:255',
            'metric_value' => 'required|string|max:255',
            'unit' => 'nullable|string|max:100',
            'normal_range' => 'nullable|string|max:100',
            'status' => 'required|string|in:Normal,High,Low',
        ]);

        $data['customer_id'] = $customer->id;

        CustomerVital::create($data);

        return redirect()->back()->with('success', 'Lab metric added successfully.');
    }

    public function deleteVital($vitalId)
    {
        $vital = CustomerVital::findOrFail($vitalId);
        $vital->delete();

        return redirect()->back()->with('success', 'Lab metric deleted.');
    }

    public function uploadReport(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $request->validate([
            'report' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // max 10MB
        ]);

        if ($request->hasFile('report')) {
            $path = $request->file('report')->store('reports', 'public');
            $booking->report_file = $path;
            $booking->status = 'completed';
            $booking->save();
        }

        return redirect()->back()->with('success', 'Medical report uploaded successfully and booking marked completed.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()->route('admin.customers.index')->with('success', 'Customer restored successfully.');
    }

    public function forceDelete($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->forceDelete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer permanently deleted.');
    }
}
