<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationsController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', ['notifications' => $request->user()->notifications()->latest()->paginate(15)]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json(['count' => $request->user()->unreadNotifications()->count()]);
    }

    public function read(Request $request, string $notification): JsonResponse|RedirectResponse
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();
        $url = data_get($item->data, 'url') ?: route('notifications.index');

        return $request->expectsJson() ? response()->json(['url' => $url]) : redirect()->to($url);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }
}
