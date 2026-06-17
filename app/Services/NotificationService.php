<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
    public function __construct(protected TelegramService $telegram) {}

    public function notifyReminder(Tugas $tugas, string $jenis): void
    {
        /** @var User|null $user */
        $user = $tugas->user;
        if (! $user) return;

        [$title, $body, $emoji] = $this->compose($jenis, $tugas);

        AppNotification::create([
            'user_id' => $user->id,
            'tugas_id' => $tugas->id,
            'jenis' => $jenis,
            'title' => $title,
            'body' => $body,
            'meta' => [
                'tanggal' => optional($tugas->tanggal)->toIso8601String(),
                'kategori' => optional($tugas->kategori)->nama_kategori,
                'prioritas' => $tugas->prioritas,
            ],
        ]);

        if ($user->hasTelegramLinked()) {
            $deadline = Carbon::parse($tugas->tanggal)->translatedFormat('d M Y H:i');
            $kategori = optional($tugas->kategori)->nama_kategori ?: '-';
            $prioritas = $tugas->prioritas === 'ya' ? '🔥 <b>Prioritas Tinggi</b>' : 'Normal';
            $html = "{$emoji} <b>{$title}</b>\n\n"
                  . "📌 <b>Tugas:</b> ".e($tugas->tugas)."\n"
                  . "🗂 <b>Kategori:</b> ".e($kategori)."\n"
                  . "🕒 <b>Deadline:</b> {$deadline}\n"
                  . "⭐ <b>Status:</b> {$prioritas}\n\n"
                  . "— RememberME";
            $this->telegram->send($user, $html);
        }
    }

    /**
     * @return array{0:string,1:string,2:string} [title, body, emoji]
     */
    protected function compose(string $jenis, Tugas $tugas): array
    {
        return match ($jenis) {
            '1_jam' => [
                'Reminder: 1 jam lagi',
                "Tugas \"{$tugas->tugas}\" akan jatuh tempo dalam 1 jam.",
                '🔔',
            ],
            '5_menit' => [
                'Reminder: 5 menit lagi!',
                "Tugas \"{$tugas->tugas}\" akan jatuh tempo dalam 5 menit.",
                '⏰',
            ],
            'deadline' => [
                'Deadline tugas telah tiba',
                "Tugas \"{$tugas->tugas}\" sudah mencapai deadline.",
                '🚨',
            ],
            default => ['Reminder', $tugas->tugas, '🔔'],
        };
    }
}
