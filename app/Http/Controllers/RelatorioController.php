<?php
// app/Http/Controllers/RelatorioController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relatorio;

class RelatorioController extends Controller
{
    /**
     * LISTAGEM (ADMIN + USUÁRIO)
     */
    public function index()
    {
        if (auth()->user()->tipo == 0) {
            $relatorios = Relatorio::all();
        } else {
            $relatorios = Relatorio::where('visivel_usuario', true)->get();
        }

        return view('relatorios.index', compact('relatorios'));
    }

    /**
     * LISTAGEM PARA ADMIN (CONTROLE)
     */
    public function adminIndex()
    {
        $relatorios = Relatorio::all();
        return view('admin.relatorios.index', compact('relatorios'));
    }

    /**
     * FORM CREATE (ADMIN)
     */
    public function create()
    {
        return view('relatorios.create');
    }

    /**
     * SALVAR (ADMIN)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        // 🔥 TRATAMENTO DO CHECKBOX
        $data['visivel_usuario'] = $request->has('visivel_usuario');

        Relatorio::create($data);

        return redirect()->route('admin.relatorios.index')
            ->with('success', 'Relatório criado com sucesso!');
    }

    /**
     * FORM EDIT
     */
    public function edit(Relatorio $relatorio)
    {
        return view('relatorios.edit', compact('relatorio'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, Relatorio $relatorio)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $data['visivel_usuario'] = $request->has('visivel_usuario');

        $relatorio->update($data);

        return redirect()->route('admin.relatorios.index')
            ->with('success', 'Relatório atualizado!');
    }

    /**
     * DELETE
     */
    public function destroy(Relatorio $relatorio)
    {
        $relatorio->delete();

        return redirect()->route('admin.relatorios.index')
            ->with('success', 'Relatório removido!');
    }
}