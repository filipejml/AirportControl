<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('veiculos');
    }

    public function down(): void
    {
        // Não é possível recriar a tabela antiga, então deixamos vazio
    }
};