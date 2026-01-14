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
        Schema::table('deals', function (Blueprint $table) {
            $table->foreignId('pipeline_id')
                ->nullable()
                ->after('team_id')
                ->constrained()
                ->nullOnDelete();

            $table->string('title')
                ->after('pipeline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['pipeline_id']);
            $table->dropColumn(['pipeline_id', 'title']);
        });
    }
};
