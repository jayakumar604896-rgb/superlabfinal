<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    protected TestimonialRepositoryInterface $testimonials;
    protected FileService $fileService;

    public function __construct(TestimonialRepositoryInterface $testimonials, FileService $fileService)
    {
        $this->middleware(['auth', 'permission:manage testimonials', 'activity_log']);
        $this->testimonials = $testimonials;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = \App\Models\Testimonial::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.testimonials.index', compact('records', 'search', 'statusFilter', 'withTrashed'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(StoreTestimonialRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('client_image')) {
            $data['client_image'] = $this->fileService->upload($request->file('client_image'), 'uploads/testimonials');
        }

        $this->testimonials->create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function show($id)
    {
        $testimonial = \App\Models\Testimonial::withTrashed()->findOrFail($id);
        return view('admin.testimonials.show', compact('testimonial'));
    }

    public function edit($id)
    {
        $testimonial = $this->testimonials->find($id);
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(UpdateTestimonialRequest $request, $id)
    {
        $testimonial = $this->testimonials->find($id);
        $data = $request->validated();

        if ($request->hasFile('client_image')) {
            $this->fileService->delete($testimonial->client_image);
            $data['client_image'] = $this->fileService->upload($request->file('client_image'), 'uploads/testimonials');
        }

        $this->testimonials->update($id, $data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy($id)
    {
        $this->testimonials->delete($id);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->testimonials->restore($id);
        return redirect()->route('admin.testimonials.index', ['trashed' => 'true'])->with('success', 'Testimonial restored successfully.');
    }

    public function forceDelete($id)
    {
        $testimonial = \App\Models\Testimonial::onlyTrashed()->findOrFail($id);
        $this->fileService->delete($testimonial->client_image);
        $this->testimonials->forceDelete($id);
        return redirect()->route('admin.testimonials.index', ['trashed' => 'true'])->with('success', 'Testimonial permanently deleted.');
    }
}
