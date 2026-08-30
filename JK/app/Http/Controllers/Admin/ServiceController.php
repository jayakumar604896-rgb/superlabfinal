<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    protected ServiceRepositoryInterface $services;
    protected CategoryRepositoryInterface $categories;
    protected FileService $fileService;

    public function __construct(
        ServiceRepositoryInterface $services,
        CategoryRepositoryInterface $categories,
        FileService $fileService
    ) {
        $this->middleware(['auth', 'permission:manage services', 'activity_log']);
        $this->services = $services;
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

        $query = \App\Models\Service::with('category');

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if (in_array($sortBy, ['id', 'title', 'price', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->paginate(10)->withQueryString();
        $categories = $this->categories->all();

        return view('admin.services.index', compact('records', 'categories', 'search', 'sortBy', 'sortOrder', 'categoryFilter', 'statusFilter', 'withTrashed'));
    }

    public function create()
    {
        $categories = $this->categories->all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);
        $data['home_collection_available'] = $request->boolean('home_collection_available');
        $data['popular'] = $request->boolean('popular');

        if (isset($data['test_components'])) {
            $data['test_components'] = array_values(array_filter($data['test_components']));
        }
        if (isset($data['faqs'])) {
            $data['faqs'] = array_values(array_filter($data['faqs'], function($item) {
                return !empty($item['question']) && !empty($item['answer']);
            }));
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/services');
        }

        $this->services->create($data);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function show($id)
    {
        $service = \App\Models\Service::withTrashed()->with('category')->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = $this->services->find($id);
        $categories = $this->categories->all();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $service = $this->services->find($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);
        $data['home_collection_available'] = $request->boolean('home_collection_available');
        $data['popular'] = $request->boolean('popular');

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

        if ($request->hasFile('image')) {
            $this->fileService->delete($service->image);
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/services');
        }

        $this->services->update($id, $data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $this->services->delete($id);
        return redirect()->route('admin.services.index')->with('success', 'Service soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->services->restore($id);
        return redirect()->route('admin.services.index', ['trashed' => 'true'])->with('success', 'Service restored successfully.');
    }

    public function forceDelete($id)
    {
        $service = \App\Models\Service::onlyTrashed()->findOrFail($id);
        $this->fileService->delete($service->image);
        $this->services->forceDelete($id);
        return redirect()->route('admin.services.index', ['trashed' => 'true'])->with('success', 'Service permanently deleted.');
    }
}
