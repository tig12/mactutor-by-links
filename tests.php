<?php
/******************************************************************************
    Tests some aspects of MacTutor files
    Not used to generate csv
    
    @license    GPL
    @history    2019-04-28 19:12:27+02:00, Thierry Graff : Creation
********************************************************************************/

define('DS', DIRECTORY_SEPARATOR);

$yaml = file_get_contents('config.yml');
$config = yaml_parse($yaml);
$dir_places = $config['directories']['places'];

test_wikipedia();

// ******************************************************
/**
    Tests the nb of place pages with a link to wikipedia
**/
function test_wikipedia(){
    global $config;
    $dir_places = $config['directories']['places'];
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
    echo "total : $n_total\n";
    echo "nb wikipedia : $n_wiki\n";
    $p = $n_wiki *100 / $n_total;
    echo "$p % ok\n";
}