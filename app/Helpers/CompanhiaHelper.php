<?php
// app/Helpers/CompanhiaHelper.php

namespace App\Helpers;

class CompanhiaHelper
{
    private static $codigos = [
        // Códigos existentes
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
        'VAII' => 'Vintage Airlines II',
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
        'CN' => 'CloudNine',
        
        // NOVOS CÓDIGOS ADICIONADOS
        'SG' => 'Singapura Airlines',      // Código para Singapura Airlines
        'AM' => 'American Airways',        // Código para American Airways
        'CZ' => 'China Southern',          // Código para China Southern
        'PF' => 'PayrionFestival',         // Código para PayrionFestival
        'RS' => 'Royal Skyways',           // Código para Royal Skyways
        'RX' => 'Riyadh Air',              // Código para Riyadh Air
        'AS' => 'Asfar',                   // Código para Asfar
        'RE' => 'Reis',                    // Código para Reis
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
     * Retorna o mapa completo de códigos => nomes
     * NOVO MÉTODO
     */
    public static function getCodigosMap()
    {
        return self::$codigos;
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
    
    /**
     * Busca companhia por código (incluindo correspondência aproximada)
     */
    public static function buscarCompanhiaPorCodigo($codigo)
    {
        // Busca exata
        if (isset(self::$codigos[$codigo])) {
            return self::$codigos[$codigo];
        }
        
        // Busca case-insensitive
        $codigoUpper = strtoupper($codigo);
        foreach (self::$codigos as $key => $nome) {
            if (strtoupper($key) === $codigoUpper) {
                return $nome;
            }
        }
        
        return null;
    }

    /**
     * Busca código da companhia por nome (case-insensitive)
     */
    public static function buscarCodigoPorNome($nome)
    {
        if (!$nome) {
            return null;
        }

        $nomeUpper = strtoupper(trim($nome));

        foreach (self::$codigos as $codigo => $nomeTabela) {
            if (strtoupper($nomeTabela) === $nomeUpper) {
                return $codigo;
            }
        }

        return null;
    }
}