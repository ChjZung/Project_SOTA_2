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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')     // Khóa ngoại liên kết bảng categories
                ->constrained('categories')
                ->onDelete('cascade');
            $table->string('name');              // Tên sản phẩm
            $table->string('slug')->unique();    // URL thân thiện SEO
            $table->string('product_code')->nullable(); // Mã sản phẩm (VD: SP-001)
            $table->text('summary')->nullable(); // Mô tả ngắn
            $table->longText('description')->nullable(); // Mô tả chi tiết (HTML)
            $table->string('image')->nullable(); // Ảnh chính sản phẩm
            $table->boolean('is_featured')->default(false); // Sản phẩm nổi bật
            $table->boolean('is_active')->default(true);    // Trạng thái hiển thị
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
