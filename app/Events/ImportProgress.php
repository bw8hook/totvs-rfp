<?php
namespace App\Events;

class ImportProgress
{
    public $status;
    public $progress;

    public function __construct(string $status, int $progress)
    {
        $this->status = $status;
        $this->progress = $progress;
    }
}