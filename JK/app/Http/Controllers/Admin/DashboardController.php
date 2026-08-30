<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Blog;
use App\Models\ContactEnquiry;
use App\Models\Testimonial;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'activity_log']);
    }

    public function index()
    {
        $metrics = [
            'users' => User::count(),
            'categories' => Category::count(),
            'services' => Service::count(),
            'blogs' => Blog::count(),
            'enquiries' => ContactEnquiry::where('status', 'unread')->count(),
            'testimonials' => Testimonial::count(),
        ];

        $recentEnquiries = ContactEnquiry::latest()->take(5)->get();
        $recentLogs = ActivityLog::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('metrics', 'recentEnquiries', 'recentLogs'));
    }
}
