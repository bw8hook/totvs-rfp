<?php

namespace App\Http\Controllers;

use App\Models\LineOfProduct;

use Illuminate\Http\Request;

class LineOfProductController extends Controller
{
    public function index()
    {
        $Itens = LineOfProduct::all();
        return view('line_of_products.index', compact('Itens'));
    }


    public function filter(Request $request)
    {
        // Definir o parâmetro de ordenação (padrão: mais recente primeiro)
        $orderBy = $request->get('sort_order', 'id_desc');

        // Iniciar a query
        $query = LineOfProduct::query();
        
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
        $lineOfProducts = LineOfProduct::all();
    
        return view('line_of_products.create', compact('lineOfProducts'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        try {
            LineOfProduct::create($validatedData);

            return redirect()->route('line-of-products.index')->with('success', 'Linha de Produto criado com sucesso.');
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('line-of-products.index')->with('error', 'Linha de Prod.');
        }
    }


    public function edit($id)
    {
        $lineOfProducts = LineOfProduct::findOrFail($id);

        $data = array(
            'lineOfProducts' => $lineOfProducts,
            'id' => $id,
        );

        return view('line_of_products.edit')->with($data);

    }

    public function update(Request $request, $id)
    {
        $lineOfProducts = LineOfProduct::findOrFail($id);

        $validatedData = $request->validate(['name' => 'required', 'status' => 'required']);

        $lineOfProducts->update($validatedData);

        return redirect()->route('line-of-products.index')->with('success', 'Linha de Produto atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $lineOfProducts = LineOfProduct::findOrFail($id);
        $lineOfProducts->delete();

        return redirect()->route('line-of-products.index')->with('success', 'Linha de Produto excluído com sucesso.');
    }
}
