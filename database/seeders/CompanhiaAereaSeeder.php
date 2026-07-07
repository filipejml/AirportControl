<?php

namespace Database\Seeders;

use App\Models\Aeronave;
use App\Models\CompanhiaAerea;
use Illuminate\Database\Seeder;

class CompanhiaAereaSeeder extends Seeder
{
    /**
     * Seed airline companies and their aircraft fleets.
     */
    public function run(): void
    {
        foreach ($this->companhias() as $companhiaData) {
            $companhia = CompanhiaAerea::updateOrCreate(
                ['codigo' => $companhiaData['codigo']],
                ['nome' => $companhiaData['nome']]
            );

            foreach ($companhiaData['aeronaves'] as $modelo) {
                $aeronave = Aeronave::where('modelo', $modelo)->firstOrFail();

                $companhia->aeronaves()->syncWithoutDetaching([
                    $aeronave->id => ['disponivel' => true],
                ]);
            }
        }
    }

    private function companhias(): array
    {
        return [
            [
                'nome' => 'Peninsula',
                'codigo' => 'PN',
                'aeronaves' => [
                    'A320Neo',
                    'CRJ1000',
                    'A330-200',
                    'A330-300',
                    'A350-900',
                ],
            ],
            [
                'nome' => 'Gluck Airlines',
                'codigo' => 'GK',
                'aeronaves' => [
                    'CRJ-700',
                    'CRJ-900-LR',
                    'ERJ190',
                    'BAE146',
                    'A320-200',
                    'ERJ135',
                    'A319-100',
                    'A320Neo',
                    'A321-200',
                    'A321Neo',
                    'A330-300',
                    'A340-600',
                    'A350-900',
                ],
            ],
            [
                'nome' => 'Air Odysseia',
                'codigo' => 'AO',
                'aeronaves' => [
                    'Q400',
                    'S340',
                ],
            ],
            [
                'nome' => 'Stellar Party',
                'codigo' => 'SP',
                'aeronaves' => [
                    '727-200',
                    'A350-900',
                    'Concorde',
                    'C919',
                    'MD-11',
                ],
            ],
            [
                'nome' => 'AraSky',
                'codigo' => 'AS',
                'aeronaves' => [
                    'E190E2',
                    '737-700',
                    'A320Neo',
                    'A321Neo',
                    'E195-AR',
                    'E195E2',
                    'A330-200',
                    'A330-900Neo',
                ],
            ],
            [
                'nome' => 'Prosperity Lines',
                'codigo' => 'PL',
                'aeronaves' => [
                    'CRJ-900-LR',
                    'ERJ135',
                    'CRJ-700',
                    '737-BBJ',
                    'A320-ACJ',
                    'ERJ190-1000',
                ],
            ],
            [
                'nome' => 'Alpha',
                'codigo' => 'AA',
                'aeronaves' => [
                    'A321-200',
                    'CRJ-900-LR',
                    '737-900ER',
                    '777-300',
                    'A319-100',
                    'A321Neo',
                    'A330-900Neo',
                    'MD-11',
                    '757-200',
                    '757-300',
                ],
            ],
            [
                'nome' => 'Skyways',
                'codigo' => 'SK',
                'aeronaves' => [
                    'CRJ-900-LR',
                    'D0328-JET',
                    'A220-300',
                    '737-700',
                    '737-BBJ',
                    'A319-CJ',
                    'A320-200',
                    'A340-600',
                    'A350-900',
                ],
            ],
            [
                'nome' => 'Outback',
                'codigo' => 'OB',
                'aeronaves' => [
                    'BAE146',
                    'A330-200',
                    'Q400',
                    'Fokker F100',
                    '737-800',
                    'A330-300',
                ],
            ],
            [
                'nome' => 'Orient',
                'codigo' => 'OT',
                'aeronaves' => [
                    'A318',
                    'A220-300',
                    'A320Neo',
                    'A320-200',
                ],
            ],
            [
                'nome' => 'Bon Voyage',
                'codigo' => 'BV',
                'aeronaves' => [
                    'A350-900',
                    'A318',
                    'A319-100',
                    'A321Neo',
                    'A340-300',
                    'A330-200',
                    'A320Neo',
                ],
            ],
            [
                'nome' => 'Fast Travel',
                'codigo' => 'FT',
                'aeronaves' => [
                    'ATR42-600',
                    'Q400',
                ],
            ],
            [
                'nome' => 'Jurassic Pax',
                'codigo' => 'JP',
                'aeronaves' => [
                    'A320Neo',
                    'A318',
                    'E190E2',
                    'A220-300',
                ],
            ],
            [
                'nome' => 'Air Kiwi',
                'codigo' => 'KW',
                'aeronaves' => [
                    'Q400',
                    'ATR42-600',
                    'ATR72-600',
                    'D0328',
                    'A320-200',
                    'A321Neo',
                ],
            ],
            [
                'nome' => 'Vintage Airline II',
                'codigo' => 'VAII',
                'aeronaves' => [
                    'S340',
                    'DC3',
                    'Convair CV240',
                ],
            ],
            [
                'nome' => 'Ryoko Airlines',
                'codigo' => 'RA',
                'aeronaves' => [
                    'E190E2',
                    'A318',
                    'A320Neo',
                    'A220-300',
                    'CRJ1000',
                ],
            ],
            [
                'nome' => 'TAL',
                'codigo' => 'TAL',
                'aeronaves' => [
                    'ATR72-600',
                    'A320Neo',
                    'E190E2',
                    'A319-100',
                    'A321Neo',
                    'A330-900Neo',
                ],
            ],
            [
                'nome' => 'CloudG',
                'codigo' => 'CG',
                'aeronaves' => [
                    'CRJ1000',
                    'Fokker F100',
                    'CRJ-700',
                    '737-700',
                ],
            ],
            [
                'nome' => 'Maasai Airways',
                'codigo' => 'MAA',
                'aeronaves' => [
                    '737-700',
                    'A318',
                    'A340-300',
                    '787-8',
                    'D0328',
                    'E190E2',
                    'ERJ175',
                ],
            ],
            [
                'nome' => 'World Wide',
                'codigo' => 'WW',
                'aeronaves' => [
                    'A320Neo',
                    '787-8',
                ],
            ],
            [
                'nome' => 'Aerowings',
                'codigo' => 'AW',
                'aeronaves' => [
                    'Q400',
                    '737-800',
                    'Q400-NextGen',
                    'A320-200',
                    'CRJ-900-NextGen',
                    'A319-100',
                ],
            ],
            [
                'nome' => 'Flyair',
                'codigo' => 'FA',
                'aeronaves' => [
                    '737-700',
                    '737-800',
                    '737-MAX8',
                ],
            ],
            [
                'nome' => 'Vahana Indonesia',
                'codigo' => 'VI',
                'aeronaves' => [
                    '737-800',
                    'A330-300',
                    'A330-200',
                    '777-300ER',
                    'A330-900Neo',
                ],
            ],
            [
                'nome' => 'Challeng Air',
                'codigo' => 'CA',
                'aeronaves' => [
                    'D0328',
                    'ERJ175',
                    'A318',
                    '737-700',
                    '787-10',
                    'A340-300',
                ],
            ],
            [
                'nome' => 'Ryukyu By AJA',
                'codigo' => 'RBA',
                'aeronaves' => [
                    'A320Neo',
                    'A321Neo',
                    '797-800',
                ],
            ],
            [
                'nome' => 'Reis',
                'codigo' => 'RS',
                'aeronaves' => [
                    'ERJ135',
                    'ERJ175',
                    'ERJ190E2',
                    'E195E2',
                    '737-700',
                    '737-800',
                    '737-900ER',
                    '777-200ER',
                    '777-300ER',
                ],
            ],
            [
                'nome' => 'Royal Skyways',
                'codigo' => 'RSK',
                'aeronaves' => [
                    'Concorde',
                ],
            ],
            [
                'nome' => 'China Southern',
                'codigo' => 'CS',
                'aeronaves' => [
                    'ERJ190',
                    'C919',
                    'C909',
                    '737-800',
                    '737-MAX8',
                    '787-9',
                    'A350-900',
                ],
            ],
            [
                'nome' => 'Singapura Airline',
                'codigo' => 'SA',
                'aeronaves' => [
                    '777-300',
                    '737-800',
                    'A350-900',
                    '787-8',
                    '787-10',
                    '737-MAX8',
                    '737-MAX8-200',
                ],
            ],
            [
                'nome' => 'American Airways',
                'codigo' => 'AAW',
                'aeronaves' => [
                    'ERJ175',
                    'A321Neo',
                    'CRJ-700',
                    '787-8',
                    '737-MAX8',
                ],
            ],
            [
                'nome' => 'Air Qilin',
                'codigo' => 'AQ',
                'aeronaves' => [
                    'A321Neo',
                    '737-MAX8',
                    'C919',
                    '737-800',
                    'A319-100',
                    'A350-900',
                    '777-300',
                ],
            ],
            [
                'nome' => 'WAT',
                'codigo' => 'WAT',
                'aeronaves' => [
                    'DC3',
                    '727-200',
                    '757-200',
                    '307-Stratoliner',
                    '767-300',
                ],
            ],
            [
                'nome' => 'Asfar',
                'codigo' => 'AF',
                'aeronaves' => [
                    'A319-CJ',
                    'A320-200',
                    'A350-900',
                    '777-200ER',
                    '777-300',
                    '777-300ER',
                ],
            ],
            [
                'nome' => 'Unity',
                'codigo' => 'UN',
                'aeronaves' => [
                    '737-700',
                    'ERJ175',
                    '787-8',
                    '737-MAX8',
                    '737-800',
                    'ERJ170-LR',
                    '777-200ER',
                    'CRJ-700',
                    'A319-100',
                    'A321Neo',
                    'A320-200',
                    '787-10',
                    '787-9',
                    '737-MAX9',
                    '777-300ER',
                    '767-300',
                    '757-300',
                    '737-900ER',
                    '737-MAX10',
                ],
            ],
            [
                'nome' => 'PlayrionFestival',
                'codigo' => 'PF',
                'aeronaves' => [
                    'Q400',
                    'A220-300',
                    '787-8',
                ],
            ],
            [
                'nome' => 'RiyadhAir',
                'codigo' => 'RYA',
                'aeronaves' => [
                    'A321Neo',
                    'A350-1000',
                    '787-9',
                ],
            ],
            [
                'nome' => 'ITA Airways',
                'codigo' => 'ITA',
                'aeronaves' => [
                    'A220-300',
                    'A319-100',
                    'A320Neo',
                    'A321Neo',
                    'A330-900Neo',
                    'A350-900',
                ],
            ],
            [
                'nome' => 'LATAM',
                'codigo' => 'LT',
                'aeronaves' => [
                    'A319-100',
                    'A320Neo',
                    '787-9',
                    '777-300ER',
                ],
            ],
        ];
    }
}
