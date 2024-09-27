<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

use deepskylog\AstronomyLibrary\Coordinates\Coordinate;
use deepskylog\AstronomyLibrary\Time;
use deepskylog\AstronomyLibrary\Coordinates\EclipticalCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\EquatorialCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\GalacticCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\GeographicalCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\HorizontalCoordinates;



class Select2Dropdown extends Component
{

    public $ottPlatform = '';
 
    public $webseries = [
        'Wanda Vision',
        'Money Heist',
        'Lucifer',
        'Stranger Things'
    ];     

    public function render()
    {

        # $year, $month, $day, $hour, $minute, $second, $tz
        $date = Carbon::create(2021, 11, 19, 21);
        $date->timezone('Atlantic/Canary');
        # float $longitude, float $latitude
        $geo_coords = new GeographicalCoordinates( -17.8800, 28.758333);

        # Aldebaran
        # Units: $ra: hourangle (0, 24), $dec: decimal deg (-90, +90)
        # $epoch: J2000 by default
        $coords = new EquatorialCoordinates(4.5986775193, 16.5093);


        // Get the mean siderial time for the given date
        $meanSiderialTime = Time::meanSiderialTime($date, $geo_coords);

        // Get the apparent siderial time for the given date
        $apparentSiderialTime = Time::apparentSiderialTime($date, $geo_coords);

        //dump(['mean' => $meanSiderialTime, 'apparent' => $apparentSiderialTime]);

        $altaz = $coords->convertToHorizontal($geo_coords, $meanSiderialTime);

        dump($date);
        dump($coords);
        dump($altaz->getAltitude()->getCoordinate());

        return view('livewire.select2-dropdown', ['coords' => $coords])->extends('layouts.app');
    }
}