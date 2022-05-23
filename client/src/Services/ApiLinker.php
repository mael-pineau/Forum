<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiLinker
{

    private $baseURL = 'http://localhost:8000/api/';
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function createData($url, $data)
    {
        // ----------------
        // Process

        // Call Api
        $response = $this->client->request('POST', $this->baseURL . $url, [
            'body' => $data
        ]);
        $content = $response->getContent();

        return $content;
    }

    public function readData($url, $data = null)
    {
        // ----------------
        // Process

        // Call Api
        $response = $this->client->request('GET', $this->baseURL . $url, [
            'body' => $data
        ]);
        $content = $response->getContent();

        return $content;
    }

    public function updateData($url, $data)
    {
        // ----------------
        // Process

        // Call Api
        $response = $this->client->request('PUT', $this->baseURL . $url, [
            'body' => $data
        ]);
        $content = $response->getContent();

        return $content;
    }

    public function deleteData($url)
    {
        // ----------------
        // Process

        // Call Api
        $response = $this->client->request('DELETE', $this->baseURL . $url);
        $content = $response->getContent();

        return $content;
    }
}
