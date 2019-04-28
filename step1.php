<?php
/******************************************************************************
    Retrieves the pages containing biographies of mathematicians from Mac Tutor
    (https://www-history.mcs.st-andrews.ac.uk/Indexes/Full_Chron.html)
    
    @license    GPL
    @history    2019-04-27 22:31:08+02:00, Thierry Graff : Creation
********************************************************************************/

define('DS', DIRECTORY_SEPARATOR);

$BASE_URL = 'https://www-history.mcs.st-andrews.ac.uk';


$yaml = file_get_contents('config.yml');
$config = yaml_parse($yaml);
$index_page = $config['index-page'];
$dir_bios = $config['directories']['bios']; // directory containing the biographies

echo "Parsing $index_page\n";

$raw = file_get_contents($index_page);
preg_match_all('#<a href="(../Biographies/.*?.html)">#', $raw, $m);

foreach($m[1] as $match){
    $url = $BASE_URL . str_replace('..', '', $match);
    $filename = str_replace('../Biographies/', '', $match);
    $fullpath = $dir_bios . DS . $filename;
    
    if(is_file($fullpath)){
        // if the file has already been retrieved
        // This permits to execute file retrieval in several executions
        continue;
    }
    
    echo "Retrieving $url\n";
    copy($url, $fullpath);
    dosleep(3); // kepp cool with the server
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
