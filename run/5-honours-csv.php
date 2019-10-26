<?php
/******************************************************************************
    Creates a csv file using informations retrieved in steps 1 and 2
    This file contains mathematicians with infos about birth / death
    and the nb of links towards each of them.
    
    @license    GPL
    @history    2019-10-26 22:19:52+02:00, Thierry Graff : Creation
********************************************************************************/

require_once 'model/MacTutor.php';
require_once 'model/Honour.php';
require_once 'model/Csv.php';
require_once 'vendor/jth_sortByKey.php';

$outfile = MacTutor::$config['directories']['mactutor'] . DS . 'mactutor-honours.csv';

$dir_honours = MacTutor::$config['directories']['honours'];

$startPage = MacTutor::$config['commands']['honours']['start-page'];

$raw = file_get_contents($startPage);
preg_match_all('#<a href ?= ?(.*?)>(.*?)</a>#', $raw, $m);

$res = "NAME;URL\n";

$N = count($m[0]);
for($i=2; $i < $N-5; $i++){
    if($i == 8){
        continue; // Fields medal repeated
    }
    $name = $m[2][$i];
    $name = Honour::cleanName($name);
    
    $href = $m[1][$i];
    $href = str_replace('"', '', $href);
    $honourPage = $dir_honours . DS . $href;

    $url = MacTutor::BASE_URL . '/Honours/' . $href;

    //echo "$name\n";
    $res .= "$name;$url\n";
}


file_put_contents($outfile, $res);
echo "Generated $outfile\n";

