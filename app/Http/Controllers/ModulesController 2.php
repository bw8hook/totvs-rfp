<?php

namespace App\Http\Controllers;
use App\Models\Module;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    
    public function index()
    {
        $Itens = Module::all();
        return view('modules.index', compact('Itens'));
    }


    public function filter(Request $request)
    {
        // Definir o parâmetro de ordenação (padrão: mais recente primeiro)
        $orderBy = $request->get('sort_order', 'id_desc');

        // Iniciar a query
        $query = Module::query();
        
        // Aplicar ordenação
        switch ($orderBy) {
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
        }

        // Paginação (40 por página)
        $items = $query->paginate(40);

        // Retornar dados como JSON
        return response()->json($items);
    }

    public function create()
    {
        $modules = Module::all();
        return view('modules.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        try {
            Module::create($validatedData);

            return redirect()->route('modules.index')->with('success', 'Módulo criado com sucesso.');
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('modules.index')->with('error', 'Erro ao criar o Módulo.');
        }
    }


    public function edit($id)
    {
        $module = Module::findOrFail($id);

        $data = array(
            'module' => $module,
            'id' => $id,
        );

        return view('modules.edit')->with($data);

    }

    public function update(Request $request, $id)
    {
        $Module = Module::findOrFail($id);

        $validatedData = $request->validate(['name' => 'required', 'status' => 'required']);

        $Module->update($validatedData);

        return redirect()->route('modules.index')->with('success', 'Módulo atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $Module = Module::findOrFail($id);
        $Module->delete();

        return redirect()->route('modules.index')->with('success', 'Módulo excluído com sucesso.');
    }
}
