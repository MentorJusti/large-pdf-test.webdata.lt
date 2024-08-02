<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class PdfResults extends Component
{
    public $pdfData = [];
    public $tables = [];
    public $currentTable = 0;

    #[On('LoadPdfData')]
    public function loadPdfResult($pdfData)
    {
        $this->pdfData = $pdfData;
        $this->processTables();
    }

    protected function processTables()
    {
        $this->tables = [];

        foreach ($this->pdfData as $tableIndex => $table) {
            $rows = [];
            
            foreach ($table['cells'] as $cellIndex => $cell) {
                if (!isset($cell['kind']) || $cell['kind'] === 'data') {
                    $rowIndex = $cell['rowIndex'];
                    $columnIndex = $cell['columnIndex'];

                    if (!isset($rows[$rowIndex])) {
                        $rows[$rowIndex] = array_fill(0, $table['columnCount'], '&nbsp;');
                    }

                    $rows[$rowIndex][$columnIndex] = $cell['content'];
                }
            }

            $this->tables[] = [
                'headers' => $this->getHeaders($table['cells']),
                'rows' => $rows
            ];
        }
    }

    protected function getHeaders($cells)
    {
        $headers = ['columnHeaders' => [], 'rowHeaders' => []];

        foreach ($cells as $cellIndex => $cell) {
            if (isset($cell['kind']) && $cell['kind'] === 'columnHeader') {
                if ($cell['rowIndex'] === 0) {
                    $headers['columnHeaders'][] = $cell;
                } else {
                    $headers['rowHeaders'][] = $cell;
                }
            }
        }

        return $headers;
    }

    public function goToPreviousTable()
    {
        if ($this->currentTable > 0) {
            $this->currentTable--;
        }
    }

    public function goToNextTable()
    {
        if ($this->currentTable < count($this->tables) - 1) {
            $this->currentTable++;
        }
    }

    public function render()
    {
        return view('livewire.pdf-results', [
            'table' => $this->tables[$this->currentTable] ?? null,
            'currentTable' => $this->currentTable + 1,
            'numOfTables' => count($this->tables)
        ]);
    }
}
