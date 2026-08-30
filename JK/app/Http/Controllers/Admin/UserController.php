<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected UserRepositoryInterface $users;
    protected RoleRepositoryInterface $roles;

    public function __construct(UserRepositoryInterface $users, RoleRepositoryInterface $roles)
    {
        $this->middleware(['auth', 'permission:manage users', 'activity_log']);
        $this->users = $users;
        $this->roles = $roles;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $roleFilter = $request->input('role');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        // Query builder
        $query = \App\Models\User::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->role($roleFilter);
        }

        // Apply sorting
        if (in_array($sortBy, ['id', 'name', 'email', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();
        $roles = $this->roles->all();

        return view('admin.users.index', compact('records', 'roles', 'search', 'sortBy', 'sortOrder', 'roleFilter', 'withTrashed'));
    }

    public function create()
    {
        $roles = $this->roles->all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = \App\Models\User::create($data);
        $user->assignRole($request->input('roles'));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $roles = $this->roles->all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($request->input('roles'));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Prevent self deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User soft deleted successfully.');
    }

    public function restore($id)
    {
        $user = \App\Models\User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index', ['trashed' => 'true'])->with('success', 'User restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = \App\Models\User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('admin.users.index', ['trashed' => 'true'])->with('success', 'User permanently deleted.');
    }
}
