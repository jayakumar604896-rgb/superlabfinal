<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected $repository;

    public function __construct(PackageRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage packages']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = \App\Models\Package::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('badge', 'like', "%{$search}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // Column sort
        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'desc');
        $allowedSort = ['id', 'name', 'status', 'offer_price', 'created_at'];

        if (in_array($sortColumn, $allowedSort)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.packages.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(PackageRequest $request)
    {
        // Sanitize arrays to filter empty inputs
        $data = $request->validated();
        if (isset($data['test_components'])) {
            $data['test_components'] = array_values(array_filter($data['test_components']));
        }
        if (isset($data['faqs'])) {
            $data['faqs'] = array_values(array_filter($data['faqs'], function($item) {
                return !empty($item['question']) && !empty($item['answer']);
            }));
        }

        $this->repository->create($data);
        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully.');
    }

    public function show($id)
    {
        $package = $this->repository->findWithTrashed($id);
        return view('admin.packages.show', compact('package'));
    }

    public function edit($id)
    {
        $package = $this->repository->find($id);
        return view('admin.packages.edit', compact('package'));
    }

    public function update(PackageRequest $request, $id)
    {
        $data = $request->validated();
        if (isset($data['test_components'])) {
            $data['test_components'] = array_values(array_filter($data['test_components']));
        } else {
            $data['test_components'] = [];
        }
        
        if (isset($data['faqs'])) {
            $data['faqs'] = array_values(array_filter($data['faqs'], function($item) {
                return !empty($item['question']) && !empty($item['answer']);
            }));
        } else {
            $data['faqs'] = [];
        }

        $this->repository->update($id, $data);
        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.packages.index')->with('success', 'Package soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.packages.index', ['trashed' => 'true'])->with('success', 'Package restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.packages.index', ['trashed' => 'true'])->with('success', 'Package permanently deleted.');
    }
}
