<div>
    <p><button class="btn btn-primary" wire:click="solve()">Solve</button></p>

    <div class="card col-2 ">
        <h4>{{ $name }}</h4>
        @dump($solvedName)
    </div>


    <form wire:submit.prevent="save">
      <div class="mb-4">
        <label for="name" class="">Title</label>
        <input wire:model="name" type="text" id="name" class="form-control" required />
        @error('name') <span class="text-alert">{{ $message }}</span> @enderror
      </div>
  
      <div class="mb-4">
        <label for="radeg" class="text-gray-700 mb-2 block font-bold">radeg</label>
        <input wire:model="radeg" type="text" id="radeg" class="form-control" required />
        @error('radeg') <span class="text-alert">{{ $message }}</span> @enderror
      </div>

      <div class="mb-4">
        <label for="decdeg" class="text-gray-700 mb-2 block font-bold">decdeg</label>
        <input wire:model="decdeg" type="text" id="decdeg"class="form-control" required />
        @error('decdeg') <span class="text-alert">{{ $message }}</span> @enderror
      </div>

      <div class="mb-4">
        <label for="Vmag" class="text-gray-700 mb-2 block font-bold">Vmag</label>
        <input wire:model="Vmag" type="text" id="Vmag" class="form-control" required />
        @error('Vmag') <span class="text-alert">{{ $message }}</span> @enderror
      </div>

  
      <div>
        <button type="submit"  class="btn btn-primary btn-lg text-light">
          {{ $target ? 'Update Target' : 'Create Target' }}
        </button>
      </div>
    </form>
  </div>