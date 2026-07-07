<?php

namespace Database\Seeders;

use App\Models\Aeronave;
use App\Models\Fabricante;
use Illuminate\Database\Seeder;

class AeronaveSeeder extends Seeder
{
    /**
     * Seed aircraft models and their passenger capacity.
     */
    public function run(): void
    {
        foreach ($this->aeronaves() as $modelo => $capacidade) {
            Aeronave::updateOrCreate(
                ['modelo' => $modelo],
                [
                    'capacidade' => $capacidade,
                    'fabricante_id' => $this->fabricanteId($modelo),
                ]
            );
        }
    }

    private function fabricanteId(string $modelo): ?int
    {
        $fabricante = match (true) {
            str_starts_with($modelo, 'ATR') => 'ATR',
            str_starts_with($modelo, 'A') => 'Airbus',
            str_starts_with($modelo, '7') || str_starts_with($modelo, '307') => 'Boeing',
            str_starts_with($modelo, 'E'), str_starts_with($modelo, 'ERJ') => 'Embraer',
            str_starts_with($modelo, 'CRJ') => 'Bombardier Aerospace',
            str_starts_with($modelo, 'Q400') => 'De Havilland Canada',
            str_starts_with($modelo, 'BAE') => 'BAE Systems',
            str_starts_with($modelo, 'D0328') => 'Dornier',
            str_starts_with($modelo, 'Fokker') => 'Fokker',
            str_starts_with($modelo, 'MD') => 'McDonnell Douglas',
            str_starts_with($modelo, 'S340') => 'Saab',
            str_starts_with($modelo, 'DC3') => 'Douglas Aircraft Company',
            str_starts_with($modelo, 'Convair') => 'Convair',
            str_starts_with($modelo, 'Concorde') => 'Aerospatiale/BAC',
            default => null,
        };

        return $fabricante
            ? Fabricante::where('nome', $fabricante)->value('id')
            : null;
    }

    private function aeronaves(): array
    {
        return [
            '307-Stratoliner' => 60,
            '727-200' => 189,
            '737-700' => 149,
            '737-800' => 189,
            '737-900ER' => 220,
            '737-BBJ' => 190,
            '737-MAX8' => 210,
            '737-MAX8-200' => 200,
            '737-MAX9' => 220,
            '737-MAX10' => 230,
            '757-200' => 200,
            '757-300' => 250,
            '767-300' => 351,
            '777-200ER' => 313,
            '777-300' => 396,
            '777-300ER' => 396,
            '787-8' => 248,
            '787-9' => 296,
            '787-10' => 336,
            '797-800' => 189,
            'A220-300' => 160,
            'A318' => 132,
            'A319-100' => 156,
            'A319-CJ' => 190,
            'A320-200' => 180,
            'A320-ACJ' => 250,
            'A320Neo' => 194,
            'A321-200' => 220,
            'A321Neo' => 244,
            'A330-200' => 406,
            'A330-300' => 440,
            'A330-900Neo' => 460,
            'A340-300' => 440,
            'A340-600' => 475,
            'A350-900' => 440,
            'A350-1000' => 350,
            'ATR42-600' => 48,
            'ATR72-600' => 72,
            'BAE146' => 100,
            'C909' => 200,
            'C919' => 158,
            'CRJ-700' => 78,
            'CRJ-900-LR' => 90,
            'CRJ-900-NextGen' => 70,
            'CRJ1000' => 100,
            'Concorde' => 120,
            'Convair CV240' => 40,
            'D0328' => 30,
            'D0328-JET' => 33,
            'DC3' => 32,
            'E190E2' => 114,
            'E195-AR' => 124,
            'E195E2' => 146,
            'ERJ135' => 37,
            'ERJ170-LR' => 66,
            'ERJ175' => 76,
            'ERJ190' => 96,
            'ERJ190-1000' => 80,
            'ERJ190E2' => 99,
            'Fokker F100' => 97,
            'MD-11' => 298,
            'Q400' => 82,
            'Q400-NextGen' => 82,
            'S340' => 34,
        ];
    }
}
