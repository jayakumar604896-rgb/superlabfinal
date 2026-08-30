<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingRequest;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Models\Package;
use App\Models\User;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $repository;

    public function __construct(BookingRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage bookings']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');
        $paymentStatusFilter = $request->query('payment_status', '');

        $query = \App\Models\Booking::query()->with(['package', 'paymentType', 'customer']);

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($paymentStatusFilter)) {
            $query->where('payment_status', $paymentStatusFilter);
        }

        // Apply column sorting
        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'desc');
        $allowedSort = ['id', 'booking_number', 'booking_date', 'status', 'payment_status', 'total_price', 'created_at'];

        if (in_array($sortColumn, $allowedSort)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.bookings.index', compact('records', 'withTrashed', 'search', 'statusFilter', 'paymentStatusFilter'));
    }

    public function create()
    {
        $packages = Package::where('status', 'active')->get();
        $users = User::all();
        $customers = \App\Models\Customer::where('status', 'active')->orderBy('name')->get();
        $paymentTypes = PaymentType::where('status', 'active')->get();
        
        // Generate a temporary booking number
        $bookingNumber = 'BK-' . strtoupper(uniqid());

        return view('admin.bookings.create', compact('packages', 'users', 'customers', 'paymentTypes', 'bookingNumber'));
    }

    public function store(BookingRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    public function show($id)
    {
        $booking = $this->repository->findWithTrashed($id);
        $booking->load(['package', 'user', 'customer', 'paymentType', 'payments.paymentType']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = $this->repository->find($id);
        $packages = Package::where('status', 'active')->get();
        $users = User::all();
        $customers = \App\Models\Customer::where('status', 'active')->orderBy('name')->get();
        $paymentTypes = PaymentType::where('status', 'active')->get();

        return view('admin.bookings.edit', compact('booking', 'packages', 'users', 'customers', 'paymentTypes'));
    }

    public function update(BookingRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.bookings.index', ['trashed' => 'true'])->with('success', 'Booking restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.bookings.index', ['trashed' => 'true'])->with('success', 'Booking permanently deleted.');
    }
}
