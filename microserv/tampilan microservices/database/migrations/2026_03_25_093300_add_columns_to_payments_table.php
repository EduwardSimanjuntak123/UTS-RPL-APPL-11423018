<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->constrained('users')->after('appointment_id');
            $table->string('transaction_id')->nullable()->unique()->after('method');
            $table->text('notes')->nullable()->after('transaction_id');
            $table->unsignedBigInteger('insurance_claim_id')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['patient_id', 'transaction_id', 'notes', 'insurance_claim_id']);
        });
    }
};
