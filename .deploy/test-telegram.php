<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$svc = app(App\Services\TelegramService::class);
$token = config('services.telegram.bot_token');
echo "Bot username (config): " . (config('services.telegram.bot_username') ?: '(empty)') . PHP_EOL;
echo "Token set: " . ($token ? 'yes (len=' . strlen($token) . ')' : 'NO') . PHP_EOL;

$me = $svc->getMe($token);
echo "getMe result: " . json_encode($me, JSON_PRETTY_PRINT) . PHP_EOL;

// Check current webhook
$resp = Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot{$token}/getWebhookInfo");
echo "WebhookInfo: " . $resp->body() . PHP_EOL;
