<?php

namespace Tests\Unit;

use App\Models\Voo;
use PHPUnit\Framework\TestCase;

class VooCodigoCompanhiaTest extends TestCase
{
    public function test_extrai_codigo_de_um_id_de_voo_valido(): void
    {
        $this->assertSame('LA', Voo::extrairCodigoCompanhia('LA-1234'));
        $this->assertSame('AAW', Voo::extrairCodigoCompanhia(' aaw-9876 '));
    }

    public function test_rejeita_id_de_voo_com_formato_invalido(): void
    {
        $this->assertNull(Voo::extrairCodigoCompanhia('LA1234'));
        $this->assertNull(Voo::extrairCodigoCompanhia('L-1234'));
        $this->assertNull(Voo::extrairCodigoCompanhia('LATAM-1234'));
    }
}
