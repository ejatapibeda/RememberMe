<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->index();
            $table->string('telegram_username')->nullable();
            $table->text('telegram_bot_token')->nullable();
            $table->string('telegram_link_code', 16)->nullable()->index();
            $table->timestamp('telegram_link_expires_at')->nullable();
            $table->timestamp('telegram_linked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_chat_id',
                'telegram_username',
                'telegram_bot_token',
                'telegram_link_code',
                'telegram_link_expires_at',
                'telegram_linked_at',
            ]);
        });
    }
};
