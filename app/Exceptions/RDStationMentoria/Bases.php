<?php

namespace App\Exceptions\RDStationMentoria;

class Bases
{
    private $RDStationMentoria;
    private $ref = "bases";

    public function __construct($RDStationMentoria)
    {
        $this->RDStationMentoria = $RDStationMentoria;
        $this->RDStationMentoria->setUrl($this->ref);
    }

    public function listBases($page = 1, $size = 20, $search = null)
    {
        $path = '/bases';
        $queryParams = ['page' => $page, 'size' => $size];
        if(!empty($search)){
            $queryParams["search"] = $search;//urlencode($search);
        }        
        if ($queryParams) {
            $path .= '?' . http_build_query($queryParams);
        }
        //dd($path,$this->RDStationMentoria->request('GET', $path));
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function getBaseById($id)
    {
        $path = '/bases/' . urlencode($id);
        return $this->RDStationMentoria->request('GET', $path);
    }
        
    public function createBase($data)
    {
        $path = '/bases';
        return $this->RDStationMentoria->request('POST', $path, $data);
    }

    // Você não tem permissão para acessar esse recurso.

    public function updateBase($id, $data)
    {
        $path = '/bases/' . urlencode($id);
        return $this->RDStationMentoria->request('PUT', $path, $data);
    }

    public function removeBase($id)
    {
        $path = '/bases/' . urlencode($id);
        return $this->RDStationMentoria->request('DELETE', $path);
    }
}
