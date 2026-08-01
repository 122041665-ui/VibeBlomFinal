<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->with('actor')
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Notificaciones marcadas como leídas.');
    }

    public function open(Request $request, UserNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        $target = $this->internalTarget($notification->url);

        return $target
            ? redirect()->to($target)
            : redirect()->route('notifications.index');
    }

    private function internalTarget(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $parts = parse_url(trim($url));
        $path = is_array($parts) ? ($parts['path'] ?? null) : null;

        if (! is_string($path) || ! str_starts_with($path, '/')) {
            return null;
        }

        $target = $path;
        $target .= isset($parts['query']) ? '?'.$parts['query'] : '';
        $target .= isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $target;
    }
}
