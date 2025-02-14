<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Processa os registros enviados, separa em arquivos e sobe no AWS S3
Schedule::command('knowledgebase:process')->everyMinute();

// Pega os arquivos processados e envia para a BASE de CONHECIMENTO do  MENTORIA
Schedule::command('app:upload-knowledge-base')->everyMinute();

// Pega os arquivos processados e envia para a BASE de CONHECIMENTO do  MENTORIA
Schedule::command('app:upload-project-to-answer')->everyMinute();


// Schedule::call(function () {
    
// })->everyMinute();

