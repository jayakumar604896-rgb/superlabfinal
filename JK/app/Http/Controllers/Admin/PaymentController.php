<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Models\Booking;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $repository;

    public function __construct(PaymentRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage payments']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = \App\Models\Payment::query()->with(['booking', 'paymentType']);

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('booking', function($bq) use ($search) {
                      $bq->where('booking_number', 'like', "%{$search}%")
                         ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // Apply column sorting
        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'desc');
        $allowedSort = ['id', 'transaction_id', 'amount', 'status', 'payment_date', 'created_at'];

        if (in_array($sortColumn, $allowedSort)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.payments.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create(Request $request)
    {
        $bookings = Booking::where('status', '!=', 'cancelled')->get();
        $paymentTypes = PaymentType::where('status', 'active')->get();
        $selectedBookingId = $request->query('booking_id');

        return view('admin.payments.create', compact('bookings', 'paymentTypes', 'selectedBookingId'));
    }

    public function store(PaymentRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('admin.payments.index')->with('success', 'Payment record created successfully.');
    }

    public function show($id)
    {
        $payment = $this->repository->findWithTrashed($id);
        $payment->load(['booking', 'paymentType']);
        return view('admin.payments.show', compact('payment'));
    }

    public function edit($id)
    {
        $payment = $this->repository->find($id);
        $bookings = Booking::all();
        $paymentTypes = PaymentType::where('status', 'active')->get();

        return view('admin.payments.edit', compact('payment', 'bookings', 'paymentTypes'));
    }

    public function update(PaymentRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('admin.payments.index')->with('success', 'Payment record updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.payments.index')->with('success', 'Payment record soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.payments.index', ['trashed' => 'true'])->with('success', 'Payment record restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.payments.index', ['trashed' => 'true'])->with('success', 'Payment record permanently deleted.');
    }
}
