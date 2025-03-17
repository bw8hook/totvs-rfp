<?php

namespace App\Http\Controllers;

use App\Models\LineOfProduct;
use App\Models\ServiceGroup;
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
        $serviceGroups = ServiceGroup::all();
        return view('line_of_products.create', compact('lineOfProducts', 'serviceGroups'));
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

    public function show(LineOfProduct $product)
    {
        return view('line_of_products.show', compact('product'));
    }

    public function edit(LineOfProduct $product)
    {
        $lineOfProducts = LineOfProduct::all();
        $serviceGroups = ServiceGroup::all();
        return view('line_of_products.edit', compact('product', 'lineOfProducts', 'serviceGroups'));
    }

    public function update(Request $request, LineOfProduct $product)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'name' => 'required',
            'category' => 'required',
            'service_group_id' => 'required|exists:rfp_service_groups,id',
            'hook_status' => 'required|boolean',
            'totvs_status' => 'required|in:Ativo,Descontinuado',
            'line_of_product_id' => 'required|exists:rfp_line_of_products,id',
            'inclusion_date' => 'required|date',
            'inactivation_date' => 'nullable|date',
        ]);

        $product->update($validatedData);

        return redirect()->route('line-of-products.index')->with('success', 'Linha de Produto atualizado com sucesso.');
    }

    public function destroy(LineOfProduct $product)
    {
        $product->delete();

        return redirect()->route('line-of-products.index')->with('success', 'Linha de Produto excluído com sucesso.');
    }
}
