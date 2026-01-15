<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Industry;

return new class extends Migration
{
    public function up(): void
    {
        $industries = [
            'Producție, materiale de construcții',
            'Servicii IT, software house',
            'Agenție de marketing, evenimente, PR',
            'Imobiliare, investiții',
            'SRE (Surse regenerabile de energie)',
            'Servicii financiare și asigurări',
            'Alte servicii pentru companii',
            'Consultanță și training',
        ];

        foreach ($industries as $industry) {
            Industry::create(['name' => $industry]);
        }
    }

    public function down(): void
    {
        Industry::whereIn('name', [
            'Producție, materiale de construcții',
            'Servicii IT, software house',
            'Agenție de marketing, evenimente, PR',
            'Imobiliare, investiții',
            'SRE (Surse regenerabile de energie)',
            'Servicii financiare și asigurări',
            'Alte servicii pentru companii',
            'Consultanță și training',
        ])->delete();
    }
};