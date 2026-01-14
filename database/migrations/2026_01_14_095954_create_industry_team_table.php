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
        Schema::create('industry_team', function (Blueprint $table) {
            $table->id();

            $table->foreignId('industry_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique(['industry_id', 'team_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_team');
    }
};
