<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftarkan perintah (commands) untuk aplikasi.
     */
    protected $commands = [
        // Mendaftarkan file SendTaskReminders yang baru dibuat
        Commands\SendTaskReminders::class,
    ];

    /**
     * Definisikan jadwal eksekusi perintah.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan perintah pengingat tugas setiap menit
        // Ini akan memicu pengecekan its_over dan pengiriman email
        $schedule->command('app:send-task-reminders')->everyMinute();
    }

    /**
     * Daftarkan closure atau perintah berbasis kelas untuk aplikasi.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}