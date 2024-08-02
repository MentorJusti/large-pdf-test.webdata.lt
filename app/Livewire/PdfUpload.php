<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Jobs\ProcessPdfJob;
use App\Events\ProcessPdfEvent;

class PdfUpload extends Component
{
    use WithFileUploads;

    #[Rule('required|file|mimes:pdf|max:10240')]
    public $pdf;
    public $path;
    public $sid = '';

    public function mount()
    {
        $this->sid = session()->getId();
    }

    public function boot()
    {
        $this->sid = session()->getId();
    }

    public function uploadFile()
    {
        $this->validate();

        $fileName = Str::random(40) . '.' . $this->pdf->getClientOriginalExtension();

        $this->path = $this->pdf->storeAs('pdfs', $fileName, 'public');

        Cache::put('pdf_path_' . $this->sid, $this->path, now()->addMinutes(1));

        ProcessPdfJob::dispatch($this->sid);

        $this->dispatch('loadPdfDisplay', $this->path);
    }

    #[On('echo:pdf-channel.{sid},ProcessPdfEvent')]
    public function checkPdfData()
    {
        $pdfData = Cache::get('pdf_data_' . $this->sid);

        $this->dispatch('LoadPdfData', $pdfData);
    }

    public function render()
    {
        return view('livewire.pdf-upload');
    }
}
