<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterLocationRequest;
use App\Repositories\Contracts\FooterLocationRepositoryInterface;
use App\Models\FooterLocation;
use Illuminate\Http\Request;

class FooterLocationController extends Controller
{
    protected $repository;

    public function __construct(FooterLocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth', 'permission:manage footer locations']);
        $this->middleware('activity_log')->only(['store', 'update', 'destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $withTrashed = $request->query('trashed') === 'true';
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', '');

        $query = FooterLocation::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where('location_name', 'like', "%{$search}%");
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'desc');
        $allowedSort = ['id', 'location_name', 'status', 'created_at'];

        if (in_array($sortColumn, $allowedSort)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(15)->withQueryString();

        return view('admin.footer_locations.index', compact('records', 'withTrashed', 'search', 'statusFilter'));
    }

    public function create()
    {
        return view('admin.footer_locations.create');
    }

    public function store(FooterLocationRequest $request)
    {
        $this->repository->create($request->validated());
        return redirect()->route('admin.footer-locations.index')->with('success', 'Footer location created successfully.');
    }

    public function show($id)
    {
        $location = $this->repository->findWithTrashed($id);
        return view('admin.footer_locations.show', compact('location'));
    }

    public function edit($id)
    {
        $location = $this->repository->find($id);
        return view('admin.footer_locations.edit', compact('location'));
    }

    public function update(FooterLocationRequest $request, $id)
    {
        $this->repository->update($id, $request->validated());
        return redirect()->route('admin.footer-locations.index')->with('success', 'Footer location updated successfully.');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return redirect()->route('admin.footer-locations.index')->with('success', 'Footer location soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->repository->restore($id);
        return redirect()->route('admin.footer-locations.index', ['trashed' => 'true'])->with('success', 'Footer location restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return redirect()->route('admin.footer-locations.index', ['trashed' => 'true'])->with('success', 'Footer location permanently deleted.');
    }
}
