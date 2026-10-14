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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                         // Khóa chính tự tăng
            $table->foreignId('parent_id')        // Liên kết danh mục cha (đa cấp)
                ->nullable()
                ->constrained('categories')
                ->onDelete('cascade');
            $table->string('name');               // Tên danh mục
            $table->string('slug')->unique();     // URL thân thiện SEO
            $table->text('description')->nullable();
            $table->string('image')->nullable();  // Ảnh đại diện danh mục
            $table->integer('sort_order')->default(0); // Thứ tự sắp xếp
            $table->timestamps();                // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
