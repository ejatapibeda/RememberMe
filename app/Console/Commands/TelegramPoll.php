<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll {--once : Hanya satu putaran getUpdates}';
    protected $description = 'Long-poll Telegram getUpdates (untuk dev tanpa webhook)';

    public function handle(TelegramService $telegram): int
    {
        if (! $telegram->appToken()) {
            $this->error('TELEGRAM_BOT_TOKEN belum di-set di .env');
            return self::FAILURE;
        }

        // Pastikan tidak ada webhook aktif (mengganggu polling)
        $telegram->deleteWebhook();

        $this->info('Mulai polling Telegram. Tekan Ctrl+C untuk berhenti.');
        $offset = (int) Cache::get('telegram:offset', 0);

        while (true) {
            $updates = $telegram->getUpdates($offset, 25);
            foreach ($updates as $update) {
                $this->line("Update: ".json_encode($update['message']['text'] ?? '...'));
                try {
                    $telegram->processUpdate($update);
                } catch (\Throwable $e) {
                    $this->error('Gagal proses update: '.$e->getMessage());
                }
                $offset = max($offset, ((int) ($update['update_id'] ?? 0)) + 1);
                Cache::put('telegram:offset', $offset, now()->addDay());
            }

            if ($this->option('once')) break;
            usleep(200_000); // 200ms idle
        }

        return self::SUCCESS;
    }
}
