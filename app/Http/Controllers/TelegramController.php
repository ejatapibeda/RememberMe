<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public function __construct(protected TelegramService $telegram) {}

    public function status(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'linked' => $user->hasTelegramLinked(),
            'telegram_username' => $user->telegram_username,
            'linked_at' => $user->telegram_linked_at,
            'has_custom_bot' => ! empty($user->telegram_bot_token),
            'bot_username' => $this->telegram->botUsername(),
            'pending_code' => $user->telegram_link_code,
            'pending_expires_at' => $user->telegram_link_expires_at,
        ]);
    }

    public function generateLinkCode(Request $request)
    {
        $user = $request->user();
        $code = strtoupper(Str::random(8));

        $user->forceFill([
            'telegram_link_code' => $code,
            'telegram_link_expires_at' => now()->addMinutes(15),
        ])->save();

        $username = $this->telegram->botUsername();
        $deepLink = $username ? "https://t.me/{$username}?start={$code}" : null;

        return response()->json([
            'success' => true,
            'code' => $code,
            'expires_at' => $user->telegram_link_expires_at,
            'bot_username' => $username,
            'deep_link' => $deepLink,
        ]);
    }

    public function unlink(Request $request)
    {
        $user = $request->user();
        $user->forceFill([
            'telegram_chat_id' => null,
            'telegram_username' => null,
            'telegram_linked_at' => null,
            'telegram_link_code' => null,
            'telegram_link_expires_at' => null,
        ])->save();

        return response()->json(['success' => true, 'message' => 'Telegram diputus.']);
    }

    public function saveCustomBot(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string|min:20',
            'chat_id' => 'required|string',
        ]);

        $info = $this->telegram->getMe($data['token']);
        if (! $info) {
            return response()->json([
                'success' => false,
                'message' => 'Token bot tidak valid atau bot tidak dapat dijangkau.',
            ], 422);
        }

        $user = $request->user();
        $user->forceFill([
            'telegram_bot_token' => $data['token'],
            'telegram_chat_id' => $data['chat_id'],
            'telegram_username' => $info['username'] ?? null,
            'telegram_linked_at' => now(),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => "Bot @{$info['username']} terhubung.",
            'bot' => $info,
        ]);
    }

    public function removeCustomBot(Request $request)
    {
        $user = $request->user();
        $user->forceFill([
            'telegram_bot_token' => null,
        ])->save();

        return response()->json(['success' => true, 'message' => 'Mode bot pribadi dimatikan.']);
    }

    public function test(Request $request)
    {
        $user = $request->user();
        if (! $user->hasTelegramLinked()) {
            return response()->json(['success' => false, 'message' => 'Telegram belum terhubung.'], 422);
        }
        $ok = $this->telegram->send($user, "✅ <b>Test berhasil!</b>\nNotifikasi RememberME aktif untukmu.");
        return response()->json([
            'success' => $ok,
            'message' => $ok ? 'Pesan test terkirim.' : 'Gagal mengirim pesan test.',
        ], $ok ? 200 : 500);
    }

    /**
     * Public webhook endpoint. Secret is part of the URL.
     */
    public function webhook(Request $request, string $secret)
    {
        $expected = config('services.telegram.webhook_secret');
        if (! $expected || ! hash_equals($expected, $secret)) {
            abort(404);
        }

        $update = $request->all();
        $this->telegram->processUpdate($update);
        return response()->json(['ok' => true]);
    }
}
