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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('patient')->after('password');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('specialty')->nullable()->after('address');
            $table->string('license_number')->nullable()->unique()->after('specialty');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('license_number');
            $table->string('insurance_provider')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'address', 'specialty', 
                'license_number', 'status', 'insurance_provider'
            ]);
        });
    }
};
