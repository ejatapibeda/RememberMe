<?php

namespace App\Console\Commands;

use App\Models\Tugas;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    protected $signature = 'app:send-task-reminders';
    protected $description = 'Kirim reminder tugas (web notification + Telegram) 1 jam, 5 menit, dan saat deadline';

    public function handle(NotificationService $notifier): int
    {
        $now = now();
        $this->info('Waktu server: ' . $now->format('Y-m-d H:i:s'));

        $tasks = Tugas::with(['user', 'kategori'])
            ->where('is_done', false)
            ->get();

        if ($tasks->isEmpty()) {
            $this->warn("Tidak ada tugas aktif.");
            return self::SUCCESS;
        }

        foreach ($tasks as $tugas) {
            if (! $tugas->user) continue;

            $deadline = \Carbon\Carbon::parse($tugas->tanggal);
            $diff = $now->diffInMinutes($deadline, false);

            $this->line("[{$tugas->id}] {$tugas->tugas} | deadline {$tugas->tanggal} | selisih {$diff} menit");

            if ($diff <= 60 && $diff > 50 && ! $tugas->notified_1_hour) {
                $notifier->notifyReminder($tugas, '1_jam');
                $tugas->update(['notified_1_hour' => true]);
                $this->info("→ kirim reminder 1 jam");
            } elseif ($diff <= 5 && $diff > 0 && ! $tugas->notified_5_min) {
                $notifier->notifyReminder($tugas, '5_menit');
                $tugas->update(['notified_5_min' => true]);
                $this->info("→ kirim reminder 5 menit");
            } elseif ($diff <= 0 && ! $tugas->notified_deadline) {
                $notifier->notifyReminder($tugas, 'deadline');
                $tugas->update([
                    'notified_deadline' => true,
                    'its_over' => true,
                ]);
                $this->info("→ kirim reminder deadline");
            }
        }

        return self::SUCCESS;
    }
}
