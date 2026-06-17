<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function appToken(): ?string
    {
        return config('services.telegram.bot_token');
    }

    public function botUsername(): ?string
    {
        return config('services.telegram.bot_username');
    }

    protected function tokenFor(User $user): ?string
    {
        return $user->telegram_bot_token ?: $this->appToken();
    }

    protected function endpoint(string $token, string $method): string
    {
        return "https://api.telegram.org/bot{$token}/{$method}";
    }

    /**
     * Build a fresh HTTP client. In local/dev disable SSL verification by
     * default to bypass missing CA bundle on Windows. Override with
     * TELEGRAM_VERIFY_SSL=true (or false) in .env.
     */
    protected function http(int $timeout = 10)
    {
        $verify = env('TELEGRAM_VERIFY_SSL', ! app()->environment('local'));
        return Http::timeout($timeout)->withOptions(['verify' => (bool) $verify]);
    }

    public function send(User $user, string $html, ?string $chatIdOverride = null): bool
    {
        $token = $this->tokenFor($user);
        $chatId = $chatIdOverride ?: $user->telegram_chat_id;
        if (! $token || ! $chatId) {
            return false;
        }

        try {
            $resp = $this->http(10)->post($this->endpoint($token, 'sendMessage'), [
                'chat_id' => $chatId,
                'text' => $html,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if (! $resp->ok()) {
                Log::warning('Telegram send failed', [
                    'user_id' => $user->id,
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                ]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('Telegram send exception: '.$e->getMessage());
            return false;
        }
    }

    public function getMe(string $token): ?array
    {
        try {
            $resp = $this->http(10)->get($this->endpoint($token, 'getMe'));
            if ($resp->ok() && ($resp->json('ok') === true)) {
                return $resp->json('result');
            }
        } catch (\Throwable $e) {
            Log::error('Telegram getMe exception: '.$e->getMessage());
        }
        return null;
    }

    public function setWebhook(string $url, ?string $secretToken = null): array
    {
        $token = $this->appToken();
        if (! $token) {
            return ['ok' => false, 'error' => 'No bot token configured'];
        }
        $payload = ['url' => $url, 'drop_pending_updates' => true];
        if ($secretToken) {
            $payload['secret_token'] = $secretToken;
        }
        $resp = $this->http(10)->post($this->endpoint($token, 'setWebhook'), $payload);
        return $resp->json() ?? ['ok' => false, 'error' => 'Empty response'];
    }

    public function deleteWebhook(): array
    {
        $token = $this->appToken();
        if (! $token) {
            return ['ok' => false];
        }
        $resp = $this->http(10)->post($this->endpoint($token, 'deleteWebhook'));
        return $resp->json() ?? ['ok' => false];
    }

    public function getUpdates(int $offset = 0, int $timeout = 25): array
    {
        $token = $this->appToken();
        if (! $token) {
            return [];
        }
        try {
            $resp = $this->http($timeout + 5)->get($this->endpoint($token, 'getUpdates'), [
                'offset' => $offset,
                'timeout' => $timeout,
                'allowed_updates' => json_encode(['message']),
            ]);
            if ($resp->ok() && $resp->json('ok')) {
                return $resp->json('result') ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('Telegram getUpdates exception: '.$e->getMessage());
        }
        return [];
    }

    /**
     * Handle a /start command from Telegram. If a valid pairing code is supplied,
     * link the chat to the matching user.
     */
    public function handleStartCommand(int $chatId, ?string $username, ?string $code): string
    {
        $code = $code ? trim($code) : null;

        if (! $code) {
            return "👋 Halo! Saya bot RememberME.\n\nUntuk menghubungkan akun Telegram-mu, buka halaman <b>Profil</b> di aplikasi, klik <b>Hubungkan Telegram</b>, lalu kirim perintah:\n<code>/start KODE_KAMU</code>";
        }

        $user = User::where('telegram_link_code', $code)->first();
        if (! $user) {
            return "❌ Kode tidak valid. Buka halaman Profil di aplikasi RememberME untuk mendapatkan kode baru.";
        }

        if ($user->telegram_link_expires_at && Carbon::parse($user->telegram_link_expires_at)->isPast()) {
            return "⌛ Kode sudah kadaluarsa. Buat kode baru di halaman Profil aplikasi.";
        }

        $user->forceFill([
            'telegram_chat_id' => (string) $chatId,
            'telegram_username' => $username,
            'telegram_link_code' => null,
            'telegram_link_expires_at' => null,
            'telegram_linked_at' => now(),
        ])->save();

        return "✅ Berhasil terhubung, <b>{$user->name}</b>!\n\nKamu akan mendapatkan reminder tugas otomatis di chat ini. 🚀";
    }

    public function processUpdate(array $update): void
    {
        $message = $update['message'] ?? null;
        if (! $message) return;

        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');
        $username = $message['from']['username'] ?? null;
        if (! $chatId) return;

        if (str_starts_with($text, '/start')) {
            $parts = preg_split('/\s+/', $text, 2);
            $code = $parts[1] ?? null;
            $reply = $this->handleStartCommand((int) $chatId, $username, $code);
        } elseif (str_starts_with($text, '/help')) {
            $reply = "ℹ️ Bot RememberME mengirim reminder tugas otomatis. Untuk pairing, gunakan <code>/start KODE</code> dari halaman Profil aplikasi.";
        } else {
            $reply = "Gunakan <code>/start KODE</code> untuk menghubungkan akun, atau <code>/help</code>.";
        }

        // Send reply directly (no user yet might exist for unmatched code)
        $token = $this->appToken();
        if ($token) {
            try {
                $this->http(10)->post($this->endpoint($token, 'sendMessage'), [
                    'chat_id' => $chatId,
                    'text' => $reply,
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Throwable $e) {
                Log::error('Telegram reply exception: '.$e->getMessage());
            }
        }
    }
}
