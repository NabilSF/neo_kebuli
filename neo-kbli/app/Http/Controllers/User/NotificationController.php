<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $data = $this->notificationService->getNotificationsData($request->user(), $page);

        return view('user.notifications.index', compact('data'));
    }

    public function markAsRead(Request $request, string $id)
    {
        $this->notificationService->markAsRead($request->user(), $id);

        return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }

    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsRead($request->user());

        return back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }

    public function destroy(Request $request, string $id)
    {
        $this->notificationService->delete($request->user(), $id);

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
