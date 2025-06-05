<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use App\Models\Configs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PSpell\Config;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $UsersActives = User::where('status', "ativo")
            ->where('departament_id', '!=', 6)
            ->count();

        $Configs = Configs::where('id', 1)->first();

        $AgentsActives = Agent::where('status', "ativo")
            ->count();
  
        $Array = array();
        $Array['UsersAtivos'] = $UsersActives;
        $Array['UsersConta'] = $Configs->users_liberado;
        $Array['AgentesAtivos'] = $AgentsActives;
        $Array['AgentesConta'] = $Configs->agentes_ativos;;
        $Array['inicio'] =  Carbon::now()->startOfMonth()->format('Y-m-d');;
        $Array['fim'] = Carbon::now()->endOfMonth()->format('Y-m-d');

        return view('config')->with($Array);
    }


    public function dadosPorPeriodo(Request $request)
    {
        $inicio = Carbon::parse($request->input('inicio'));
        $fim = Carbon::parse($request->input('fim'));

        // Chamada à API
        $response = Http::get("https://rfp.hook.app.br/api/usage/app/07e05c04-28fa-4d48-b2fc-44088923e97b", [
            'startDate' => $inicio->toDateString(),
            'endDate' => $fim->toDateString(),
            'graph' => true
        ]);

        $apiData = $response->json();
        

        // Criar mapa de data => valor vindo da API
        $valores = collect($apiData['dates'])->mapWithKeys(function ($date, $index) use ($apiData) {
            return [Carbon::parse($date)->toDateString() => (int)$apiData['values'][$index]];
        });

        // Gerar todas as datas entre início e fim
        $todasAsDatas = [];
        $valoresCompletos = [];

        for ($date = $inicio->copy(); $date->lte($fim); $date->addDay()) {
            $isoDate = $date->toDateString(); // ex: 2025-05-05
            $brDate = $date->format('d/m/Y'); // ex: 05/05/2025

            $todasAsDatas[] = $brDate;
            $valoresCompletos[] = $valores->get($isoDate, 0); // se não houver valor, assume 0
        }

        return response()->json([
            'dates' => $todasAsDatas,
            'values' => $valoresCompletos,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
