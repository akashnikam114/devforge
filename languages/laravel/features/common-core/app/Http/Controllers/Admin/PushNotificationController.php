<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use App\Services\Firebase\FirebaseService;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;
use Exception;

class PushNotificationController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PushNotificationService();
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
                    }

                    return '<div><span class="tb-status text-danger" style="cursor:pointer" onclick="changeStatus(' . $rec->id . ',1)">Inactive</span></div>';
                })
                ->addColumn('action', function ($rec) {
                    return '<ul class="nk-tb-actions gx-1 my-n1">
                        <li class="me-n1">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="javascript:void(0)" onclick="sendNotification(' . $rec->id . ')"><em class="icon ni ni-send"></em><span>Send Notification</span></a></li>
                                        <li><a href="' . url('admin/push_notifications/edit') . '/' . $rec->id . '"><em class="icon ni ni-edit"></em><span>Edit Notification</span></a></li>
                                        <li><a href="javascript:void(0)" onclick="deleteRecord(' . $rec->id . ')"><em class="icon ni ni-trash"></em><span>Delete Notification</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>';
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
        return view('admin.push_notifications.all');
    }

    public function create()
    {
        return view('admin.push_notifications.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
            'is_active' => 'nullable|integer|in:0,1',
        ]);

        $imagePath = NULL;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = "Notification_" . time() . "_" . rand(1111, 9999) . "." . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('Notifications', $filename, 'public');
        }

        $data = $request->only(['title', 'description', 'is_active']);
        $data['image'] = $imagePath;

        $response = $this->service->store($data);

        if ($response) {
            return redirect()->route('admin.notification')->with('success', 'Notification added successfully.');
        }
        return back()->with('error', 'Something went wrong');
    }

    public function edit($id)
    {
        $data = $this->service->fetch($id);
        if ($data) {
            return view('admin.push_notifications.edit', compact('data'));
        }
        return redirect()->route('admin.notification')->with('error', 'Notification not found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|integer|in:0,1',
        ]);

        $notification = $this->service->fetch($id);
        $imagePath = $notification->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $image = $request->file('image');
            $filename = "Notification_" . time() . "_" . rand(1111, 9999) . "." . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('Notifications', $filename, 'public');
        }

        $data = $request->only(['title', 'description', 'is_active']);
        $data['image'] = $imagePath;

        $response = $this->service->update($id, $data);

        if ($response) {
            return redirect()->route('admin.notification')->with('success', 'Notification updated successfully.');
        }
        return back()->with('error', 'Something went wrong');
    }

    public function destroy($id)
    {
        $record = PushNotification::find($id);
        if ($record) {
            if ($record->image) {
                Storage::disk('public')->delete($record->image);
            }
            $record->delete();
            return response()->json(['status' => 'success', 'message' => 'Notification deleted successfully.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
    }

    public function sendPushNotification($id)
    {
        try {
            $notification = PushNotification::find($id);

            if (!$notification) {
                return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
            }

            $firebaseService = new FirebaseService();

            $data = [
                'title' => $notification->title,
                'description' => $notification->description,
                'image' => $notification->image,
                'topic' => 'all_users'
            ];

            $result = $firebaseService->sendPushNotification($data);

            if (isset($result['name'])) {
                return response()->json(['status' => 'success', 'message' => 'Notification has been sent successfully.']);
            }

            return response()->json(['status' => 'error', 'message' => 'Failed to send notification.']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to send notification.']);
        }
    }

    public function changeStatus(Request $request)
    {
        $response = PushNotification::where('id', $request->id)->update(['is_active' => $request->status]);

        if ($response) {
            $msg = $request->status == 1 ? 'Activated' : 'Inactivated';
            return response()->json(['status' => 'success', 'message' => "Notification $msg successfully."]);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid Data']);
    }
}
