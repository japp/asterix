@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">
            {{ __('Proposal overview') }}
        </h2>
    </x-slot>

    <h2>{{ $target->name }}</h2>

    <div class="card">
        <div class="card-body">

            <div class="row">

                <div class="col-md-6">
                    <table class="table table-striped">
                        <tr>
                            <th>Name</th>
                            <td>{{ $target->name }}</td>
                        </tr>
                        <tr>
                            <th>RA</th>
                            <td>{{ $target->radeg }}</td>
                        </tr>
                        <tr>
                            <th>Dec</th>
                            <td>{{ $target->decdeg }}</td>
                        </tr>
                        <tr>
                            <th>Vmag</th>
                            <td>{{ $target->Vmag }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection