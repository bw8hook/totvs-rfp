<?php

namespace App\Exceptions\MagicWriteAPI;

use Illuminate\Support\Facades\Http;

use GlobalFuncoes;
class Completions {
    
    private $magicWriteAPI;

    public function __construct($magicWriteAPI) {
        $this->magicWriteAPI = $magicWriteAPI;
    }    

    public function create($parameters = []) {
        return $this->magicWriteAPI->request('POST', 'rest/completions', $parameters);
    }

    public function createWithPromptID($promptID="", $parameters = []) {
        $path = 'rest/completions/' . urlencode($promptID);
        return $this->magicWriteAPI->request('POST', $path, $parameters);
    }
    public function getWithPromptID($promptID, $parameters = []) { //{limit: '20', cursor: 'null', search: 'nulll', promptID: '123'}
        $path = 'rest/completions/' . urlencode($promptID) . '?' . http_build_query($parameters);
        return $this->magicWriteAPI->request('GET', $path);
    }
    public function deleteWithPromptIDAndCompletionID($promptID, $completionID) {
        $path = 'rest/completions/' . urlencode($promptID) . '/' . urlencode($completionID);
        return $this->magicWriteAPI->request('DELETE', $path);
    }
}
