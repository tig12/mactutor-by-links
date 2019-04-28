<?php
/******************************************************************************
    Creates a csv file using informations retrieved in steps 1 and 2
    This file contains mathematicians with infos about birth / death
    and the nb of links towards each of them.
    
    @license    GPL
    @history    2019-04-27 23:28:35+02:00, Thierry Graff : Creation
********************************************************************************/

define('DS', DIRECTORY_SEPARATOR);

define('CSV_SEP', ';');
define('CSV_FIELDS', [
    'NAME',
    'NB_LINKS',
    'B_DATE',
    'B_PLACE',
    'B_LG',
    'B_LAT',
    'D_DATE',
    'D_PLACE',
]);

$yaml = file_get_contents('config.yml');
$config = yaml_parse($yaml);
$dir_bios = $config['directories']['bios'];
$dir_places = $config['directories']['places'];

// prepare patterns
$pName = '#<h1>(.*?)</h1>#';
$pBirthDeath = '#<h3>Born:(.*?)Died:(.*?)</h3>#sm';
$pLink = '#<a href="../Mathematicians/(.*?).html"#'; // links to other mathematicians
$pBirthPlace = '#<a href = "../BirthplaceMaps/(.*?.html)"><b>Show birthplace location</b></a>#';
$pLgLat = '@Its latitude and longitude are <b>(\d+)&#176;(\d+\'[N|S]) (\d+)&#176;(\d+\'[E|W])</b>@';
$pLink = '#<a href="../Mathematicians/(.*?)"#';

$results = [];

$files_bio = glob($dir_bios . '/*');
foreach($files_bio as $file_bio){
    
    $key = basename($file_bio); // ex "Aaboe.html"
    if(!isset($results[$key])){
        $results[$key] = newResultEntry();
    }
    
    $raw = file_get_contents($file_bio);
    
    // name
    preg_match($pName, $raw, $m1);
    $name = html_entity_decode($m1[1]);
    
    // birth, death
    preg_match($pBirthDeath, $raw, $m2);
    if(count($m2) == 3){
        $birth = html_entity_decode(trim(strip_tags($m2[1])));
        $death = html_entity_decode(strip_tags($m2[2]));
        [$bdate, $bplace] = compute_date($birth);
        [$ddate, $dplace] = compute_date($death);
        // Try to open page with birth place
        preg_match($pBirthPlace, $raw, $m3);
        if(count($m3) == 2){
            $file_place = clean_place($m3[1]);
            $fullpath_place = $dir_places . DS . $file_place;
            $raw2 = file_get_contents($fullpath_place);
            // longitude, latitude of birth place
            preg_match($pLgLat, $raw2, $m4);
            if(count($m4) == 5){
                $lg = compute_lat($m4[1], $m4[2]);
                $lat = compute_lg($m4[3], $m4[4]);
            }
            else{
                echo "Cannot parse $fullpath_place\n";
                $lg = $lat = '';
            }
        }
    }
    else{
        $bplace = $bdate = '';
        $dplace = $ddate = '';
        $lg = $lat = '';
    }
    $current = [
        'name' => $name,
    ];
    
    // parse links to other mathematicians
    preg_match_all($pLink, $raw, $m5);
    // if links are found
    if(isset($m5[1])){
        $links = array_unique($m5[1]); // multiple links to the same person are counted for one
        foreach($links as $link){
            if(!isset($results[$link])){
                $results[$link] = newResultEntry();
            }
            $results[$link]['NB_LINKS'] ++; // HERE increment celebrity count
        }
    }
    
    // fill current person
    // do field by field to avoid erasing NB_LINKS
    $results[$key]['NAME'] = $name;
    $results[$key]['B_DATE'] = $bdate;
    $results[$key]['B_PLACE'] = $bplace;
    $results[$key]['B_LG'] = $lg;
    $results[$key]['B_LAT'] = $lat;
    $results[$key]['D_DATE'] = $ddate;
    $results[$key]['D_PLACE'] = $dplace;
//echo "\n<pre>"; print_r($results); echo "</pre>\n"; exit;
//break;
}

$csv = implode(CSV_SEP, CSV_FIELDS) . "\n";
foreach($results as $person){
    $csv .= implode(CSV_SEP, $person) . "\n";
}
file_put_contents($config['result-csv'], $csv);
echo "csv file stored in {$config['result-csv']}\n";
    

// ******************************************************
function newResultEntry(){
    $new = [];
    foreach(CSV_FIELDS as $field){
        $new[$field] = '';
    }
    $new['NB_LINKS'] = 0;
    return $new;
}


// ******************************************************
/**
    Converts a string to a date
    Ex : 5 August 1802 in Frindöe (near Stavanger), Norway returns 1802-08-05
    @param  $str The string to parse
    @return  An array with two elements :
                - day in iso 8601 format YYYY-MM-DD
                - place : string identical to the place in $str
**/
function compute_date($str){
    $months = [
        'January' => '01',
        'Jan' => '01',
        'February' => '02',
        'Feb' => '02',
        'March' => '03',
        'April' => '04',
        'Apr' => '04',
        'May' => '05',
        'June' => '06',
        'Jun' => '06',
        'July' => '07',
        'August' => '08',
        'Aug' => '08',
        'September' => '09',
        'October' => '10',
        'November' => '11',
        'December' => '12',
    ];
    $p1 = '/(\d+) (\w+) (\d+) in (.*)/'; // pattern for most common case
    preg_match($p1, $str, $m);
    if(count($m) == 5){
        $day = $m[3] . '-' . $months[$m[2]] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        $place = $m[4];
    }
    else{
        $day = $place = '';
    }
    return [$day, $place];
}


// ******************************************************
/**
    @param $str_deg A string like "55"
    @param $$str_min A string like "34'E"
    @return decimal degrees
**/
function compute_lg($str_deg, $str_min){
    $deg = $str_deg;
    $min = substr($str_min, 0, -2);
    $EW = substr($str_min, -1);
    switch($EW){
        case 'E' : break;
        case 'W' : $deg *= -1; break;
        default: throw new Exception("Unable to parse longitude - EW = $EW");
    }
    $deg += round($min / 60, 6);
    return $deg;
}


// ******************************************************
/**
    @param $str_deg A string like "55"
    @param $$str_min A string like "41'N"
    @return decimal degrees
**/
function compute_lat($str_deg, $str_min){
    $deg = $str_deg;
    $min = substr($str_min, 0, -2);
    $NS = substr($str_min, -1);
    switch($NS){
        case 'N' : break;
        case 'S' : $deg *= -1; break;
        default: throw new Exception("Unable to parse latitude - NS = $NS");
    }
    $deg += round($min / 60, 6);
    return $deg;
}

// ******************************************************
/**
    Fix problems of white space in place names
**/
function clean_place($str){
    $clean = $str;
    if(substr($str, 0, 1) == ' '){
        // happens for New-York, Washington
        $clean = substr($str, 1);
    }
    else if(strpos($str, ' ') !==  false){
        // happens for Rimavska Sobota
        $clean = str_replace(' ', '_', $str);
    }
    return $clean;
}
