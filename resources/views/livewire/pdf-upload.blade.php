<div class="card mb-4">
    <div class="card-header" style="font-weight: bold;">
        Upload PDF
    </div>
    <div class="card-body">
        <form enctype="multipart/form-data" wire:submit.prevent="uploadFile">
            <div class="input-group">
                <input type="file" accept=".pdf" class="form-control" wire:model="pdf">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>

            @error('pdf')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </form>
        
        @if (session()->has('message'))
            <div class="alert alert-success mt-3">{{ session('message') }}</div>
        @endif
    </div>
</div>
