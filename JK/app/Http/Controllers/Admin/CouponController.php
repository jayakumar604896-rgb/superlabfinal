<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Coupon;
use App\Models\Customer;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage coupons']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = Coupon::query()->withCount('customers');

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.coupons.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'mobile', 'email']);

        return view('admin.coupons.create', compact('customers'));
    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        $customerIds = $data['customer_ids'] ?? [];
        unset($data['customer_ids']);

        $coupon = Coupon::create($data);
        $coupon->customers()->sync($customerIds);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function show($id)
    {
        $coupon = Coupon::withTrashed()->with('customers')->findOrFail($id);

        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit($id)
    {
        $coupon = Coupon::with('customers')->findOrFail($id);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'mobile', 'email']);

        return view('admin.coupons.edit', compact('coupon', 'customers'));
    }

    public function update(CouponRequest $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $data = $request->validated();
        $customerIds = $data['customer_ids'] ?? [];
        unset($data['customer_ids']);

        $coupon->update($data);
        $coupon->customers()->sync($customerIds);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy($id)
    {
        Coupon::findOrFail($id)->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon archived successfully.');
    }

    public function restore($id)
    {
        Coupon::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.coupons.index', ['trashed' => 'true'])->with('success', 'Coupon restored successfully.');
    }

    public function forceDelete($id)
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);
        $coupon->customers()->detach();
        $coupon->forceDelete();

        return redirect()->route('admin.coupons.index', ['trashed' => 'true'])->with('success', 'Coupon permanently deleted.');
    }
}
