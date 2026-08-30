<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ContactEnquiryRepositoryInterface;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    protected ContactEnquiryRepositoryInterface $enquiries;

    public function __construct(ContactEnquiryRepositoryInterface $enquiries)
    {
        $this->middleware(['auth', 'permission:manage enquiries', 'activity_log']);
        $this->enquiries = $enquiries;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $withTrashed = $request->input('trashed', 'false') === 'true';

        $query = \App\Models\ContactEnquiry::query();

        if ($withTrashed) {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.enquiries.index', compact('records', 'search', 'statusFilter', 'withTrashed'));
    }

    public function show($id)
    {
        $enquiry = \App\Models\ContactEnquiry::withTrashed()->findOrFail($id);

        if ($enquiry->status === 'unread' && !$enquiry->trashed()) {
            $enquiry->update(['status' => 'read']);
        }

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function destroy($id)
    {
        $this->enquiries->delete($id);
        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry soft deleted successfully.');
    }

    public function restore($id)
    {
        $this->enquiries->restore($id);
        return redirect()->route('admin.enquiries.index', ['trashed' => 'true'])->with('success', 'Enquiry restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->enquiries->forceDelete($id);
        return redirect()->route('admin.enquiries.index', ['trashed' => 'true'])->with('success', 'Enquiry permanently deleted.');
    }
}
