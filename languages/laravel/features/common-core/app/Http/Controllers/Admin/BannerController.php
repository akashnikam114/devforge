<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new BannerService();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $query = $this->service->fetchRecord($data);

            return DataTables::of($query)
                ->addColumn('is_active', function ($rec) {
                    if ($rec->is_active == 1) {
                        return '<div><span class="tb-status text-success" style="cursor:pointer" onclick="changeStatus(' . $rec->id . ',0)">Active</span></div>';
                    } else {
                        return '<div><span class="tb-status text-danger" style="cursor:pointer" onclick="changeStatus(' . $rec->id . ',1)">Inactive</span></div>';
                    }
                })
                ->addColumn('action', function ($rec) {
                    return '<ul class="nk-tb-actions gx-1 my-n1">
                        <li class="me-n1">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="' . url('admin/banners/edit') . '/' . $rec->id . '"><em class="icon ni ni-edit"></em><span>Edit Banner</span></a></li>
                                        <li><a href="javascript:void(0)" onclick="deleteRecord(' . $rec->id . ')"><em class="icon ni ni-trash"></em><span>Delete Banner</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>';
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
        return view('admin.banners.all');
    }

    public function create()
    {
        return view('admin.banners.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'link_url' => 'nullable|url',
        ]);

        $imagePath = NULL;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = "Banner_" . time() . "_" . rand(1111, 9999) . "." . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('Banners', $filename, 'public');
        }

        $data = $request->only(['link_url']);
        $data['image'] = $imagePath;

        $response = $this->service->store($data);

        if ($response) {
            return redirect()->route('admin.banners')->with('success', 'Banner added successfully.');
        }
        return back()->with('error', 'Something went wrong');
    }

    public function edit($id)
    {
        $data = $this->service->fetch($id);
        if ($data) {
            return view('admin.banners.edit', compact('data'));
        }
        return redirect()->route('admin.banners')->with('error', 'Banner not found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
            'link_url' => 'nullable|url',
        ]);

        $banner = $this->service->fetch($id);
        $imagePath = $banner->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $image = $request->file('image');
            $filename = "Banner_" . time() . "_" . rand(1111, 9999) . "." . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('Banners', $filename, 'public');
        }

        $data = $request->only(['link_url']);
        $data['image'] = $imagePath;

        $response = $this->service->update($id, $data);

        if ($response) {
            return redirect()->route('admin.banners')->with('success', 'Banner updated successfully.');
        }
        return back()->with('error', 'Something went wrong');
    }

    public function destroy($id)
    {
        $record = Banner::find($id);
        if ($record) {
            if ($record->image) {
                Storage::disk('public')->delete($record->image);
            }
            $record->delete();
            return response()->json(['status' => 'success', 'message' => 'Banner deleted successfully.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Banner not found.']);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $response = Banner::where('id', $id)->update(['is_active' => $status]);

        if ($response) {
            $msg = $status == 1 ? 'Activated' : 'Inactivated';
            return response()->json(['status' => 'success', 'message' => "Banner $msg successfully."]);
        }
        return response()->json(['status' => 'error', 'message' => 'Invalid Data']);
    }
}
