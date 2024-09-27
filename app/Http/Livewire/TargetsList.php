<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Target;
use Livewire\WithPagination;

class TargetsList extends Component
{


    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $targets = Target::orderBy('id','desc')
                    ->where(function ($query) {
                        $query->where('name', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('name', 'LIKE', '%' . $this->search . '%');
                    })
                    ->paginate(5);

        return view('livewire.targets-list', ['targets' =>  $targets]);
    }
}

