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
        // Thêm cột image vào bảng users sau cột email
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa cột image từ bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};

