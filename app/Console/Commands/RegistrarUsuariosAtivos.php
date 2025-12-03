<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ControleUsuario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistrarUsuariosAtivos extends Command
{
    protected $signature = 'usuarios:registrar';
    protected $description = 'Registra o total de usuários habilitados no dia';

    public function handle(): void
    {
        $hoje = Carbon::today()->toDateString();

        $quantidade = DB::table('usuarios')
        ->where('status', 'ativo')
        ->where('department_id', '!=', 6)
        ->count();

        ControleUsuario::updateOrCreate(
            ['data' => $hoje],
            ['usuarios_habilitados' => $quantidade]
        );

        $this->info("Registrado {$quantidade} usuários habilitados em {$hoje}");
    }
}
