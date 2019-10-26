<?php
/******************************************************************************
    
    @license    GPL
    @history    2019-10-26 20:51:36+02:00, Thierry Graff : Creation
********************************************************************************/

require_once 'model/MacTutor.php';
require_once 'model/Honour.php';
require_once 'model/Csv.php';

$dir_honours = MacTutor::$config['directories']['honours'];

$startPage = MacTutor::$config['commands']['honours']['start-page'];

$raw = file_get_contents($startPage);
preg_match_all('#<a href ?= ?(.*?)>(.*?)</a>#', $raw, $m);

$N = count($m[0]);
for($i=2; $i < $N-5; $i++){
    echo "\n";
    $name = $m[2][$i];
    $name = Honour::cleanName($name);
    
    $href = $m[1][$i];
    $href = str_replace('"', '', $href);
    $url = MacTutor::BASE_URL . '/Honours/' . $href;
    
    $dest = $dir_honours . DS . $href;
    $dest = str_replace('../ems/', '', $dest);
    $dest = str_replace('../BMC/', '', $dest);
    
    echo "$name\n";
    
    if(is_file($dest)){
        echo "CONTINUE - already stored\n";
        continue;
    }
    echo "$url\n";
    echo "$dest\n";
    
    echo "Retrieving $url\n";
    copy($url, $dest); // HERE download file
    MacTutor::dosleep(1.5); // kepp cool with the server
}
