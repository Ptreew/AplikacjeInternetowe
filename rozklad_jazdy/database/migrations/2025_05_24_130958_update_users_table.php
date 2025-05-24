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
        Schema::table('users', function (Blueprint $table) {
            // Add username field after name
            $table->string('username')->after('name')->unique();
            
            // Change role to enum
            $table->enum('role', ['standard', 'admin'])->default('standard')->after('password');
            
            // Drop the existing email_verified_at if it exists
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
            $table->timestamp('email_verified_at')->nullable();
        });
    }
};
