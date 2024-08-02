<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Chat\DALLE3;
use App\Models\Models;

class AzureCognitiveService
{
    protected $apiKey;

    public function __construct()
    { 
        $this->apiKey = env('AZURE_COGNITIVE_API_KEY');
    }

    public function getOperationLocation($source)
    {
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://di23-test.cognitiveservices.azure.com/formrecognizer/documentModels/prebuilt-document:analyze?api-version=2023-07-31', [
            'urlSource' => $source,
        ]);

        if ($response->successful()) {
            return $response->header('Operation-Location');
        }
        else {
            Log::error('Error starting analysis: ' . $response->json());
        }

        return null;
    }

    public function getJsonData($operationLocation)
    {
        while (true) {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($operationLocation);
    
            if ($response->successful()) {
                $jsonData = $response->json();
    
                if ($jsonData['status'] === 'running') {
                    sleep(1);
                } 
                else {
                    return $jsonData['analyzeResult']['tables'];
                }
            }
            else {
                Log::error('Error fetching JSON data: ' . $response->json());

                break;
            }
        }
    
        return null;
    }
}