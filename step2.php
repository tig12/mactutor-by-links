<?php
/******************************************************************************
    Retrieves the pages containing birth places of mathematicians from Mac Tutor
    (https://www-history.mcs.st-andrews.ac.uk/Indexes/Full_Chron.html)
    This step uses the pages downloaded in step1 to compute the place (to limit calls to Mac Tutor server).
    
    @license    GPL
    @history    2019-04-28 08:36:21+02:00, Thierry Graff : Creation
********************************************************************************/

define('DS', DIRECTORY_SEPARATOR);

$BASE_URL = 'https://www-history.mcs.st-andrews.ac.uk';


$yaml = file_get_contents('config.yml');
$config = yaml_parse($yaml);

$dir_bios = $config['directories']['bios'];
$dir_places = $config['directories']['places'];

$p = '#<a href = "../BirthplaceMaps/(.*?.html)"><b>Show birthplace location</b></a>#';
$files_bios = glob("$dir_bios/*.html");

foreach($files_bios as $file_bio){
    // Extract birth place from biography
    // and download the page of this place
    $raw = file_get_contents($file_bio);
    preg_match($p, $raw, $m);
//echo "Parsing file $file_bio\n";
    if(!isset($m[1])){
        // no birth place
        echo "Unable to retrieve birth place in file $file_bio\n";
        continue;
        //echo $raw; exit;
    }
    // particular cases with white spaces
    $m[1] = clean_place($m[1]);
    
    $url = $BASE_URL . '/BirthplaceMaps/' . $m[1];
    $fullpath = $dir_places . DS . $m[1];
    
    if(is_file($fullpath)){
        // if the file has already been retrieved
        // Happens for places where several mathematicians are born
        // This permits to execute file retrieval in several executions
        continue;
    }
    
    echo "Retrieving $url\n";
    copy($url, $fullpath);
    dosleep(2); // kepp cool with the server
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
    if(strpos($str, ' ') !==  false){
        // happens for Rimavska Sobota
        $clean = str_replace(' ', '_', $str);
    }
    return $clean;
}

// ******************************************************
/** 
    Equivalent to sleep(), but echoes a message and second number does not need to be integer
    @param  $x  positive number ; seconds
**/
function dosleep($x){
    echo "  dosleep($x) ";
    usleep($x * 1000000);
    echo " - end sleep\n";
}
