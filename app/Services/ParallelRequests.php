<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ParallelRequests
{
    protected $client;
    protected $requests = [];
    protected $results = [];

    public function __construct()
    {
        $this->client = new Client();
    }

    public function addRequest($method, $uri, $options = [], $headers = [], $body = null)
    {

        $options['headers'] = $headers;
        if ($body) {
            $options['json'] = $body;
        }
        $this->requests[] = new Request($method, $uri, $options);
    }



        
    public function execute($concurrency = 20)
    {
        $pool = new Pool($this->client, $this->requests, [
            'concurrency' => $concurrency,
            'fulfilled' => function ($response, $index) {
                $this->results[$index] = $response->getBody()->getContents();
            },
            'rejected' => function (RequestException $reason, $index) {
                $this->results[$index] = "Erro: " . $reason->getMessage();
                dd('erros');
                Log::error("Requisição {$index} falhou: " . $reason->getMessage());
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        return $this->results;
    }
}
