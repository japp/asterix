@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">
            {{ __('Targets') }}
        </h2>
    </x-slot>

    <h2>{{ __('Targets') }}</h2>

    <div class="card">
        <div class="card-body">

            <a href="{{ route('targets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add target
            </a>

            @livewire('targets-list')
        
        </div>
    </div>
@endsection