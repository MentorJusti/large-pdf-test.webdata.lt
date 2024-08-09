<div>
    <div class="card" style="max-width: 650px;">
        <div class="card-header" style="font-weight: bold;">
            Scanned Results
        </div>
        <div class="card-body" style="height: 580px; overflow-y: scroll;">
            @if ($loading)
                <div class="d-flex flex-column h-100 justify-content-center align-items-center">
                    <div class="spinner-border text-light" style="width: 5rem; height: 5rem; border-width: 0.5rem;"></div>
                </div>
            @else
                @if (isset($tables) && count($tables) > 0)
                    @foreach ($tables as $table)
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
                    @endforeach
                @else
                    <p class="text-muted">This page has no tables...</p>
                @endif
            @endif
        </div>
    </div>

    @if (!$loading && $totalPages > 1)
        <div class="d-flex justify-content-between mt-3">
            <button wire:click="goToPreviousPage" class="btn btn-primary w-25 {{ $currentPage === 1 ? 'disabled' : '' }}">Previous</button>
            <span class="mx-3">{{ $currentPage }} / {{ $totalPages }}</span>
            <button wire:click="goToNextPage" class="btn btn-primary w-25 {{ $currentPage === $totalPages ? 'disabled' : '' }}">Next</button>
        </div>
    @endif
</div>
