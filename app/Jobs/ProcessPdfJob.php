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
use Smalot\PdfParser\Parser;

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sid;
    protected $parser;

    public function __construct($sid)
    {
        $this->sid = $sid;
        $this->parser = new Parser();
    }

    public function handle()
    {
        $this->azureCognitiveService = app(AzureCognitiveService::class);
  
        try {
            Log::info('PDF Processing Started!');

            $filePath = Cache::get('pdf_path_' . $this->sid);
            $pdfUrl = Storage::disk('public')->url($filePath);

            $details = $this->parser->parseFile($pdfUrl)->getDetails();

            $allJsonData = [];

            for ($start = 1; $start <= $details['Pages']; $start += 2) {
                $end = $start + 1;
                $pageRange = "$start-$end";

                Log::info('Processing page range: ' . $pageRange);

                $getOperationLocation = $this->azureCognitiveService->getOperationLocation($pdfUrl, $pageRange);
                $getJsonData = $this->azureCognitiveService->getJsonData($getOperationLocation);

                if (is_array($getJsonData)) {
                    $allJsonData = array_merge($allJsonData, $getJsonData);
                }
            }

            Cache::put('pdf_data_' . $this->sid, $allJsonData, now()->addMinutes(1));
            Cache::put('pdf_pages_' . $this->sid, $details['Pages'], now()->addMinutes(1));

            Log::info('PDF Processing Finished!');
        }
        catch (\Exception $e) {
            Log::error('Error in Process PDF job: ' . $e->getMessage());
        }

        event(new ProcessPdfEvent($this->sid));
    }
}
