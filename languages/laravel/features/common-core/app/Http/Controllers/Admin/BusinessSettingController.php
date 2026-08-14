<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BusinessSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new BusinessSettingService();
    }

    public function edit()
    {
        $data = $this->service->fetchRecord();
        if ($data) {
            return view('admin.business_settings.edit', compact('data'));
        }
        return back()->with('error', 'Business settings not found');
    }

    public function update(Request $request)
    {
        $rules = [];
        foreach ($request->except('_token') as $key => $value) {
            if ($key === 'app_logo') {
                $rules[$key] = 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048';
            } elseif (is_bool($value) || $value === "true" || $value === "false") {
                $rules[$key] = 'nullable|in:true,false';
            } else {
                $rules[$key] = 'required';
            }
        }

        $validatedData = $request->validate($rules);

        if ($request->hasFile('app_logo')) {
            $currentLogo = $this->service->getValue('app_logo');
            if ($currentLogo && !str_starts_with($currentLogo, 'assets/') && Storage::disk('public')->exists($currentLogo)) {
                Storage::disk('public')->delete($currentLogo);
            }

            $validatedData['app_logo'] = $request->file('app_logo')->store('business-settings', 'public');
        }

        $response = $this->service->update($validatedData);

        if ($response) {
            return redirect()->back()->with('success', 'Business settings updated successfully.');
        }

        return back()->with('error', 'Something went wrong');
    }
}
