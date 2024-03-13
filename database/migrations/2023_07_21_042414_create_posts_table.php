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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');//사용자ID
            $table->foreignId('category_id')->constrained()->onDelete('cascade');//카테고리ID
            $table->unsignedBigInteger('order')->default(0);//순서
            $table->string('title');//제목
            $table->string('client');//클라이언트
            $table->string('specs');//spec
            $table->text('infos'); //간단한 내용
            $table->boolean('public')->default(true);//공개 비공개 여부
            $table->date('project_date')->nullable(); // 날짜
            $table->longtext('content')->nullable();//내용
            $table->unsignedInteger('like_count')->default(0);//좋아요 수
            $table->unsignedInteger('view_count')->default(0);//조회수
            $table->unsignedInteger('comment_count')->default(0);//댓글수 
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('posts');
        Schema::enableForeignKeyConstraints();
    }
};
