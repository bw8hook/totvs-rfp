<?php

namespace App\Exceptions\RDStationMentoria;

class User
{
    private $RDStationMentoria;
    private $ref="contas";

    public function __construct($RDStationMentoria)
    {
        $this->RDStationMentoria = $RDStationMentoria;
        $this->RDStationMentoria->setUrl($this->ref);
    }

    public function getCurrentUser()
    {
        $path = '/users/current';
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function updateProfile($data)
    {
        $path = '/users/current';
        return $this->RDStationMentoria->request('PUT', $path, $data);
    }

    public function changePassword($data)
    {
        $path = '/users/credentials';
        return $this->RDStationMentoria->request('PUT', $path, $data);
    }

    public function sendRecoveryLink($data)
    {
        $path = '/users/recovery';
        return $this->RDStationMentoria->request('POST', $path, $data);
    }

    public function recoverPassword($data)
    {
        $path = '/users/recover';
        return $this->RDStationMentoria->request('POST', $path, $data);
    }
}
