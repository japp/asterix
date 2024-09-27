<div>

    <div class="mb-4">
        <input
          wire:model.live.debounce.300ms="search"
          type="text"
          placeholder="Search target..."
          class="form-control px-4 py-2"
        />
    </div>

    <table class="table table-striped">
    <tr class="bg-light py-3">
        <th>Name</th>
        <th>Coords (J2000)</th>
        <th>Vmag</th>
        <th></th>
    </tr>
    @foreach($targets as $target)
    <tr class="">
        <td><a href="{{ route('targets.show', $target) }}" title="View target details">{{ $target->name}} </td>
        <td><code>{{ d2hms($target->radeg)}}</code> <code>{{ d2dms($target->decdeg)}}</code></td>
        <td>{{ $target->Vmag}}</td>
        <td>
            <div class="btn-group" role="group" aria-label="">
                <a class="btn btn-primary btn-sm" href="{{ url('targets/'.$target->id.'/edit') }}">Edit</a>

                <form action="{{ url('targets/'.$target->id) }}" method="POST" >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach
    </table>

    {!! $targets->links() !!}
</div>
