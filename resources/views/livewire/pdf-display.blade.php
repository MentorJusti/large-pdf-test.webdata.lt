<div class="card">
    <div class="card-header" style="font-weight: bold;">
        Original PDF
    </div>
    <div class="card-body">
        <iframe src="{{ $pdfUrl != null ? Storage::disk('public')->url($pdfUrl) : '' }}" width="100%" height="600px"></iframe>
    </div>
</div>