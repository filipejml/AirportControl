<?php
// app/Helpers/CompanhiaHelper.php

namespace App\Helpers;

class CompanhiaHelper
{
    private static $codigos = [
        'PL' => 'Prosperity Lines',
        'PP' => 'Pop! Airline',
        'FT' => 'Fast Travel',
        'GK' => 'Gluck Airlines',
        'AO' => 'Air Odysseia',
        'WW' => 'World Wide',
        'AA' => 'Alpha',
        'SK' => 'Skyways',
        'RA' => 'Ryoko Airlines',
        'ASY' => 'AraSky',
        'OB' => 'Outback',
        'JP' => 'Jurassic Pax',
        'VI' => 'Vahana Indonesia',
        'PN' => 'Península',
        'BV' => 'Bon Voyage',
        'OT' => 'Orient',
        'SPY' => 'Stellar Party',
        'AW' => 'Aerowings',
        'AK' => 'Air Kiwi',
        'VAII' => 'Vintage Airline II',
        'CA' => 'ChallengAir',
        'WAT' => 'WAT',
        'TAL' => 'TAL',
        'HW' => 'Hallowings',
        'KW' => 'Air Kiwi',
        'FA' => 'Flyair',
        'MAA' => 'Maasai Airways',
        'EX' => 'Evish Xmas',
        'SCA' => 'Santa Claus',
        'RBA' => 'Ryukyu by AJA',
        'CN' => 'CloudNine'
    ];

    /**
     * Verifica se um código de companhia é válido
     */
    public static function isCodigoValido($codigo)
    {
        return array_key_exists($codigo, self::$codigos);
    }

    /**
     * Retorna o nome da companhia pelo código
     */
    public static function getNomeCompanhia($codigo)
    {
        return self::$codigos[$codigo] ?? null;
    }

    /**
     * Retorna todos os códigos válidos
     */
    public static function getCodigosValidos()
    {
        return array_keys(self::$codigos);
    }

    /**
     * Extrai o código do ID do voo (ex: AA-1234 retorna AA)
     */
    public static function extrairCodigo($idVoo)
    {
        if (preg_match('/^([A-Z]{2,4})-/', $idVoo, $matches)) {
            return $matches[1];
        }
        return null;
    }
}