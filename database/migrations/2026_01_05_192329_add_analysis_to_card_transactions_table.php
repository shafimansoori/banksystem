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
        Schema::table('card_transactions', function (Blueprint $table) {
            $table->enum('risk_level', ['safe', 'low', 'medium', 'high'])->default('safe')->after('updated_at');
            $table->text('analysis_result')->nullable()->after('risk_level');
            $table->boolean('is_flagged')->default(false)->after('analysis_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_transactions', function (Blueprint $table) {
            $table->dropColumn(['risk_level', 'analysis_result', 'is_flagged']);
        });
    }
};
