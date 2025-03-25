<?php

namespace App\Http\Controllers;

use App\Models\Segments;
use Illuminate\Http\Request;

class SegmentsController extends Controller
{
    public function index()
    {
        $Itens = Segments::all();
        return view('segments.index', compact('Itens'));
    }


    public function filter(Request $request)
    {
        // Definir o parâmetro de ordenação (padrão: mais recente primeiro)
        $orderBy = $request->get('sort_order', 'id_desc');

        // Iniciar a query
        $query = Segments::query();
        
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
        $Segments = Segments::all();
        return view('segments.create', compact('Segments'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        try {
            Segments::create($validatedData);

            return redirect()->route('segments.index')->with('success', 'Segmento criado com sucesso.');
        } catch (\Throwable $th) {
            return redirect()->route('segments.index')->with('error', 'Erro ao Salvar Segmento.');
        }
    }


    public function edit($id)
    {
        $Segment = Segments::findOrFail($id);

        $data = array(
            'Segment' => $Segment,
            'id' => $id,
        );

        return view('segments.edit')->with($data);

    }

    public function update(Request $request, $id)
    {
        $lineOfProducts = Segments::findOrFail($id);

        $validatedData = $request->validate(['name' => 'required', 'status' => 'required']);

        $lineOfProducts->update($validatedData);

        return redirect()->route('segments.index')->with('success', 'Segmento atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $lineOfProducts = Segments::findOrFail($id);
        $lineOfProducts->delete();

        return redirect()->route('segments.index')->with('success', 'Segmento excluído com sucesso.');
    }
}
