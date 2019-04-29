<?php
/******************************************************************************
    Extract birth places from biographies and download the pages of these places.
    Uses the biographies downloaded in step1.
    
    @license    GPL
    @history    2019-04-28 08:36:21+02:00, Thierry Graff : Creation
********************************************************************************/

require_once 'model/MacTutor.php';
require_once 'model/Bio.php';

$dir_bios = MacTutor::$config['directories']['bios'];
$dir_places = MacTutor::$config['directories']['places'];

$files_bios = glob("$dir_bios/*.html");

echo "Start place retrieval using biography files ...\n";

$p = '#<a href = "../BirthplaceMaps/(.*?.html)"><b>Show birthplace location</b></a>#';

$n_without_birth = 0;
foreach($files_bios as $file_bio){

    $file_bio = Bio::clean_name($file_bio);
    $raw = file_get_contents($file_bio);
    preg_match($p, $raw, $m);
//echo "Parsing file $file_bio\n";
    if(!isset($m[1])){
        // no birth place
        echo "Unable to retrieve birth place in file " . basename($file_bio) . "\n";
        $n_without_birth++;
        continue;
        //echo $raw; exit;
    }
    // particular cases with white spaces
    $m[1] = MacTutor::clean_place($m[1]);
    
    $url = MacTutor::BASE_URL . '/BirthplaceMaps/' . $m[1];
    $fullpath = $dir_places . DS . $m[1];
    
    if(is_file($fullpath)){
        // if the file has already been retrieved
        // Happens when several mathematicians are born in the same place
        // Also permits to perform file retrieval in several executions
        continue;
    }
    
    echo "Retrieving $url\n";
    copy($url, $fullpath); // HERE download file
    MacTutor::dosleep(2); // kepp cool with the server
}

echo "... Place retrieval finished\n";
echo "$n_without_birth bios without birth place\n";
