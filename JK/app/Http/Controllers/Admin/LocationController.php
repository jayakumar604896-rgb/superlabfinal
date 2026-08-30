<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\LocationRequest;
use App\Repositories\Contracts\LocationRepositoryInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $repository;

    public function __construct(LocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage locations']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = \App\Models\Location::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
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

        return view('admin.locations.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(LocationRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('admin.locations.index')->with('success', 'Location created successfully.');
    }

    public function show($id)
    {
        $location = $this->repository->findWithTrashed($id);
        return view('admin.locations.show', compact('location'));
    }

    public function edit($id)
    {
        $location = $this->repository->find($id);
        return view('admin.locations.edit', compact('location'));
    }

    public function update(LocationRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.locations.index')->with('success', 'Location soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.locations.index', ['trashed' => 'true'])->with('success', 'Location restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.locations.index', ['trashed' => 'true'])->with('success', 'Location permanently deleted.');
    }
}
