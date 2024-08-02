<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class PdfDisplay extends Component
{
    public $pdfUrl = null;

    #[On('loadPdfDisplay')]
    public function loadPdfResult($pdfUrl)
    {
        $this->pdfUrl = $pdfUrl;
    }

    public function render()
    {
        return view('livewire.pdf-display');
    }
}
