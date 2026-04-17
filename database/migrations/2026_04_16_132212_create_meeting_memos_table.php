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
        Schema::create('meeting_memos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('document_number')->nullable();
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->mediumText('rules');
            $table->foreignUuid('company_id')->constrained('companies')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_memos');
    }
};
