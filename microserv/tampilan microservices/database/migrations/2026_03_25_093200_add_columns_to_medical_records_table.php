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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained('users')->after('patient_id');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->after('doctor_id');
            $table->text('treatment')->nullable()->after('diagnosis');
            $table->text('lab_results')->nullable()->after('treatment');
            $table->text('medications')->nullable()->after('lab_results');
            $table->dateTime('follow_up_date')->nullable()->after('medications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['appointment_id']);
            $table->dropColumn([
                'doctor_id', 'appointment_id', 'treatment', 'lab_results', 
                'medications', 'follow_up_date'
            ]);
        });
    }
};
