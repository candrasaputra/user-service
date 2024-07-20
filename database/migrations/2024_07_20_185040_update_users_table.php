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
            // Rename the 'email' column to 'username'
            $table->renameColumn('email', 'username');
            
            // Add 'created_by' and 'updated_by' columns
            $table->unsignedBigInteger('created_by')->nullable()->after('password');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename the 'username' column back to 'email'
            $table->renameColumn('username', 'email');
            
            // Drop 'created_by' and 'updated_by' columns
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });
    }
};
