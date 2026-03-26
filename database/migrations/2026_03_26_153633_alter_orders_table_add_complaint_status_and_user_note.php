<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah enum value 'complaint'
        DB::statement("ALTER TABLE orders MODIFY status ENUM(
            'pending',
            'waiting_verification',
            'success',
            'rejected',
            'complaint'
        ) NOT NULL");

        // Tambah field user_note
        Schema::table('orders', function (Blueprint $table) {
            $table->text('user_note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback enum tanpa 'complaint'
        DB::statement("ALTER TABLE orders MODIFY status ENUM(
            'pending',
            'waiting_verification',
            'success',
            'rejected',
            'cancelled'
        ) NOT NULL");

        // Hapus field user_note
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('user_note');
        });
    }
};
