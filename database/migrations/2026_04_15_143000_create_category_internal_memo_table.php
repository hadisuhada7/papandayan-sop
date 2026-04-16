<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_internal_memo', function (Blueprint $table) {
            $table->uuid('category_id');
            $table->uuid('internal_memo_id');
            $table->primary(['category_id', 'internal_memo_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('internal_memo_id')->references('id')->on('internal_memos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_internal_memo');
    }
};
