<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tugas;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderTugasMail;

class SendTaskReminders extends Command
{
    protected $signature = 'app:send-task-reminders';
    protected $description = 'Kirim reminder tugas 1 jam dan 5 menit sebelum deadline';

   public function handle()
{
    $now = now();
    $this->info("Waktu Server/Laptop Sekarang: " . $now->format('Y-m-d H:i:s'));

    // Ambil SEMUA tugas yang belum selesai dan belum dikirim reminder
    $tasks = Tugas::with('user')
        ->where('is_done', false)
        ->get();

    if ($tasks->isEmpty()) {
        $this->warn("Tidak ada tugas yang berstatus 'is_done = false' di database.");
        return;
    }

    foreach ($tasks as $tugas) {
        $deadline = \Carbon\Carbon::parse($tugas->tanggal);
        
        // Hitung selisih menit antara sekarang dan deadline
        // Positif berarti deadline di masa depan, negatif berarti sudah lewat
        $diffInMinutes = $now->diffInMinutes($deadline, false);
        
        $this->info("Mengecek Tugas: [{$tugas->tugas}] | Deadline: {$tugas->tanggal} | Selisih: {$diffInMinutes} menit");

        // Logic 1 Jam (antara 55 - 65 menit)
        if ($diffInMinutes <= 60 && $diffInMinutes > 50 && !$tugas->notified_1_hour) {
            $this->kirimEmail($tugas, '1_jam');
            $tugas->update(['notified_1_hour' => true]);
        } 
        
        // Logic 5 Menit (antara 1 - 6 menit)
        elseif ($diffInMinutes <= 5 && $diffInMinutes > 0 && !$tugas->notified_5_min) {
            $this->kirimEmail($tugas, '5_menit');
            $tugas->update(['notified_5_min' => true]);
        }

        // Logic Deadline (sudah lewat atau pas)
        elseif ($diffInMinutes <= 0 && !$tugas->notified_deadline) {
            $this->kirimEmail($tugas, 'deadline');
            $tugas->update([
                'notified_deadline' => true,
                'its_over' => true
            ]);
        }
    }
}

// Buat fungsi helper di bawah handle() agar kode rapi
private function kirimEmail($tugas, $jenis)
{
    try {
        Mail::to($tugas->user->email)->send(new ReminderTugasMail($tugas, $jenis));
        $this->info("✅ Email {$jenis} TERKIRIM ke: {$tugas->user->email}");
    } catch (\Exception $e) {
        $this->error("❌ GAGAL kirim email: " . $e->getMessage());
    }
}
}
