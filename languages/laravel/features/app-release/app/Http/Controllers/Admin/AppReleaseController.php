<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppRelease;
use App\Services\AppReleaseService;
use DataTables;
use Illuminate\Http\Request;

class AppReleaseController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new AppReleaseService();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->service->fetchRecord($request->all());

            return DataTables::of($query)
                ->addColumn('is_force_update', function ($rec) {
                    return $rec->is_force_update == 1
                        ? '<span class="tb-status text-success">Enabled</span>'
                        : '<span class="tb-status text-danger">Disabled</span>';
                })
                ->addColumn('action', function ($rec) {
                    return '<ul class="nk-tb-actions gx-1 my-n1">
                        <li class="me-n1">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="' . url('admin/app_releases/edit') . '/' . $rec->id . '"><em class="icon ni ni-edit"></em><span>Edit App Release</span></a></li>
                                        <li><a href="javascript:void(0)" onclick="deleteRecord(' . $rec->id . ')"><em class="icon ni ni-trash"></em><span>Delete App Release</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>';
                })
                ->rawColumns(['action', 'is_force_update'])
                ->make(true);
        }

        return view('admin.app_releases.all');
    }

    public function create()
    {
        return view('admin.app_releases.add');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['is_force_update'] = $request->has('is_force_update') ? 1 : 0;

        $response = $this->service->store($data);

        if ($response) {
            return redirect()->route('admin.app_releases')->with('success', 'App release added successfully.');
        }

        return back()->with('error', 'Something went wrong');
    }

    public function edit($id)
    {
        $data = $this->service->fetch($id);

        if ($data) {
            return view('admin.app_releases.edit', compact('data'));
        }

        return redirect()->route('admin.app_releases')->with('error', 'App release not found');
    }

    public function update(Request $request, $id)
    {
        $data = $this->validateRequest($request, $id);
        $data['is_force_update'] = $request->has('is_force_update') ? 1 : 0;

        $response = $this->service->update($id, $data);

        if ($response) {
            return redirect()->route('admin.app_releases')->with('success', 'App release updated successfully.');
        }

        return back()->with('error', 'Something went wrong');
    }

    public function destroy($id)
    {
        $record = AppRelease::find($id);

        if ($record) {
            $record->delete();
            return response()->json(['status' => 'success', 'message' => 'App release deleted successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'App release not found.']);
    }

    private function validateRequest(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'platform' => 'required|in:android,ios',
            'latest_version' => [
                'required',
                'string',
                'regex:/^(\d+\.)*\d+$/',
                function ($attribute, $value, $fail) use ($request, $ignoreId) {
                    $query = AppRelease::where('platform', $request->platform);

                    if ($ignoreId) {
                        $query->where('id', '!=', $ignoreId);
                    }

                    $previous = $query->latest()->first();

                    if ($previous && version_compare($value, $previous->latest_version, '<=')) {
                        $fail("The latest version must be higher than the current {$request->platform} version ({$previous->latest_version}).");
                    }
                },
            ],
            'release_notes' => 'nullable|string',
        ], [
            'platform.required' => 'The platform field is required.',
            'platform.in' => 'The platform must be either android or ios.',
            'latest_version.required' => 'The latest version field is required.',
            'latest_version.regex' => 'The latest version format is invalid.',
        ]);
    }
}
