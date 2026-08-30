<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    protected BlogRepositoryInterface $blogs;
    protected CategoryRepositoryInterface $categories;
    protected FileService $fileService;

    public function __construct(
        BlogRepositoryInterface $blogs,
        CategoryRepositoryInterface $categories,
        FileService $fileService
    ) {
        $this->middleware(['auth', 'permission:manage blogs', 'activity_log']);
        $this->blogs = $blogs;
        $this->categories = $categories;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $categoryFilter = $request->input('category_id');
        $statusFilter = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = \App\Models\Blog::with(['category', 'author']);

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
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
        $categories = $this->categories->all();

        return view('admin.blogs.index', compact('records', 'categories', 'search', 'sortBy', 'sortOrder', 'categoryFilter', 'statusFilter', 'withTrashed'));
    }

    public function create()
    {
        $categories = $this->categories->all();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(StoreBlogRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);
        $data['author_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/blogs');
        }

        $this->blogs->create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    public function show($id)
    {
        $blog = \App\Models\Blog::withTrashed()->with(['category', 'author'])->findOrFail($id);
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit($id)
    {
        $blog = $this->blogs->find($id);
        $categories = $this->categories->all();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(UpdateBlogRequest $request, $id)
    {
        $blog = $this->blogs->find($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);

        if ($request->hasFile('image')) {
            $this->fileService->delete($blog->image);
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/blogs');
        }

        $this->blogs->update($id, $data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy($id)
    {
        $this->blogs->delete($id);
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->blogs->restore($id);
        return redirect()->route('admin.blogs.index', ['trashed' => 'true'])->with('success', 'Blog post restored successfully.');
    }

    public function forceDelete($id)
    {
        $blog = \App\Models\Blog::onlyTrashed()->findOrFail($id);
        $this->fileService->delete($blog->image);
        $this->blogs->forceDelete($id);
        return redirect()->route('admin.blogs.index', ['trashed' => 'true'])->with('success', 'Blog post permanently deleted.');
    }
}
