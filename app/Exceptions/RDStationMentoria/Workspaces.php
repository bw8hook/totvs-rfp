<?php

namespace App\Exceptions\RDStationMentoria;

class Workspaces
{
    private $RDStationMentoria;
    private $ref = "meuassistente";

    public function __construct($RDStationMentoria)
    {
        $this->RDStationMentoria = $RDStationMentoria;
        $this->RDStationMentoria->setUrl($this->ref);
    }

    public function getCurrentWorkspace()
    {
        $path = '/workspaces/current';
        return $this->RDStationMentoria->request('GET', $path);
    }
}