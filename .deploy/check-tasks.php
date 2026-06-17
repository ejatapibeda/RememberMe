<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Server NOW: ' . now() . PHP_EOL;
echo 'TZ: ' . config('app.timezone') . PHP_EOL . PHP_EOL;

$tasks = App\Models\Tugas::with('user')->where('is_done', false)->latest()->take(10)->get();
foreach ($tasks as $t) {
    $deadline = \Carbon\Carbon::parse($t->tanggal);
    $diff = now()->diffInMinutes($deadline, false);
    echo sprintf(
        "[#%d] %s | deadline=%s | diff=%.1f min | n1h=%d n5m=%d nDL=%d over=%d | user_chat=%s\n",
        $t->id,
        $t->tugas,
        $t->tanggal,
        $diff,
        $t->notified_1_hour ? 1 : 0,
        $t->notified_5_min ? 1 : 0,
        $t->notified_deadline ? 1 : 0,
        $t->its_over ? 1 : 0,
        $t->user?->telegram_chat_id ?: '(none)'
    );
}
