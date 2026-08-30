<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentGatewayRequest;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    protected $repository;

    public function __construct(PaymentGatewayRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage payment gateways']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = \App\Models\PaymentGateway::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // Apply column sorting
        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'desc');
        $allowedSort = ['id', 'name', 'status', 'environment', 'created_at'];

        if (in_array($sortColumn, $allowedSort)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.payment_gateways.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.payment_gateways.create');
    }

    public function store(PaymentGatewayRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('admin.payment-gateways.index')->with('success', 'Payment gateway created successfully.');
    }

    public function show($id)
    {
        $gateway = $this->repository->findWithTrashed($id);
        return view('admin.payment_gateways.show', compact('gateway'));
    }

    public function edit($id)
    {
        $gateway = $this->repository->find($id);
        return view('admin.payment_gateways.edit', compact('gateway'));
    }

    public function update(PaymentGatewayRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('admin.payment-gateways.index')->with('success', 'Payment gateway updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.payment-gateways.index')->with('success', 'Payment gateway soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.payment-gateways.index', ['trashed' => 'true'])->with('success', 'Payment gateway restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.payment-gateways.index', ['trashed' => 'true'])->with('success', 'Payment gateway permanently deleted.');
    }
}
