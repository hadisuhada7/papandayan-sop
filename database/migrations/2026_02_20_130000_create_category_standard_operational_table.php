<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_standard_operational', function (Blueprint $table) {
            $table->uuid('category_id');
            $table->uuid('standard_operational_id');
            $table->primary(['category_id', 'standard_operational_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('standard_operational_id')->references('id')->on('standard_operationals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_standard_operational');
    }
};
