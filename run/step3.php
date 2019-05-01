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
require_once 'lib/jth_sortByKey.php';

$dir_bios = MacTutor::$config['directories']['bios'];
$dir_places = MacTutor::$config['directories']['places'];

// prepare patterns
$pName = '#<h1>(.*?)</h1>#';
$pBirthDeath = '#<h3>Born:(.*?)Died:(.*?)</h3>#sm';
$pLink = '#<a href="../Mathematicians/(.*?).html"#'; // links to other mathematicians
$pBirthPlace = '#<a href = "../BirthplaceMaps/(.*?.html)"><b>Show birthplace location</b></a>#';
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
    $bplace = $bdate = '';
    $dplace = $ddate = '';
    $lg = $lat = '';
    $wikipedia = '';
    if(count($m2) == 3){
        $birth = trim(html_entity_decode(strip_tags($m2[1])));
        $death = trim(html_entity_decode(strip_tags($m2[2])));
        [$bdate, $bplace] = Bio::compute_date_place($birth);
        [$ddate, $dplace] = Bio::compute_date_place($death);
        // Try to get infos from page with birth place
        preg_match($pBirthPlace, $raw, $m3);
        if(count($m3) == 2){
            $file_place = MacTutor::clean_place($m3[1]);
            [$lg, $lat, $wikipedia] = Place::get_infos($file_place); // HERE parses wikipedia page
            if([$lg, $lat] == ['', '']){
                echo "Cannot get coordinates for $file_place\n";
            }
        }
    }
    $current = [
        'name' => $name,
    ];
    
    // parse links to other mathematicians
    preg_match_all($pLink, $raw, $m4);
    // if links are found
    if(isset($m4[1])){
        $links = array_unique($m4[1]); // multiple links to the same person are counted for one
        foreach($links as $link){
            $link = Bio::fix_broken_link($link);
            if(!in_array($link, $basenames_bio)){
                echo "Link $link does not correspond\n";
            }
            if(!isset($results[$link])){
                $results[$link] = newResultEntry();
            }
            $results[$link]['N_LINKS'] ++; // HERE increment celebrity count
        }
    }
    
    // fill current person
    // do field by field to avoid erasing N_LINKS
    $results[$key]['ID'] = substr($key, 0, -5);
    $results[$key]['NAME'] = $name;
    $results[$key]['B_DATE'] = $bdate;
    $results[$key]['B_PLACE'] = $bplace;
    $results[$key]['B_LG'] = $lg;
    $results[$key]['B_LAT'] = $lat;
    $results[$key]['B_WIKIPEDIA'] = $wikipedia;
    $results[$key]['D_DATE'] = $ddate;
    $results[$key]['D_PLACE'] = $dplace;

    // particular cases
    if($key == 'Bordoni.html'){
        $results[$key]['B_PLACE'] = 'Mezzana Corti, now Cava Manara, Savoy, Italy';
    }
}

// generate csv

$results = array_reverse(jth_sortByKey::sortByKey($results, 'N_LINKS'));

$csv = implode(CSV::SEP, CSV::FIELDS) . "\n";
foreach($results as $person){
    if( $person['B_DATE'] == '' && MacTutor::$config['keep-only-dates']){
        continue;
    }
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
    $new['N_LINKS'] = 0;
    return $new;
}


