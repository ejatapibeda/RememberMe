<?php

namespace App\Services;

use App\Models\RecurringReminder;
use Illuminate\Support\Facades\Log;

class RecurringNotificationService
{
    public function __construct(
        protected TelegramService $telegram,
        protected NotificationService $notifications,
    ) {}

    public function processDueReminders(): void
    {
        $now = now();
        $currentDay = (int) $now->dayOfWeek; // 0=Sun, 1=Mon...
        $currentTime = $now->format('H:i');
        $todayStart = $now->copy()->startOfDay();

        $reminders = RecurringReminder::with('user')
            ->where('enabled', true)
            ->where('time', $currentTime)
            ->where(function ($q) use ($todayStart) {
                $q->whereNull('last_sent_at')
                  ->orWhere('last_sent_at', '<', $todayStart);
            })
            ->get();

        foreach ($reminders as $reminder) {
            if (! in_array($currentDay, $reminder->days ?? [], true)) {
                continue;
            }

            $user = $reminder->user;
            if (! $user) {
                continue;
            }

            $title = "\u{1F514} Rutinitas: {$reminder->title}";
            $body = $reminder->body ?: "Waktunya untuk {$reminder->title}!";

            // Web notification
            $this->notifications->notify(
                $user,
                $title,
                $body,
                null,
                'recurring'
            );

            // Telegram
            $this->telegram->send($user, "<b>{$title}</b>\n\n{$body}");

            $reminder->update(['last_sent_at' => now()]);

            Log::info('Recurring reminder sent', [
                'reminder_id' => $reminder->id,
                'user_id' => $user->id,
                'title' => $reminder->title,
            ]);
        }
    }
}
