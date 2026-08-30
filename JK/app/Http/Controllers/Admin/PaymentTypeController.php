<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentTypeRequest;
use App\Repositories\Contracts\PaymentTypeRepositoryInterface;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    protected $repository;

    public function __construct(PaymentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage payment types']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = \App\Models\PaymentType::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // Apply column sorting
        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'desc');
        $allowedSort = ['id', 'name', 'status', 'created_at'];

        if (in_array($sortColumn, $allowedSort)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.payment_types.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.payment_types.create');
    }

    public function store(PaymentTypeRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('admin.payment-types.index')->with('success', 'Payment type created successfully.');
    }

    public function show($id)
    {
        $paymentType = $this->repository->findWithTrashed($id);
        return view('admin.payment_types.show', compact('paymentType'));
    }

    public function edit($id)
    {
        $paymentType = $this->repository->find($id);
        return view('admin.payment_types.edit', compact('paymentType'));
    }

    public function update(PaymentTypeRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('admin.payment-types.index')->with('success', 'Payment type updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.payment-types.index')->with('success', 'Payment type soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.payment-types.index', ['trashed' => 'true'])->with('success', 'Payment type restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.payment-types.index', ['trashed' => 'true'])->with('success', 'Payment type permanently deleted.');
    }
}
