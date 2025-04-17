<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Processa os registros enviados, separa em arquivos e sobe no AWS S3
Schedule::command('knowledgebase:process')->everyMinute()->withoutOverlapping(30);

// Processa os registros enviados corrigidos, separa em arquivos e sobe no AWS S3
Schedule::command('app:process-and-upload-expedition')->everyMinute()->withoutOverlapping(30);

// Pega os arquivos processados e envia para a BASE de CONHECIMENTO do MENTORIA e do OPEN IA
Schedule::command('app:upload-knowledge-base')->everyMinute()->withoutOverlapping(30);

// Pega os requisitos recebidos e envia para o MENTORIA para responder
Schedule::command('app:upload-project-to-answer')->everyMinute()->withoutOverlapping(30);

// Pega os requisitos recebidos e envia para o OPEN IA para responder
Schedule::command('app:upload-project-to-answer-hook')->everyTenMinutes()->withoutOverlapping(30);

// Pega os requisitos que já foram enviados e tenta envia novamente para o OPEN IA para responder
Schedule::command('app:upload-retry-records')->everyTenMinutes()->withoutOverlapping(expiresAt: 30);

// Valida se todos foram respondidos, e atualiza o status do projeto.
Schedule::command('app:update-processed-project')->everyThreeMinutes();

// Pega todos os projetos "concluídos" e cria um arquivo de download das resposta
Schedule::command('app:create-answered-file')->everyThreeMinutes();


