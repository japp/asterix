@extends('layouts.app')

@section('content')
 
    <div class="card">
        <div class="card-header">
            <div class="float-start">
                Edit target
            </div>
            <div class="float-end">
                <a href="{{ route('targets.index') }}" class="btn btn-primary btn-sm">&larr; Back</a>
            </div>
        </div>
        <div class="card-body">
            @livewire('target-form', ['target' => $target])
        </div>
    </div>
@endsection