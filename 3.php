<?php
/******************************************************************************
    Creates a csv file using informations retrieved in steps 1 and 2
    This file contains mathematicians with infos about birth / death
    and the nb of links towards each of them.
    
    @license    GPL
    @history    2019-04-27 23:28:35+02:00, Thierry Graff : Creation
********************************************************************************/

require_once 'model/MacTutor.php';
require_once 'model/Bio.php';
require_once 'model/Place.php';
require_once 'model/Csv.php';

$dir_bios = MacTutor::$config['directories']['bios'];
$dir_places = MacTutor::$config['directories']['places'];

// prepare patterns
$pName = '#<h1>(.*?)</h1>#';
$pBirthDeath = '#<h3>Born:(.*?)Died:(.*?)</h3>#sm';
$pLink = '#<a href="../Mathematicians/(.*?).html"#'; // links to other mathematicians
$pBirthPlace = '#<a href = "../BirthplaceMaps/(.*?.html)"><b>Show birthplace location</b></a>#';
$pLgLat = '@Its latitude and longitude are <b>(\d+)&#176;(\d+\'[N|S]) (\d+)&#176;(\d+\'[E|W])</b>@';
$pLink = '#<a href="../Mathematicians/(.*?)"#';

$results = [];

$files_bio = glob($dir_bios . '/*');

// to check the links
$basenames_bio = [];
foreach($files_bio as $tmp){
    $basenames_bio[] = basename($tmp);
}

foreach($files_bio as $file_bio){
    
    $key = basename($file_bio); // ex "Aaboe.html"
    if(!isset($results[$key])){
        $results[$key] = newResultEntry();
    }
    
    $raw = file_get_contents($file_bio);
    
    // name
    preg_match($pName, $raw, $m1);
    $name = trim(html_entity_decode($m1[1]));
    
    // birth, death
    preg_match($pBirthDeath, $raw, $m2);
    if(count($m2) == 3){
        $birth = trim(html_entity_decode(strip_tags($m2[1])));
        $death = trim(html_entity_decode(strip_tags($m2[2])));
        [$bdate, $bplace] = Bio::compute_date($birth);
        [$ddate, $dplace] = Bio::compute_date($death);
        // Try to open page with birth place
        preg_match($pBirthPlace, $raw, $m3);
        if(count($m3) == 2){
            $file_place = MacTutor::clean_place($m3[1]);
// @todo put the following code in Place.php
//            [$lg, $lat, $wikipedia] = Place::getInfo($file_place);
// /* 
            $fullpath_place = $dir_places . DS . $file_place;
            $raw2 = file_get_contents($fullpath_place);
            // longitude, latitude of birth place
            $lg = $lat = '';
            preg_match($pLgLat, $raw2, $m4);
            if(count($m4) == 5){
                $lg = Place::compute_lat($m4[1], $m4[2]);
                $lat = Place::compute_lg($m4[3], $m4[4]);
            }
            else{
                echo "Cannot parse $fullpath_place\n";
            }
            // wikipedia
// */
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
            $link = Bio::fix_broken_link($link);
            if(!in_array($link, $basenames_bio)){
                echo "Link $link does not correspond\n";
            }
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

    // particular cases
    if($key == 'Bordoni.html'){
        $results[$key]['B_PLACE'] = 'Mezzana Corti, now Cava Manara, Savoy, Italy';
    }
}

// generate csv

$csv = implode(CSV::SEP, CSV::FIELDS) . "\n";
foreach($results as $key => $person){
if(trim($person['NAME']) == ''){ echo "$key\n"; }
    $csv .= implode(CSV::SEP, $person) . "\n";
}
file_put_contents(MacTutor::$config['result-csv'], $csv); // HERE write file
echo "csv file stored in " . MacTutor::$config['result-csv'] . "\n";
    

// ******************************************************
function newResultEntry(){
    $new = [];
    foreach(CSV::FIELDS as $field){
        $new[$field] = '';
    }
    $new['NB_LINKS'] = 0;
    return $new;
}


