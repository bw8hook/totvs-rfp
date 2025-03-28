<?php

namespace App\Console\Commands;

use App\Models\ProjectFiles;
use App\Models\ProjectRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateProcessedProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-processed-project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update processed project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $ProjectFiles = ProjectFiles::where('status', "em processamento")->get();
                foreach ($ProjectFiles as $File) {
                    $Records = ProjectRecord::where('project_records.project_file_id', $File->id)
                        ->where('project_records.status', "processando")
                        ->count();
            
                        if($Records == 0){
                            $File->status = 'processado';
                            $File->save();
                        }
                }
            Log::info("Executado com sucesso");
        } catch (\Exception $e) {
            Log::error("Erro no processamento: " . $e->getMessage());
        }
    }
}
