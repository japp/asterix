
function getTargets() {
    let targets = localStorage.getItem('targets');

    if (targets) {
        const targets_list = JSON.parse(targets);
        
        return targets_list;
    }
   
    return [];
}

function addTarget() {

    let name = document.getElementById('target_name').value;
    let ra = document.getElementById('target_ra').value;
    let dec = document.getElementById('target_dec').value;

    let coords = hex2d(ra, dec);
    var target = {
            "name": name,
            "ra": coords['ra'],
            "dec": coords['dec'],
        };

    var targets = JSON.parse(localStorage.getItem("targets"));
    if (targets == null) targets = [];

    localStorage.setItem("target", JSON.stringify(target));

    // Save targets back to local storage
    targets.push(target);
    localStorage.setItem("targets", JSON.stringify(targets));
}


function d2hms(d, delimiter = ":") {
    const h = d / 15;
    const hh = Math.floor(h);
    const m = (h - hh) * 60;
    const mm = Math.floor(m);
    const s = (m - mm) * 60;
    const ss = s.toFixed(3);
    
    const h0 = hh < 10 ? "0" : "";
    const m0 = mm < 10 ? "0" : "";
    const s0 = ss < 10 ? "0" : "";
  
    return h0 + hh + delimiter + m0 + mm + delimiter + s0 + ss;
}
  

function d2dms(d, delimiter = ":") {
    let sign = "";

    if (d < 0) {
      d = Math.abs(d);
      sign = '-';
    } else {
      sign = '+';
    }
  
    const dd = Math.floor(d);
    const m = (d - dd) * 60;
    const mm = Math.floor(m);
    const s = (m - mm) * 60;
    const ss = s.toFixed(3);
  
    const d0 = dd < 10 ? "0" : "";
    const m0 = mm < 10 ? "0" : "";
    const s0 = ss < 10 ? "0" : "";
  
    return sign + d0 + dd + delimiter + m0 + mm + delimiter + s0 + ss;
}

/**
 * HH:MM:SS a grados D.DDD
 * 
 * @param {String} ra: Right ascension in HH:MM:SS.S format
 * @returns {Float}: Right ascension ra in decimal degrees
 */
function hms2d(ra) {
    // Divide las cadenas en partes para grados, minutos y segundos
    const raParts = ra.split(':');

    if (raParts.length !== 3) {
        return "Formato incorrecto. Debe ser 'hh:mm:ss.sss'.";
    }
    // Convierte las partes en valores decimales
    const raDegrees = parseFloat(raParts[0]) * 15 + parseFloat(raParts[1]) / 4 + parseFloat(raParts[2]) / 240;

    return raDegrees;
}


/**
 * DD:MM:SS a grados D.DDD
 * 
 * @param {String} dec in DD:MM:SS.S format
 * @returns {Float} declination in decimal degrees
 */
function dms2d(dec) {
    // Divide las cadenas en partes para grados, minutos y segundos
    const decParts = dec.split(':');

    if (decParts.length !== 3) {
        return "Formato incorrecto. Debe ser 'dd:mm:ss.sss'.";
    }

    // Convierte las partes en valores decimales
    const decDegrees = parseFloat(decParts[0]) + parseFloat(decParts[1]) / 60 + parseFloat(decParts[2]) / 3600;

    return decDegrees;
}

function deg2date(degrees, date) {

    let datetime = moment(date).set({hour:0,minute:0,second:0,millisecond:0}).add(degrees/15, 'hours');

    return datetime;
}
  

/**
 * Tiempo Sideral Local (LST)
 * 
 * Tiempo Sideral Local (LST) = GST + (Longitud Este / 15° por hora)
 * 
 * @param {Date} date 
 * @param {float} longitude: Negative if West (degrees)
 * 
 * @returns {float}: Local sidereal time in degrees
 */
function LocalSiderealTime(date, longitude) {
    // Greenwich Apparent Sidereal Time (GAST).
    // https://github.com/cosinekitty/astronomy/tree/master/source/js#siderealtimedate--number
    // return GAST in hours
    const gst = Astronomy.SiderealTime(date);

    // convert GAST to degrees and sum the longitude of the observer
    // in degree to obtain the Local Sidereal Time

    const lst_hours = Math.abs(gst + longitude/15);

    return moment(date).set({hours:0, minutes:0, seconds:0, milliseconds:0}).add(lst_hours, 'hours');
}

function FormatDate(date) {
    var year = Pad(date.getFullYear(), 4);
    var month = Pad(1 + date.getMonth(), 2);
    var day = Pad(date.getDate(), 2);
    var hour = Pad(date.getHours(), 2);
    var minute = Pad(date.getMinutes(), 2);
    var second = Pad(date.getSeconds(), 2);
    return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
}

function Pad(s, w) {
    s = s.toFixed(0);
    while (s.length < w) {
        s = '0' + s;
    }
    return s;
}

function loadFile(filePath) {
    var result = null;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", filePath, false);
    xmlhttp.send();
    if (xmlhttp.status==200) {
        result = xmlhttp.responseText;
    }
    return result;
}

/**
 * name solver using Simbad server
 * 
 * http://vizier.cfa.harvard.edu/viz-bin/nph-sesame/A?
 * http://cdsweb.u-strasbg.fr/cgi-bin/nph-sesame/A?
 * 
 * http://cds.unistra.fr/cgi-bin/nph-sesame/-oxp/NSV?
 * 
 * 
 */
function sesame(name) {

    try {
        let sesame_url = encodeURI("https://cds.unistra.fr/cgi-bin/nph-sesame/-oxp/~A?" + name); 
        var content = loadFile(sesame_url);

        parser = new DOMParser();
        xmlDoc = parser.parseFromString(content, "text/xml");

        var jradeg = xmlDoc.getElementsByTagName("jradeg")[0].childNodes[0].nodeValue
        var jdedeg = xmlDoc.getElementsByTagName("jdedeg")[0].childNodes[0].nodeValue

        var data = {'ra': jradeg, 'dec': jdedeg};
 
    } catch (error) {

        var data = false;
    }

    return data;
}


/**
 * Convierte el numero de días desde J2000.0 a UT1
 * 
 * @param {float} daysFraction 
 * @returns {Date} Date object in UTC 
 */
function j2000ToUTC(daysFraction) {
    // La fecha J2000.0 en UT es 1 de enero de 2000 a las 11:58:55.816 UTC
    const j2000UT = new Date(Date.UTC(2000, 0, 1, 11, 58, 55, 816));

    // Convertir los días fraccionarios a milisegundos
    const millisecondsPerDay = 86400000; // 24 * 60 * 60 * 1000
    const millisecondsFromJ2000 = daysFraction * millisecondsPerDay;

    // Calcular la nueva fecha y hora
    const resultDate = new Date(j2000UT.getTime() + millisecondsFromJ2000);

    return resultDate;
}



/**
 * 
 * Moon phase
 * https://github.com/cosinekitty/astronomy/tree/master/source/js#moonphasedate--number
 *   0 = new moon
 *  90 = first quarter
 * 180 = full moon
 * 270 = third quarter
 * 
 * @param {Array} observer 
 * @param {Date} obsdate 
 * @returns {Array} {AstroTime} 
 */
function sunInfo(observer, obsdate) {

    // searchRiseSet(body: Body, observer: Observer, direction: Direction, startTime: Time, limitDays: Double, metersAboveGround: Double = 0.0): Time?
    let sunrise = Astronomy.SearchRiseSet('Sun', observer, +1, obsdate, -1);
    let sunset = Astronomy.SearchRiseSet('Sun', observer, -1, obsdate, +1);

    // Civil twilight
    // SearchAltitude(body, observer, direction, dateStart, limitDays, altitude) ⇒ AstroTime
    // https://github.com/cosinekitty/astronomy/tree/master/source/js#SearchAltitude
    let civil_twilight_dusk = Astronomy.SearchAltitude('Sun', observer, -1, obsdate, +1, -6);
    let civil_twilight_down  = Astronomy.SearchAltitude('Sun',  observer, +1, obsdate, -1, -6);

    // Nautical twilight
    let nautical_twilight_dusk = Astronomy.SearchAltitude('Sun',  observer, -1, obsdate, +1, -12);
    let nautical_twilight_down = Astronomy.SearchAltitude('Sun',  observer, +1, obsdate, -1, -12);

    // Astronomical twilight
    let astronomical_twilight_dusk = Astronomy.SearchAltitude('Sun',  observer, -1, obsdate, +1, -18);
    let astronomical_twilight_down = Astronomy.SearchAltitude('Sun',  observer, +1, obsdate, -1, -18);

    return {
        'sunrise': sunrise,
        'sunset': sunset,
        'civil_twilight_dusk': civil_twilight_dusk,
        'civil_twilight_down': civil_twilight_down,
        'nautical_twilight_dusk': nautical_twilight_dusk,
        'nautical_twilight_down': nautical_twilight_down,
        'astronomical_twilight_dusk': astronomical_twilight_dusk,
        'astronomical_twilight_down': astronomical_twilight_down
    };
}

/**
 * 
 * @param {Array} observer 
 * @param {Date} obsdate 
 * @returns {Array}  
 */
function moonInfo(observer, obsdate) {

    let moonrise = Astronomy.SearchRiseSet('Moon', observer, +1, obsdate, -1);
    let moonset  = Astronomy.SearchRiseSet('Moon', observer, -1, obsdate, +1);

    return {
        'moonrise': moonrise,
        'moonset': moonset,
        'moonphase': Astronomy.MoonPhase(obsdate),
        'illum': Astronomy.Illumination(Astronomy.Body.Moon, obsdate)
    };

}


function showMessage() {
    return {
        show: true,
        message: "Un mensaje"
    }
}

function targetsList() {
    let initial_targets = [];

    let targets_localStorage = localStorage.getItem('targets');
    if (targets_localStorage !== null & targets_localStorage != "") {
        initial_targets = JSON.parse(targets_localStorage);
    } 

    return {
       
        status: false,
        isError: true,
        name: "",
        ra: "",
        dec: "",
        targets: initial_targets,
        addTarget() {
            this.targets.push({'name': this.name, 'ra': hms2d(this.ra), 'dec': dms2d(this.dec)});
            this.name = "";
            this.ra = "";
            this.dec = "";
            localStorage.setItem("targets", JSON.stringify(this.targets));
        },
        targetData(target) {
            return "<span class=''>" +target.name + "</span> &nbsp; <span><code>"+ d2hms(target.ra) + "</span> <span>" + d2dms(target.dec) + "</code></span>";
        },
        solveTarget() {
            let name = document.getElementById('target_name').value;
            let coords = sesame(name); 
        
            if (coords) {
                this.ra = d2hms(coords['ra']);
                this.dec = d2dms(coords['dec']);
            } 
        },
        deleteTarget(index) {
            this.targets = this.targets.filter((target, targetIndex) => {
                return index !== targetIndex
            });
            localStorage.setItem("targets", JSON.stringify(this.targets));
        },
        deleteTargets() {
            targets = [];
            localStorage.setItem("targets", targets);
        },
        targetCount() {
            return this.targets.length + " targets"
        },
        isLastTarget(index) {
            return this.targets.length - 1 === index
        }
    };
}

async function setLocation() {

    let locations;
    const response = await fetch('./locations.json');
    locations = await response.json();
    var select = document.getElementById("locations");

    var location = JSON.parse(localStorage.getItem('location'));

    if (location !== null) {
        select.value = location['code'];
    } 

    select.addEventListener("change", function(){
        var selectedOption = this.options[this.selectedIndex];
        localStorage.setItem("location", JSON.stringify(locations[selectedOption.value]));
    });

    return location;

}

async function setLocationsSelect() {

    let locations;
    const response = await fetch('./locations.json');
    locations = await response.json();

    var select = document.getElementById("locations");

    if (select.options.length === 0) {
        Object.keys(locations).forEach(code => {
            var option = document.createElement("option");
            option.text = locations[code]['name'];
            option.value = code;
            select.add(option);
        })
    }

}
   