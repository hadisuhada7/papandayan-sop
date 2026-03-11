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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('attachment');
            $table->enum('type', ['sop', 'form'])->nullable();
            $table->foreignUuid('standard_operational_id')->nullable()->constrained('standard_operationals')->onDelete('cascade');
            $table->foreignUuid('policy_letter_id')->nullable()->constrained('policy_letters')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
