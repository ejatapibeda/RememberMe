<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = AppNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'unread_count' => $items->whereNull('read_at')->count(),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();
        return response()->json(['unread_count' => $count]);
    }

    public function markRead(Request $request, int $id)
    {
        $n = AppNotification::where('user_id', $request->user()->id)->findOrFail($id);
        if (! $n->read_at) {
            $n->read_at = now();
            $n->save();
        }
        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, int $id)
    {
        $n = AppNotification::where('user_id', $request->user()->id)->findOrFail($id);
        $n->delete();
        return response()->json(['success' => true]);
    }
}
