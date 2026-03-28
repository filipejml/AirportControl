<?php
// database/seeders/UpdateCompanhiaCodigosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanhiaAerea;
use App\Helpers\CompanhiaHelper;

class UpdateCompanhiaCodigosSeeder extends Seeder
{
    public function run()
    {
        $codigosMap = CompanhiaHelper::getCodigosMap();
        $updated = 0;
        $created = 0;
        
        $this->command->info("\nAtualizando códigos das companhias...\n");
        
        foreach ($codigosMap as $codigo => $nomeHelper) {
            // Buscar companhia pelo nome exato primeiro
            $companhia = CompanhiaAerea::where('nome', $nomeHelper)->first();
            
            // Se não encontrar, tentar busca aproximada (case-insensitive)
            if (!$companhia) {
                $companhia = CompanhiaAerea::where('nome', 'LIKE', "%{$nomeHelper}%")->first();
            }
            
            if ($companhia) {
                // Companhia encontrada
                if (is_null($companhia->codigo)) {
                    $companhia->codigo = $codigo;
                    $companhia->save();
                    $updated++;
                    $this->command->info("✓ [ATUALIZADO] {$companhia->nome} -> Código: {$codigo}");
                } elseif ($companhia->codigo !== $codigo) {
                    $this->command->warn("⚠ [CONFLITO] {$companhia->nome} tem código {$companhia->codigo}, mas deveria ser {$codigo}");
                } else {
                    $this->command->line("  [OK] {$companhia->nome} já possui código: {$codigo}");
                }
            } else {
                // Companhia não encontrada, criar nova?
                $this->command->warn("✗ [NÃO ENCONTRADA] {$nomeHelper} (código: {$codigo})");
                
                // Opcional: criar automaticamente
                // $companhia = CompanhiaAerea::create([
                //     'nome' => $nomeHelper,
                //     'codigo' => $codigo
                // ]);
                // $created++;
                // $this->command->info("✓ [CRIADA] {$nomeHelper} -> Código: {$codigo}");
            }
        }
        
        // Listar companhias que não têm código no helper
        $this->command->info("\n" . str_repeat('-', 50));
        
        $companhiasSemCodigo = CompanhiaAerea::whereNull('codigo')->get();
        if ($companhiasSemCodigo->count() > 0) {
            $this->command->error("\n⚠  COMPANHIAS SEM CÓDIGO:");
            foreach ($companhiasSemCodigo as $companhia) {
                $this->command->error("  - {$companhia->nome} (ID: {$companhia->id})");
            }
            $this->command->info("\nPara corrigir manualmente, execute:");
            $this->command->info("  php artisan tinker");
            $this->command->info("  \$c = App\\Models\\CompanhiaAerea::find(ID);");
            $this->command->info("  \$c->codigo = 'CODIGO';");
            $this->command->info("  \$c->save();");
        } else {
            $this->command->info("✓ Todas as companhias têm código!");
        }
        
        $this->command->info("\n" . str_repeat('-', 50));
        $this->command->info("Resumo:");
        $this->command->info("  - Companhias atualizadas: {$updated}");
        if ($created > 0) {
            $this->command->info("  - Companhias criadas: {$created}");
        }
        $this->command->info("  - Total de códigos mapeados: " . count($codigosMap));
        
        if ($updated > 0) {
            $this->command->info("\n✅ Seeder executado com sucesso!");
        }
    }
}