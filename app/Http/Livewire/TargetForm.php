<?php

namespace App\Http\Livewire;

use App\Models\Target;
use Illuminate\Support\Str;
use Livewire\Component;

use Japp\Astrolib\Astrolib;


class TargetForm extends Component
{
    public $target;
    public $name;
    public $radeg;
    public $decdeg;
    public $Vmag;
    public $magnitudes;

    public $solvedName;

    protected $rules = [
        'name' => 'required|min:5',
        'radeg' => 'required',
        'decdeg' => 'required',
    ];

    public function solve() {

        if ($this->name) {
        $astrolib = new Astrolib();
        $this->solvedName = $astrolib->sesame($this->name);

            if (key_exists('Resolver', $this->solvedName['Target']))
            {

                $jpos = explode(" ", $this->solvedName['Target']['Resolver']['jpos']);
                
                $this->radeg = $jpos[0];
                $this->decdeg = $jpos[1];

                # Simbad don't provide fluxes for some targets
                if (key_exists('mag', $this->solvedName['Target']['Resolver']))
                {
                    $mags = $this->solvedName['Target']['Resolver']['mag'];
                    $simbad_magnitudes = [];

                    if (key_exists("@attributes", $mags)) {
                        $band = trim($mags["@attributes"]["band"]);
                        $value = trim($mags["v"]);

                        $simbad_magnitudes[$band] = round($value, 2);  // one decimal is enought
                    } else {
                        foreach ($mags as $mag) {
                            $band = trim($mag["@attributes"]["band"]);
                            $value = trim($mag["v"]);

                            $simbad_magnitudes[$band] = round($value, 2);
                        }
                    }
                    $this->magnitudes = json_encode($simbad_magnitudes);
                    $this->magnitudes = str_replace(["{", "}", "\"", ",", ":"],
                                                    ["", "", "", ", ", "="],
                                                    $this->magnitudes);
                } else {
                    $this->magnitudes = "";
                }

                $this->solvedName = 1;

            } else {
                # if name solving fails, clean some variables
                $this->solvedName = -1;

                $this->radeg = null;
                $this->decdeg = null;
                $this->magnitudes = null;
            }
        }
    }

    public function mount($target = null)
    {
        if ($target) {
            $this->target = $target;
            $this->name = $target->name;
            $this->radeg = $target->radeg;
            $this->decdeg = $target->decdeg;
            $this->Vmag = $target->Vmag;
        }
    }

    public function save()
    {
        $this->validate();

        $isNew = !$this->target;

        if ($isNew) {
            $this->target = new Target();
            $this->target->user_id = auth()->id();
        }

        $this->target->name = $this->name;
        //$this->post->slug = Str::slug($this->title);
        $this->target->radeg = $this->radeg;
        $this->target->decdeg = $this->decdeg;
        $this->target->Vmag = $this->Vmag;
        $this->target->save();

        //$this->post->tags()->sync($this->selectedTags);

        session()->flash('message', $isNew ? 'Target created successfully.' : 'Target updated successfully.');

        return redirect()->route('targets.show', $this->target);
    }

    public function render()
    {
        return view('livewire.target-form');
    }
}