<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage roles', 'activity_log']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = Role::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.roles.index', compact('records', 'search', 'withTrashed'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show($id)
    {
        $role = Role::withTrashed()->findOrFail($id);
        return view('admin.roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->input('name')
        ]);

        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting core Super Admin role
        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The Super Admin role cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role soft deleted successfully.');
    }

    public function restore($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->restore();
        return redirect()->route('admin.roles.index', ['trashed' => 'true'])->with('success', 'Role restored successfully.');
    }

    public function forceDelete($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The Super Admin role cannot be deleted.');
        }
        $role->forceDelete();
        return redirect()->route('admin.roles.index', ['trashed' => 'true'])->with('success', 'Role permanently deleted.');
    }
}
