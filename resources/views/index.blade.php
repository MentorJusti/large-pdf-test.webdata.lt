@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y my-5">
        <div class="row">
            <div class="col">
                @livewire('pdf-upload')
            </div>
        </div>

        <div class="row">
            <div class="col"> 
                @livewire('pdf-display')
            </div>
            <div class="col"> 
                @livewire('pdf-results')
            </div> 
        </div>
    </div> 
</div>