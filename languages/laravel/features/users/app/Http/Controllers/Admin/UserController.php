<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with('role')->orderByDesc('id');

            return DataTables::eloquent($query)
                ->addColumn('role_name', function ($user) {
                    return optional($user->role)->name ?? 'NA';
                })
                ->addColumn('status', function ($user) {
                    return $user->is_active == 1
                        ? '<span class="tb-status text-success">Active</span>'
                        : '<span class="tb-status text-danger">Inactive</span>';
                })
                ->addColumn('action', function ($user) {
                    return '<a href="' . url('admin/users/details') . '/' . $user->id . '" class="btn btn-sm btn-primary"><em class="icon ni ni-eye"></em><span>Details</span></a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.users.all');
    }

    public function show($id)
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }

        return view('admin.users.details', compact('user'));
    }
}
