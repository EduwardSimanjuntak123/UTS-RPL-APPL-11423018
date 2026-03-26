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
        Schema::table('appointments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('status');
            $table->text('notes')->nullable()->after('description');
            $table->string('type')->default('consultation')->after('notes');
            $table->string('location')->nullable()->after('type');
            $table->integer('duration')->default(30)->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['description', 'notes', 'type', 'location', 'duration']);
        });
    }
};
