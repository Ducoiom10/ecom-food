<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Danh sách thông báo của user đang đăng nhập.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->paginate(20);

        return view('client.notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu một thông báo đã đọc.
     */
    public function markRead(int $id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc.
     */
    public function markAllRead()
    {
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    /**
     * API trả về số thông báo chưa đọc + 5 thông báo mới nhất (cho dropdown).
     */
    public function unreadCount()
    {
        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
        $recent = auth()->user()->notifications()->latest()->take(5)->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'recent' => $recent->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }
}
