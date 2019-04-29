<?php
/******************************************************************************
    Retrieves the pages containing biographies of mathematicians on local machine
    Uses https://www-history.mcs.st-andrews.ac.uk/Indexes/Full_Chron.html
    
    @license    GPL
    @history    2019-04-27 22:31:08+02:00, Thierry Graff : Creation
********************************************************************************/

require_once 'model/MacTutor.php';
require_once 'model/Bio.php';


$index_page = MacTutor::$config['index-page'];
$dir_bios = MacTutor::$config['directories']['bios'];

echo "Parsing $index_page\n";

$raw = file_get_contents($index_page);
preg_match_all('#<a href="(../Biographies/.*?.html)">#', $raw, $m);

echo "Start biography retrieval...\n";
foreach($m[1] as $match){
    
    $url = MacTutor::BASE_URL . str_replace('..', '', Bio::clean_name($match));
    $filename = str_replace('../Biographies/', '', Bio::clean_name($match));
    $fullpath = $dir_bios . DS . $filename;
    
    if(is_file($fullpath)){
        // if the file has already been retrieved
        // This permits to execute file retrieval in several executions
        continue;
    }
    
    echo "Retrieving $url\n";
    copy($url, $fullpath); // HERE download file
    MacTutor::dosleep(2); // kepp cool with the server
}

echo "... Biography retrieval finished\n";

