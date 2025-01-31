<?php

namespace App\Exceptions\RDStationMentoria;

class Accounts {

    private $RDStationMentoria;
    private $ref="contas";

    public function __construct($RDStationMentoria)
    {
        $this->RDStationMentoria = $RDStationMentoria;
        $this->RDStationMentoria->setUrl($this->ref);
    }

    public function listAccounts() {
        $path = '/accounts';
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function setupAccount($data) {
        $path = '/accounts';
        return $this->RDStationMentoria->request('POST', $path, $data);
    }

    public function getAccountByPlatform($platform) {
        $path = '/accounts/platform:' . urlencode($platform);
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function checkAccountStatus($platform) {
        $path = '/accounts/check/' . urlencode($platform);
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function removeAccount($id) {
        $path = '/accounts/' . urlencode($id);
        return $this->RDStationMentoria->request('DELETE', $path);
    }

    public function getAccountCredentials($id, $workspaceId, $platform) {
        $path = '/admin/accounts/' . urlencode($id) . '/' . urlencode($workspaceId) . '/' . urlencode($platform);
        return $this->RDStationMentoria->request('GET', $path);
    }

}
