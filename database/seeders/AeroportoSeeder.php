<?php

namespace Database\Seeders;

use App\Models\Aeroporto;
use Illuminate\Database\Seeder;

class AeroportoSeeder extends Seeder
{
    /**
     * Seed the airports used by the application.
     */
    public function run(): void
    {
        $aeroportos = [
            'Paris Beauvais - BVA',
            'Madeira - FNC',
            'Faro - FAO',
        ];

        foreach ($aeroportos as $nomeAeroporto) {
            Aeroporto::updateOrCreate(
                ['nome_aeroporto' => $nomeAeroporto],
                ['nome_aeroporto' => $nomeAeroporto]
            );
        }
    }
}
