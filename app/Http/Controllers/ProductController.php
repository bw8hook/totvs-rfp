<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\LineOfProduct;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $lineOfProducts = LineOfProduct::all();
        $serviceGroups = ServiceGroup::all();
        return view('products.create', compact('lineOfProducts', 'serviceGroups'));
    }

    public function store(Request $request)
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

        Product::create($validatedData);

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $lineOfProducts = LineOfProduct::all();
        $serviceGroups = ServiceGroup::all();
        return view('products.edit', compact('product', 'lineOfProducts', 'serviceGroups'));
    }

    public function update(Request $request, Product $product)
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

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }
}
