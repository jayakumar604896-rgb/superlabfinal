<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $categories;
    protected FileService $fileService;

    public function __construct(CategoryRepositoryInterface $categories, FileService $fileService)
    {
        $this->middleware(['auth', 'permission:manage categories', 'activity_log']);
        $this->categories = $categories;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $statusFilter = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = \App\Models\Category::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if (in_array($sortBy, ['id', 'name', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('records', 'search', 'sortBy', 'sortOrder', 'statusFilter', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/categories');
        }

        $this->categories->create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function show($id)
    {
        $category = \App\Models\Category::withTrashed()->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->categories->find($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categories->find($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);

        if ($request->hasFile('image')) {
            // Delete old image
            $this->fileService->delete($category->image);
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/categories');
        }

        $this->categories->update($id, $data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $this->categories->delete($id);
        return redirect()->route('admin.categories.index')->with('success', 'Category soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->categories->restore($id);
        return redirect()->route('admin.categories.index', ['trashed' => 'true'])->with('success', 'Category restored successfully.');
    }

    public function forceDelete($id)
    {
        $category = \App\Models\Category::onlyTrashed()->findOrFail($id);
        $this->fileService->delete($category->image);
        $this->categories->forceDelete($id);
        return redirect()->route('admin.categories.index', ['trashed' => 'true'])->with('success', 'Category permanently deleted.');
    }
}
