<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\FileService;

class SettingController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->middleware(['auth', 'permission:manage settings', 'activity_log']);
        $this->fileService = $fileService;
    }

    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if (! $setting) {
                continue;
            }

            if ($setting->type === 'file') {
                if ($request->hasFile($key)) {
                    $this->fileService->delete($setting->value);
                    $path = $this->fileService->upload($request->file($key), 'uploads/settings');
                    $setting->update(['value' => $path]);
                }
                continue;
            }

            $setting->update(['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
