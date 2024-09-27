@extends('layouts.app')

@section('content')
    @if (session('status'))
      <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
      </div>
    @endif
    <div class="card">
        <div class="card-body">
       

            @livewire('target-form')
        </div>
    </div>
@endsection