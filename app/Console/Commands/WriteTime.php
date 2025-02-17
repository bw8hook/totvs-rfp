<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class WriteTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timestamp = Carbon::now()->toDateTimeString();
        $content = "Debug timestamp: $timestamp\n";

        Storage::append('debug_timestamps.log', $content);

        $this->info("Timestamp written to debug_timestamps.log");
    }
}
