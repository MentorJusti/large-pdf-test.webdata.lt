<div>
    <div class="card" style="max-width: 650px;">
        <div class="card-header" style="font-weight: bold;">
            Scanned Results
        </div>
        <div class="card-body" style="height: 580px; overflow-y: scroll;">
            @if ($table)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach ($table['headers']['columnHeaders'] as $header)
                                    <th colspan="{{ $header['columnSpan'] ?? 1 }}" rowspan="{{ $header['rowSpan'] ?? 1 }}">
                                        {{ $header['content'] }}
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($table['headers']['rowHeaders'] as $header)
                                    <th>
                                        {{ $header['content'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($table['rows'] as $row)
                                <tr>
                                    @foreach ($row as $cellContent)
                                        <td>
                                            {!! $cellContent !!}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No results...</p>
            @endif
        </div>
    </div>

    @if ($table)
        <div class="d-flex justify-content-between mt-3">
            <button wire:click="goToPreviousTable" class="btn btn-primary w-25 {{ $currentTable === 0 ? 'disabled' : '' }}">Previous</button>
            <span class="mx-3">{{ $currentTable + 1 }} / {{ $numOfTables }}</span>
            <button wire:click="goToNextTable" class="btn btn-primary w-25 {{ $currentTable === ($numOfTables - 1) ? 'disabled' : '' }}">Next</button>
        </div>
    @endif
</div>