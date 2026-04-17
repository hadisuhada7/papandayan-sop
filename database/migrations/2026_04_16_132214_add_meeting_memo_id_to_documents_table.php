<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignUuid('meeting_memo_id')->nullable()->after('internal_memo_id')->constrained('meeting_memos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['meeting_memo_id']);
            $table->dropColumn('meeting_memo_id');
        });
    }
};
