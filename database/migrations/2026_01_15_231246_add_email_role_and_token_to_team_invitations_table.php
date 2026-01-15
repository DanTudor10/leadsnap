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
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->string('email')->after('team_id');
            $table->string('role')->default('user')->after('email'); // 'user' or 'admin'
            $table->string('token')->unique()->nullable()->after('role'); // for unique link invitations
            $table->timestamp('accepted_at')->nullable()->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->dropColumn(['email', 'role', 'token', 'accepted_at']);
        });
    }
};
