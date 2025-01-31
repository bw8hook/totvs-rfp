<?php

namespace App\Exceptions\MagicWriteAPI;

use Illuminate\Support\Facades\Http;

use GlobalFuncoes;

class Agents {
    
    private $magicWriteAPI;

    public function __construct($magicWriteAPI) {
        $this->magicWriteAPI = $magicWriteAPI;
    }    

    public function getPublicAgent($companyId, $agentId) {
        $path = 'rest/public/agents/' . urlencode($companyId) . '/' . urlencode($agentId);
        return $this->magicWriteAPI->request('GET', $path);
    }

    public function getAgent($agentId) {
        $path = 'rest/agents/' . urlencode($agentId);
        return $this->magicWriteAPI->request('GET', $path);
    }

    public function listAgents($status = null) {
        $path = 'rest/agents';
        if ($status) {
            $path .= '?' . http_build_query(['status' => $status]);
        }
        return $this->magicWriteAPI->request('GET', $path);
    }
    public function executeAgent($companyId, $agentId, $history, $input) {
        $path = 'rest/public/agents/' . urlencode($companyId) . '/' . urlencode($agentId);
        $parameters = [ 'history' => $history,'input' => $input ];
        return $this->magicWriteAPI->request('POST', $path, $parameters);
    }
    public function listPublicAgents($companyId, $limit = null) {
        $path = 'rest/public/agents/' . urlencode($companyId);
        if ($limit) {
            $path .= '?' . http_build_query(['limit' => $limit]);
        }
        return $this->magicWriteAPI->request('GET', $path);
    }

    public function getCurrentCompany() {
        $path = 'rest/companies/current';
        return $this->magicWriteAPI->request('GET', $path);
    }

    public function updateAgents($agentId,$parameters = []) {
        $path = 'rest/agents/' . urlencode($agentId);
        return $this->magicWriteAPI->request('PUT', $path, $parameters);
    }    

}