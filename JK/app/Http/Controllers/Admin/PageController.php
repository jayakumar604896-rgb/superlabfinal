<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    protected PageRepositoryInterface $pages;

    public function __construct(PageRepositoryInterface $pages)
    {
        $this->middleware(['auth', 'permission:manage pages', 'activity_log']);
        $this->pages = $pages;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $statusFilter = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = \App\Models\Page::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if (in_array($sortBy, ['id', 'title', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.pages.index', compact('records', 'search', 'sortBy', 'sortOrder', 'statusFilter', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);

        $this->pages->create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function show($id)
    {
        $page = \App\Models\Page::withTrashed()->findOrFail($id);
        return view('admin.pages.show', compact('page'));
    }

    public function edit($id)
    {
        $page = $this->pages->find($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, $id)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);

        $this->pages->update($id, $data);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $this->pages->delete($id);
        return redirect()->route('admin.pages.index')->with('success', 'Page soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->pages->restore($id);
        return redirect()->route('admin.pages.index', ['trashed' => 'true'])->with('success', 'Page restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->pages->forceDelete($id);
        return redirect()->route('admin.pages.index', ['trashed' => 'true'])->with('success', 'Page permanently deleted.');
    }
}
