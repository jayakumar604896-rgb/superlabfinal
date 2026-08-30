<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryRequest;
use App\Repositories\Contracts\GalleryRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    protected GalleryRepositoryInterface $gallery;
    protected CategoryRepositoryInterface $categories;
    protected FileService $fileService;

    public function __construct(
        GalleryRepositoryInterface $gallery,
        CategoryRepositoryInterface $categories,
        FileService $fileService
    ) {
        $this->middleware(['auth', 'permission:manage gallery', 'activity_log']);
        $this->gallery = $gallery;
        $this->categories = $categories;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id');
        $statusFilter = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = \App\Models\Gallery::with('category');

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $categories = $this->categories->all();

        return view('admin.gallery.index', compact('records', 'categories', 'search', 'categoryFilter', 'statusFilter', 'withTrashed'));
    }

    public function create()
    {
        $categories = $this->categories->all();
        return view('admin.gallery.create', compact('categories'));
    }

    public function store(StoreGalleryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/gallery');
        }

        $this->gallery->create($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item uploaded successfully.');
    }

    public function show($id)
    {
        $gallery = $this->gallery->findWithTrashed($id);
        $gallery->load('category');

        return view('admin.gallery.show', compact('gallery'));
    }

    public function edit($id)
    {
        $gallery = $this->gallery->find($id);
        $categories = $this->categories->all();
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    public function update(StoreGalleryRequest $request, $id)
    {
        $gallery = $this->gallery->find($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->fileService->delete($gallery->image);
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/gallery');
        }

        $this->gallery->update($id, $data);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item updated successfully.');
    }

    public function destroy($id)
    {
        $this->gallery->delete($id);
        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->gallery->restore($id);
        return redirect()->route('admin.gallery.index', ['trashed' => 'true'])->with('success', 'Gallery item restored successfully.');
    }

    public function forceDelete($id)
    {
        $gallery = \App\Models\Gallery::onlyTrashed()->findOrFail($id);
        $this->fileService->delete($gallery->image);
        $this->gallery->forceDelete($id);
        return redirect()->route('admin.gallery.index', ['trashed' => 'true'])->with('success', 'Gallery item permanently deleted.');
    }
}
