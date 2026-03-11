<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_policy_letter', function (Blueprint $table) {
            $table->uuid('category_id');
            $table->uuid('policy_letter_id');
            $table->primary(['category_id', 'policy_letter_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('policy_letter_id')->references('id')->on('policy_letters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_policy_letter');
    }
};
