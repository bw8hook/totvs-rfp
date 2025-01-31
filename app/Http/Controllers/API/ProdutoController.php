<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // Retornar todos os produtos
    public function index()
    {
        return response()->json(Produto::all());
    }

    // Armazenar um novo produto
    public function store(Request $request)
    {
        $produto = Produto::create($request->all());
        return response()->json($produto, 201);
    }

    // Mostrar um produto específico
    public function show($id)
    {
        $produto = Produto::findOrFail($id);
        return response()->json($produto);
    }

    // Atualizar um produto existente
    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);
        $produto->update($request->all());
        return response()->json($produto);
    }

    // Deletar um produto
    public function destroy($id)
    {
        Produto::destroy($id);
        return response()->json(null, 204);
    }
}
