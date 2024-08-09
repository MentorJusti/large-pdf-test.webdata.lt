<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PdfResults extends Component
{
    public $pdfData = [];
    public $tables = [];
    public $currentTable = 0;
    public $currentPage = 1;
    public $totalPages = 1;
    public $tablesPerPage = [];
    public $sid = '';
    public $loading = false;

    public function mount()
    {
        $this->sid = session()->getId();
    }

    public function boot()
    {
        $this->sid = session()->getId();
    }

    #[On('PdfScanningStarted')]
    public function loadSpinner()
    {
        $this->loading = true;
    }

    #[On('LoadPdfData')]
    public function loadPdfResult($pdfData)
    {
        $this->pdfData = $pdfData;
        $this->totalPages = Cache::get('pdf_pages_' . $this->sid, 1);

        $this->loading = false;

        $this->processTables();
    }

    protected function processTables()
    {
        $this->tablesPerPage = array_fill(1, $this->totalPages, []);

        foreach ($this->pdfData as $tableIndex => $table) {
            $tablePages = [];

            foreach ($table['cells'] as $cell) {
                $pageNumber = $cell['boundingRegions'][0]['pageNumber'];
                if (!in_array($pageNumber, $tablePages)) {
                    $tablePages[] = $pageNumber;
                }
            }

            foreach ($tablePages as $page) {
                $this->tablesPerPage[$page][] = [
                    'headers' => $this->getHeaders($table['cells']),
                    'rows' => $this->processRows($table['cells']),
                ];
            }
        }

        $this->setCurrentPageTables();
    }

    protected function processRows($cells)
    {
        $rows = [];

        foreach ($cells as $cell) {
            if (!isset($cell['kind']) || $cell['kind'] === 'data') {
                $rowIndex = $cell['rowIndex'];
                $columnIndex = $cell['columnIndex'];

                if (!isset($rows[$rowIndex])) {
                    $rows[$rowIndex] = array_fill(0, max(array_column($cells, 'columnIndex')) + 1, '&nbsp;');
                }

                $rows[$rowIndex][$columnIndex] = $cell['content'];
            }
        }

        return $rows;
    }

    protected function getHeaders($cells)
    {
        $headers = ['columnHeaders' => [], 'rowHeaders' => []];

        foreach ($cells as $cell) {
            if (isset($cell['kind']) && $cell['kind'] === 'columnHeader') {
                if ($cell['rowIndex'] === 0) {
                    $headers['columnHeaders'][] = $cell;
                }
                else {
                    $headers['rowHeaders'][] = $cell;
                }
            }
        }

        return $headers;
    }

    protected function setCurrentPageTables()
    {
        $this->tables = $this->tablesPerPage[$this->currentPage] ?? [];
    }

    public function goToPreviousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->setCurrentPageTables();
        }
    }

    public function goToNextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->setCurrentPageTables();
        }
    }

    public function render()
    {
        return view('livewire.pdf-results', [
            'tables' => $this->tables,
            'currentPage' => $this->currentPage,
            'totalPages' => $this->totalPages
        ]);
    }
}
