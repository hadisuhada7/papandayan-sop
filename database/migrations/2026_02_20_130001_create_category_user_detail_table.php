<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_user_detail', function (Blueprint $table) {
            $table->uuid('category_id');
            $table->uuid('user_detail_id');
            $table->primary(['category_id', 'user_detail_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('user_detail_id')->references('id')->on('user_details')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_user_detail');
    }
};
