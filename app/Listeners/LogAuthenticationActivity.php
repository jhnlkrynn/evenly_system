<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;

/** Writes every login and logout to activity_logs (auto-discovered listener). */
class LogAuthenticationActivity
{
    public function handleLogin(Login $event): void
    {
        $this->log($event->user, 'login', 'Logged in');
    }

    public function handleLogout(Logout $event): void
    {
        $this->log($event->user, 'logout', 'Logged out');
    }

    private function log(?Authenticatable $user, string $action, string $description): void
    {
        if (! $user) {
            return;
        }

        ActivityLog::record($user->getAuthIdentifier(), $action, "{$description} as {$user->role}");
    }
}
