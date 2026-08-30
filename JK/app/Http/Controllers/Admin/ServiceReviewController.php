<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceReview;
use Illuminate\Http\Request;

class ServiceReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage service reviews', 'activity_log']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        $query = ServiceReview::query()
            ->with(['service:id,title,slug', 'package:id,name,slug']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reviewer_name', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.service_reviews.index', compact('records', 'search', 'statusFilter'));
    }

    public function show($id)
    {
        $review = ServiceReview::with(['service', 'package', 'customer'])->findOrFail($id);

        return view('admin.service_reviews.show', compact('review'));
    }

    public function updateStatus(Request $request, $id)
    {
        $review = ServiceReview::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,active,rejected',
        ]);

        $review->update(['status' => $request->input('status')]);

        return redirect()
            ->route('admin.service-reviews.show', $review->id)
            ->with('success', 'Review status updated.');
    }

    public function destroy($id)
    {
        ServiceReview::findOrFail($id)->delete();

        return redirect()
            ->route('admin.service-reviews.index')
            ->with('success', 'Review deleted.');
    }
}
