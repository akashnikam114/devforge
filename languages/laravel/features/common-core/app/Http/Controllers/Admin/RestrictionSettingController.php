<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RestrictionSettingService;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;

class RestrictionSettingController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new RestrictionSettingService();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $query = $this->service->fetchRecord($data);

            return DataTables::of($query)
                ->addColumn('action', function ($rec) {
                    return '<ul class="nk-tb-actions gx-1 my-n1">
                        <li class="me-n1">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="' . url('admin/restriction_settings/edit') . '/' . $rec->id . '"><em class="icon ni ni-edit"></em><span>Edit Restriction</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.restriction_settings.all');
    }

    public function edit($id)
    {
        $data = $this->service->fetch($id);
        if ($data) {
            return view('admin.restriction_settings.edit', compact('data'));
        }
        return redirect()->route('admin.restriction_settings')->with('error', 'Restriction not found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'restriction_name' => 'required',
            'is_restriction_enabled' => 'sometimes|boolean',
            'is_button_enabled' => 'sometimes|boolean',
            'title' => 'sometimes|required_if:is_restriction_enabled,1',
            'sub_title' => 'sometimes|required_if:is_restriction_enabled,1',
            'image' => 'sometimes|image|max:2048',
            'url_label' => 'sometimes|required_if:is_button_enabled,1',
            'redirection_url' => 'sometimes|required_if:is_button_enabled,1',
        ], [
            'restriction_name.required' => 'The restriction name field is required.',
            'title.required_if' => 'The title field is required.',
            'sub_title.required_if' => 'The subtitle field is required.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image size must not exceed 2 MB.',
            'url_label.required_if' => 'The URL label field is required.',
            'redirection_url.required_if' => 'The redirection URL field is required.',
        ]);

        $restriction = $this->service->fetch($id);
        $imagePath = $restriction->image;

        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $cleanName = str_replace(' ', '_', $request->restriction_name);
            $filename = $cleanName . "_" . time() . "_" . rand(1111, 9999) . "." . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('Restriction', $filename, 'public');
        }

        $data = $request->only(['restriction_name', 'title', 'sub_title', 'url_label', 'redirection_url']);
        $data['is_restriction_enabled'] = $request->has('is_restriction_enabled');
        $data['is_button_enabled'] = $request->has('is_button_enabled');
        $data['image'] = $imagePath;

        $response = $this->service->update($id, $data);

        if ($response) {
            return redirect()->route('admin.restriction_settings')->with('success', 'Restriction settings updated successfully.');
        }

        return back()->with('error', 'Something went wrong.');
    }
}
