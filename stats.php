<?php
/********************************************************************************
    Statistics on generated csv file.
    
    @license    GPL v2 or later
    @history    2019-04-29 03:36:03+02:00, Thierry Graff : Creation 
********************************************************************************/

require_once 'model/MacTutor.php';
require_once 'model/Csv.php';

numbers();


// ******************************************************
/**
**/
function numbers(){
    require_once 'lib/jth_csvAssociative.php';
    $csv = jth_csvAssociative::csvAssociative(MacTutor::$config['result-csv'], Csv::SEP);
    
    $nTotal = count($csv);
    $nLgLat = 0;
    $nDate = 0;
    $nDateLgLat = 0;
    
    foreach($csv as $row){
        if($row['B_DATE'] != '')
            $nDate++;
        if($row['B_LG'] != '' && $row['B_LG'] != '')
            $nLgLat++;
        if($row['B_DATE'] != '' && $row['B_LG'] != '' && $row['B_LG'] != '')
            $nDateLgLat++;
    }
    echo "total = $nTotal\n";
    echo "date = $nDate\n";
    echo "lg lat = $nLgLat\n";
    echo "date AND lg lat = $nDateLgLat\n";
}

