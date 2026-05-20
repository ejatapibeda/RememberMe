<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('tugas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_user');
        $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        $table->unsignedBigInteger('id_kategori');
        $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('cascade');
        $table->dateTime('tanggal'); 
        $table->enum('prioritas', ['ya', 'tidak'])->default('tidak'); 
        $table->string('tugas');
        $table->boolean('is_done')->default(false);
        $table->boolean('its_over')->default(false);
         $table->boolean('notified_1_hour')->default(false);
        $table->boolean('notified_5_min')->default(false);
         $table->boolean('notified_deadline')->default(false);
        $table->timestamps();
    });
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
