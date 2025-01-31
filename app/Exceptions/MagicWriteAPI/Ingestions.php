<?php

namespace App\Exceptions\MagicWriteAPI;

use Illuminate\Support\Facades\Http;

use GlobalFuncoes;

class Ingestions {
    private $magicWriteAPI;

    public function __construct($magicWriteAPI) {
        $this->magicWriteAPI = $magicWriteAPI;
    }

    public function listIngestions($limit = 50, $search = null, $status = null) {
        $path = 'rest/ingestions';

        $queryParams = [];
        if ($search) { $queryParams['search'] = $search; } //":".$search; } //json_encode([":".$search]); }
        if ($status) { $queryParams['status'] = $status; }
        if ($limit) { $queryParams['limit'] = $limit; }
        if (!empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        //dd($path,$this->magicWriteAPI->request('GET', $path),'https://api.magicwrite.ai/rest/ingestions?limit=50&cursor=testeste&search=teste&status=succeeded');
        return $this->magicWriteAPI->request('GET', $path);
    }

    public function getIngestion($ingestionId) {
        $path = 'rest/ingestions/' . urlencode($ingestionId);
        return $this->magicWriteAPI->request('GET', $path);
    }
    public function updateIngestion($ingestionId,$parameters = []) {
        $path = 'rest/ingestions/' . urlencode($ingestionId);
        return $this->magicWriteAPI->request('PUT', $path, $parameters);
    }
    public function createIngestion($parameters = []) {
        $path = 'rest/ingestions';
        return $this->magicWriteAPI->request('POST', $path, $parameters);
    }    
    public function deleteIngestion($ingestionId) {
        $path = 'rest/ingestions/' . urlencode($ingestionId);
        return $this->magicWriteAPI->request('DELETE', $path);
    }    
    /*
    public function createIngestion($type='file', $url="", $title="", $description="Recebido API ", $ext="") {
        $path = 'rest/ingestions';
        $parameters = [  'sources'=>[], 'description' => 'VIA API - '.date("Y_M_D_H_i") ];
        if($type=='file'){
            $parameters['sources'][]=[
                'type' => 'file',
                'file' => [
                    'url' => $url,
                    'title' => $title,
                    'ext' => $ext,
                    'description' => $description,
                ],
            ];
        }
        //dd($parameters);
        return $this->magicWriteAPI->request('POST', $path, $parameters);
    }
    */       
    
}
