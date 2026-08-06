<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeneralSettingService;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new GeneralSettingService();
    }

    public function edit($id)
    {
        $data = $this->service->fetch($id);
        if ($data) {
            return view('admin.general_settings.edit', compact('data'));
        }
        return back()->with('error', 'General settings not found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'support_phone' => 'nullable|string|max:30',
            'default_language' => 'required|string|max:10',
            'date_format' => 'required|string|max:30',
            'time_format' => 'required|string|max:30',
            'items_per_page' => 'required|integer|min:1|max:500',
        ]);

        $data = $request->only([
            'site_title',
            'support_email',
            'support_phone',
            'default_language',
            'date_format',
            'time_format',
            'items_per_page',
        ]);

        $response = $this->service->update($id, $data);

        if ($response) {
            return redirect()->back()->with('success', 'General settings updated successfully.');
        }
        return back()->with('error', 'Something went wrong');
    }
}
