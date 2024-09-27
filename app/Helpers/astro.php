<?php

use Carbon\Carbon;

function d2hms($d, $sep=":") {
    try { 
        # Convierte grados decimales a hh:mm:ss
        $h = $d/15.;
        $hh = floor($h);
        $m = ($h - $hh)*60.;
        $mm = floor($m);
        $s = ($m - $mm)*60.;
        $ss = round($s,1);
        if ($hh <10) {$h0 = 0;} else  {$h0 = '';}
        if ($mm <10) {$m0 = 0;} else  {$m0 = '';}
        if ($ss <10) {$s0 = 0;} else  {$s0 = '';}

        $format = "%02d$sep%02d$sep%04.01f";

        return sprintf($format, $hh, $mm, $ss);
    } catch (Exception $e) {
        return null;
    }
}


function d2dms($d, $sep=":")
{
    try { 
        # Convierte grados decimales a dd:mm:ss
        if ($d < 0) {$d = $d*(-1); $sign = '-';} else {$sign = '+';}
        $dd = floor($d);
        $m = ($d - $dd)*60.;
        $mm = floor($m);
        $s = ($m - $mm)*60.;
        $ss = round($s,1);
        if ($dd <10) {$d0 = 0;} else  {$d0 = '';}
        if ($mm <10) {$m0 = 0;} else  {$m0 = '';}
        if ($ss <10) {$s0 = 0;} else  {$s0 = '';}

        return  $sign.$d0.$dd.$sep.$m0.$mm.$sep.$s0. sprintf("%.01f",$ss);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Converts hexadecimal coordinates to decimal degrees
 *
 * @param string $hms in format DD:MM:SS or HH:DD:SS
 * @param bool $hourangle
 * @return type float $degrees
 *
 */
function hex2d($hms, $hourangle=False)
{
    $parts = explode(":", trim($hms));

    # check if it is negative
    if (strpos($parts[0], "-") === FALSE) {
        $sign = +1;
    } else {
        $sign = -1;
    }

    $degrees = (float)$parts[0] + $sign*(float)$parts[1]/60 + $sign*(float)$parts[2]/3600;

    # If its an hour angle, multiply by 15
    if ($hourangle) {
        $degrees *= 15;
    }

    return $degrees;
}


/**
 * Convierte dia juliano a fecha gregoriana
 *
 * Es una version modificada de jdtogregorian de PHP, para
 * que admita fracion de dia
 *
 * @param float $jd Dia juliano
 * @return string Fecha en fomatato Y-m-d h:i:s
 *
 */
function jd2date($jd, $in_seconds=False) {

    # Separo la parte entera y fraccion de $jd
    $jd_int = floor($jd);
    $jd_dec = $jd - $jd_int;

    # Fecha gregoriana en formato MM/dd/YY
    # Solo admite entero
    $MdY = jdtogregorian($jd_int);

    # Convierto a timestamp (segundos unix)
    $Ymd = date("Y-m-d H:i:s", strtotime("$MdY"));

    $effectiveDate = strtotime($Ymd);  // Unix time

    # Hay que anhadir 12h en segundos por la definicion
    # de JD y la fraccion de dia, en segundos
    $fraction_day = $jd_dec*(24*60*60) + 12*60*60;

    if ($in_seconds) {
        return $effectiveDate + $fraction_day;
    }

    return date("Y-m-d H:i:s", $effectiveDate + $fraction_day);
}


function simbad_link($name, $title='View Simbad data', $target='_blank', $link_name='')
{
    if ($link_name=="") { $link_name = $name;}
    $url = "http://simbad.u-strasbg.fr/simbad/sim-id?protocol=html&Ident=".urlencode($name);
    $link = "<a href=\"$url\" title=\"$title\" target=\"$target\">$link_name</a>";
    return $link;
}


function json2badge(String $json) {

    try { 
        $magnitudes = json_decode($json, True);
        $badges = "";

        foreach ($magnitudes as $band => $magnitude) 
        {
            $badges .= sprintf('<span class="badge badge-secondary" style="font-size: 0.9rem;"><strong>%s</strong>=%s</span> ', $band, $magnitude);
        }
        return $badges;
    } catch (Exception $e) {
        return null;
    }
}


/**
 * Get sunset, sunrice and twilights for a specific location and date.
 *
 * @param string $date Date of observation in YYYY-MM-DD format
 * @param string $location Obsevatory name, OT, OAO, Keck, etc. 
 *
 * @return array: True if the transit occurs between during the night, according twilights, else False.
 * 
 * 
 *  $date->utcOffset(); 
 *   ->toDateTimeString();
 *
 */
function sun_info($date=Null, $location="OT") {

    # La Palma
    $latitude = 28.758333;
    $longitude = -17.8800;
    //$geo_coords = new GeographicalCoordinates( -17.8800, 28.758333);


    if ($date) {
        $night_start = Carbon::createFromFormat('Y-m-d H', sprintf('%s 12', $date));
    } else {
        $night_start = Carbon::now();
    }

    $night_start->timezone('Atlantic/Canary');
    $night_end = $night_start->copy()->add(1, 'day');

    $sun_info = date_sun_info($night_start->timestamp, $latitude, $longitude);
    $sun_info_next_day = date_sun_info($night_end->timestamp, $latitude, $longitude);

    return [
        'sunset' => Carbon::createFromTimestamp($sun_info['sunset']),
        'sunrise' => Carbon::createFromTimestamp($sun_info_next_day['sunrise']),

        'night_begin_civil' => Carbon::createFromTimestamp($sun_info['civil_twilight_end']),
        'night_end_civil' => Carbon::createFromTimestamp($sun_info_next_day['civil_twilight_begin']),

        'night_begin_nautical' => Carbon::createFromTimestamp($sun_info['nautical_twilight_end']),
        'night_end_nautical' => Carbon::createFromTimestamp($sun_info_next_day['nautical_twilight_begin']),

        'night_begin_astronomical' => Carbon::createFromTimestamp($sun_info['astronomical_twilight_end']),
        'night_end_astronomical' => Carbon::createFromTimestamp($sun_info_next_day['astronomical_twilight_begin']),
    ];

}