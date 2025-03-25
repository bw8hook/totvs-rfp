<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Agent;
use Illuminate\Http\Request;

class AgentsController extends Controller
{
   
    public function index()
    {
        $Itens = Agent::all();
        return view('agents.index', compact('Itens'));
    }


    public function filter(Request $request)
    {
        // Definir o parâmetro de ordenação (padrão: mais recente primeiro)
        $orderBy = $request->get('sort_order', 'id_desc');

        // Iniciar a query
        $query = Agent::query();
        
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
        $agents = Agent::all();
    
        return view('agents.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'agent_name' => 'required',
            'search_engine' => 'required',
            'status' => 'required',
        ]);

        try {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer dataset-XSIGaQdZXZdDLux237SDv7s9',
                    'Content-Type' => 'application/json'
                ])->post('https://totvs-ia.hook.app.br/v1/datasets', [
                    'name' => $request->agent_name,
                    'description' => 'Base de conhecimento criada via TOTVS SMART RFP - API',
                    'permission' => 'all_team_members',
                    'indexing_technique' => 'high_quality'                    
                ]);
            
                if ($response->successful()) {
                    $data = $response->json();
                    $validatedData['knowledge_id_hook'] = $data['id'];
                    $CriarAgente = Agent::create($validatedData);

                    return redirect()->route('agents.index')->with('success', 'Agente criado com sucesso.');
                }
            
            } catch (\Exception $e) {
                return redirect()->route('agents.index')->with('error', 'Erro ao criar Agente. - '. $e->getMessage());
            }
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('agents.index')->with('error', 'Erro ao criar Agente.');
        }
    }


    public function edit($id)
    {
        $agents = Agent::findOrFail($id);

        $data = array(
            'agents' => $agents,
            'id' => $id,
        );

        return view('agents.edit')->with($data);

    }

    public function update(Request $request, $id)
    {
        $lineOfProducts = Agent::findOrFail($id);

        $validatedData = $request->validate(['name' => 'required', 'search_engine' => 'required', 'status' => 'required']);

        $lineOfProducts->update($validatedData);

        return redirect()->route('agents.index')->with('success', 'Agente atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $lineOfProducts = Agent::findOrFail($id);
        $lineOfProducts->delete();

        return redirect()->route('agents.index')->with('success', 'Agente excluído com sucesso.');
    }
}
