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
        Schema::create('inquiries', function (Blueprint $table) {
        $table->id();
        $table->string('name');              // Tên khách hàng
        $table->string('email');             // Email liên hệ
        $table->string('phone');             // Số điện thoại
        $table->string('company')->nullable(); // Tên công ty (không bắt buộc)
        $table->text('message');             // Nội dung yêu cầu báo giá
        $table->string('status')->default('pending'); // pending | processed | closed
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
