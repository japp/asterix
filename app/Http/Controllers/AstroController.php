<?php
/**
 * Carbon library
 * https://carbon.nesbot.com/docs/
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\MonthlyUsersChart;

use Carbon\Carbon;

use deepskylog\AstronomyLibrary\Coordinates\Coordinate;
use deepskylog\AstronomyLibrary\Time;
use deepskylog\AstronomyLibrary\Coordinates\EclipticalCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\EquatorialCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\GalacticCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\GeographicalCoordinates;
use deepskylog\AstronomyLibrary\Coordinates\HorizontalCoordinates;

use Japp\Astrolib\Astrolib;
use \Mpdf\Mpdf as PDF; 
use Illuminate\Support\Facades\Storage;


class AstroController extends Controller
{

    public function chart(MonthlyUsersChart $chart)
    {
        $astrolib = new Astrolib();

        $title = "Ese peaso de gráfica";

        $suninfo = sun_info();

        $date = $suninfo['sunset'];
        //$date->timezone('Atlantic/Canary');

        $location = $astrolib->getLocations()['OT'];
        # Longitude, Latitude (deg)
        $geo_coords = new GeographicalCoordinates($location['longitude'], $location['latitude']);

        # Targets
        # Units: $ra: hourangle (0, 24), $dec: decimal deg (-90, +90)
        # $epoch: J2000 by default
        $targets_names = ["Aldebaran", "Pollux", "M101", "Fomalhaut", "Cor Caroli", "Polaris"];

        $targets = [];

        foreach ($targets_names as $name) {
            $simbad = $astrolib->sesame($name);

            # Si resuelve, añadelo a la lista
            if (key_exists('Resolver', $simbad['Target'])) {
                $jradeg = $simbad['Target']['Resolver']['jradeg'];
                $jdedeg = $simbad['Target']['Resolver']['jdedeg'];
            }
            
            $targets[$name] = new EquatorialCoordinates($jradeg/15, $jdedeg);
        }

        $datasets = [];

        $last_date = $date->copy();
        $timerange = [];
        $m = 0;

        foreach ($targets as $name => $coords) {

            $datasets[$name] = [];
            $datasets[$name]['name'] = $name;
            $datasets[$name]['data'] = [];
            # si el target está encima del horizonte en algún momento
            $datasets[$name]['over_horizon'] = False;

            # Calcula la altitud de cada objeto en tramos de 10min
            while ($last_date < $suninfo['sunrise']) {

                // Get the mean siderial time for the given date
                $meanSiderialTime = Time::meanSiderialTime($last_date, $geo_coords);
                $altaz = $coords->convertToHorizontal($geo_coords, $meanSiderialTime);

                $altitude = $altaz->getAltitude()->getCoordinate();
                if ($altitude > 0) {
                    $datasets[$name]['over_horizon'] = True;
                    $datasets[$name]['data'][] = $altitude;
                }  else {
                    $datasets[$name]['data'][] = null;
                }

                $timerange[] = $last_date->format('Y-m-d H:i');
                
                //$azimuth1[] = $altaz->getAzimuth()->getCoordinate();
                $last_date = $last_date->add(10, 'minute');
            }

            # si el target no está encima del horizonte en algún momento, quítalo de la lista
            if ($datasets[$name]['over_horizon'] === False) {
                unset($datasets[$name]);
            }

            $last_date = $date->copy();
        }

        return view('chart', ['chart' => $chart->build($timerange, array_values($datasets), $title)]);
    } 



    public function testapi()
    {
        $suninfo = sun_info();

        try {
            return response(['data' => $suninfo, 'status' => 200]);
        } catch (ModelNotFoundException $e) {
            return response(['message' => 'Fail!', 'status' => 404]);
        }
    }


    public function observability(Request $request)
    {

        $astrolib = new Astrolib();

        $observability = $astrolib->observability($request->targets, $request->location, $request->start_datetime, $request->end_datetime, $min_altitude=30, $min_time=20, $observability_type=$request->observability_type);

        return response(['data' => response()->json($observability), 'status' => 200]);
    }

    public function solveName(Request $request)
    {

        if ($request->name) {
            $simbad = $astrolib->sesame($request->name);
            $result = [];

            # Si resuelve, añadelo a la lista
            if (key_exists('Resolver', $simbad['Target'])) {
                $result['name'] = $request->name;
                $result['jradeg'] = (float)$simbad['Target']['Resolver']['jradeg'];
                $result['jdedeg'] = (float)$simbad['Target']['Resolver']['jdedeg'];
            } else {
                return response(['message' => 'Can not resolve the name '. $request->name, 'status' => 500]);
            }
            
        } else {
            return response(['message' => 'Target name not valid', 'status' => 500]);
        }


        return response(['data' => response()->json($result), 'status' => 200]);
    }



}
