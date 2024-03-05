<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user()->id;

        $notifications = Notification::where('user_id', $user)
        ->orderBy('created_at','desc')
        ->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function show($id)
    {
        $user = auth()->user()->id;
        $notification = $user->notifications()->find($id);

        if ($notification) {
            return response()->json(['notification' => $notification]);
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }

    public function markAsRead($id)
    {
        $user = auth()->user()->id;
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }

    public function destroy($id)
    {
        $user = auth()->user()->id;
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted']);
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }

    public function store(Request $request)
    {
        Notification::updateOrCreate(
            [
                'user_id' => $request->user_id,
            ],
            [
                'user_id' => $request->user_id,
                'type' => $request->type,
                'notifiable_type' => $request->notifiable_type,
                'notifiable_id' => $request->notifiable_id,
                'data' => $request->data,
                'read_at' => null, // Initially set as unread
            ]
        );

        return response()->json(['message' => 'Notification stored successfully'], 201);
    }
}
