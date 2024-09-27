<x-guest-layout>
    <div class="row justify-content-center pt-4">
        <div class="col-10">
            <div>
                <x-jet-authentication-card-logo />
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="container px-4 mx-auto">
                        <div class="p-6 m-20 bg-white rounded shadow">
                            {!! $chart->container() !!}
                        </div>
                    </div>
                    
                    <script src="{{ $chart->cdn() }}"></script>
                    
                    {{ $chart->script() }}
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

