<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url? : Base URL aplikasi (https://...). Default APP_URL}';
    protected $description = 'Pasang webhook Telegram ke endpoint /telegram/webhook/{secret}';

    public function handle(TelegramService $telegram): int
    {
        $secret = config('services.telegram.webhook_secret');
        if (! $secret) {
            $this->error('TELEGRAM_WEBHOOK_SECRET belum di-set di .env');
            return self::FAILURE;
        }

        $base = rtrim($this->argument('url') ?: config('app.url'), '/');
        $url = "{$base}/telegram/webhook/{$secret}";
        $this->info("Set webhook ke: {$url}");

        $resp = $telegram->setWebhook($url);
        $this->line(json_encode($resp, JSON_PRETTY_PRINT));

        return ($resp['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
    }
}
