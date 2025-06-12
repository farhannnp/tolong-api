<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
    public function up(): void
    {
        Schema::table('warnings', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null')->after('expires_at');
            $table->string('status')->default('active')->after('admin_id'); 
        });
    }

    public function down(): void
    {
        Schema::table('warnings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
            $table->dropColumn(['admin_id', 'status']);
        });
    }
};
    