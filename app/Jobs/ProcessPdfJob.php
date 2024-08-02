<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable; 
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Events\ProcessPdfEvent;
use App\Services\AzureCognitiveService;

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sid; 

    public function __construct($sid)
    {
        $this->sid = $sid;
    }

    public function handle()
    {
        $this->azureCognitiveService = app(AzureCognitiveService::class);
  
        try {
            $filePath = Cache::get('pdf_path_' . $this->sid);
            $pdfUrl = Storage::disk('public')->url($filePath);

            $getOperationLocation = $this->azureCognitiveService->getOperationLocation($pdfUrl);
            $getJsonData = $this->azureCognitiveService->getJsonData($getOperationLocation);

            Cache::put('pdf_data_' . $this->sid, $getJsonData, now()->addMinutes(1));
        }
        catch (\Exception $e) {  
            Log::error('Error in Process PDF job: ' . $e->getMessage());
        }
 
        event(new ProcessPdfEvent($this->sid));
    }
}