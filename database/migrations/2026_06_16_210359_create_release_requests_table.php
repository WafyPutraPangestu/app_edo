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
        Schema::create('release_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 1. File Upload Klien (Sudah ada)
            $table->string('surat_kuasa_path');
            $table->string('awb_path')->nullable();

            // 2. TAMBAHAN BARU: Detail Kargo (Diisi Klien saat awal pengajuan atau Admin saat verifikasi)
            $table->string('awb_number')->nullable(); // Contoh: 205-32481223
            $table->string('flight_number')->nullable(); // Contoh: NH0871
            $table->string('origin')->nullable(); // Contoh: BEIJING
            $table->string('destination')->nullable(); // Contoh: JAKARTA
            $table->string('quantity')->nullable(); // Contoh: 31 CTNS / 1 PALLET
            $table->string('gross_weight')->nullable(); // Contoh: 532.0 KG
            $table->text('goods_description')->nullable(); // Contoh: TANTALUM WIRE

            // 3. Status & Notes (Sudah ada)
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('rejection_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_requests');
    }
};
