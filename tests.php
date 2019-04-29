<?php
/******************************************************************************
    Tests some aspects of MacTutor files
    Not used to generate csv
    
    @license    GPL
    @history    2019-04-28 19:12:27+02:00, Thierry Graff : Creation
********************************************************************************/

require_once 'model/MacTutor.php';

test_wikipedia();

// ******************************************************
/**
    Tests the nb of place pages with a link to wikipedia
    Exec 2019-04-29 03:35:16+02:00 :
    nb wikipedia : 1135 / 1273 : 89.16 % ok
**/
function test_wikipedia(){
    $dir_places = MacTutor::$config['directories']['places'];
    $files_places = glob($dir_places . '/*');
    // stats
    $n_total = count($files_places);
    $n_wiki = 0;
    
    $pWiki = '@<a href=(https://en.wikipedia.org/wiki/.*?) @';
    
    foreach($files_places as $file_place){
        $raw = file_get_contents($file_place);
        preg_match($pWiki, $raw, $m);
        if(count($m) == 2){
            $n_wiki++;
        }
    }
    echo "nb wikipedia : $n_wiki / $n_total : ";
    $p = $n_wiki *100 / $n_total;
    echo round($p, 2) . " % ok\n";
}