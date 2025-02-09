<?php

namespace App\Services;

use App\Settings\GeneralSettings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

class AiService
{
    protected $client;
    protected $model;
    public function __construct(){
        $generalSettings = new GeneralSettings();

        $this->model = $generalSettings->ai_model;
        $this->client = new Client([
            'base_uri' => $generalSettings->ai_base_url,
            'timeout' => 120.0,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function predict($prompt, $data, $options): array
    {
        $requestBody = [
            'prompt' => $prompt,
            'model' => $this->model,
        ];

        if ($data) {
            $dataString = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            $requestBody['prompt'] = $prompt . "\n\n### **Prompt Data:**\n" . $dataString;
        }

        if ($options) {
            $requestBody = array_merge($requestBody, $options);
        }

        $response = $this->client->post('/api/generate', [
            'json' => $requestBody,
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}
