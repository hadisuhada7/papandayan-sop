<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_meeting_memo', function (Blueprint $table) {
            $table->uuid('category_id');
            $table->uuid('meeting_memo_id');
            $table->primary(['category_id', 'meeting_memo_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('meeting_memo_id')->references('id')->on('meeting_memos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_meeting_memo');
    }
};
