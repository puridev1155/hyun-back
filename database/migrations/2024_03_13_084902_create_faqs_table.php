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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');//카테고리ID
            $table->string('company_name');//제목
            $table->string('phone');//클라이언트
            $table->string('email');//spec
            $table->text('urls'); //간단한 내용
            $table->boolean('project_price')->default(true);//공개 비공개 여부
            $table->date('project_date')->nullable(); // 날짜
            $table->date('project_info')->nullable(); // 날짜
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
