<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $systemUsers = User::select('id', 'name', 'email', 'phone_number')
            ->orderBy('name')
            ->get();

        return view('admin.activity_logs.all', compact('systemUsers'));
    }

    public function data(Request $request)
    {
        $query = Activity::with('causer')->orderByDesc('id');
        $this->applyFilters($query, $request);

        return DataTables::eloquent($query)->make(true);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('from_date_time')) {
            $query->where('created_at', '>=', Carbon::parse($request->from_date_time)->format('Y-m-d H:i:s'));
        }

        if ($request->filled('to_date_time')) {
            $query->where('created_at', '<=', Carbon::parse($request->to_date_time)->format('Y-m-d H:i:s'));
        }

        if ($request->filled('system_user_id') && $request->system_user_id !== 'all') {
            $query->where('causer_id', $request->system_user_id);
        }
    }
}
