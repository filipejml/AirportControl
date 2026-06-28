<?php

namespace Tests\Unit;

use App\Helpers\CompanhiaHelper;
use PHPUnit\Framework\TestCase;

class CompanhiaHelperTest extends TestCase
{
    public function test_codigos_consolidados_das_companhias(): void
    {
        $this->assertSame('Unity', CompanhiaHelper::getNomeCompanhia('UA'));
        $this->assertSame('American Airways', CompanhiaHelper::getNomeCompanhia('AAW'));
        $this->assertSame('Royal Skyways', CompanhiaHelper::getNomeCompanhia('RX'));

        $this->assertSame('UA', CompanhiaHelper::buscarCodigoPorNome('Unity'));
        $this->assertSame('AAW', CompanhiaHelper::buscarCodigoPorNome('American Airways'));
        $this->assertSame('RX', CompanhiaHelper::buscarCodigoPorNome('Royal Skyways'));
    }

    public function test_codigos_antigos_ou_conflitantes_nao_sao_aceitos(): void
    {
        $this->assertFalse(CompanhiaHelper::isCodigoValido('UN'));
        $this->assertNotSame('Royal Skyways', CompanhiaHelper::getNomeCompanhia('RS'));
        $this->assertNotSame('Riyadh Air', CompanhiaHelper::getNomeCompanhia('RX'));
    }
}
