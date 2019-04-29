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
Result of execution 2019-04-29 :
total = 2841
birth
  date = 2311
  lg lat = 2635
  date AND lg lat = 2291
---
death date = 2318                                                                                       

**/
function numbers(){
    require_once 'lib/jth_csvAssociative.php';
    $csv = jth_csvAssociative::csvAssociative(MacTutor::$config['result-csv'], Csv::SEP);
    
    $nTotal = count($csv);
    $nLgLat = 0;
    $nDate = 0;
    $nDateLgLat = 0;
    $nDeath = 0;
    $bdates = [];
    
    foreach($csv as $row){
        if($row['B_DATE'] != ''){
            $nDate++;
            $bdates[] = $row['B_DATE'];
        }
        if($row['B_LG'] != '' && $row['B_LG'] != '')
            $nLgLat++;
        if($row['B_DATE'] != '' && $row['B_LG'] != '' && $row['B_LG'] != '')
            $nDateLgLat++;
        if($row['D_DATE'] != '')
            $nDeath++;
    }
    natsort($bdates);
//    $date_min = $bdates[0];
//    $date_max = $bdates[count($bdates) - 1];
    
    echo "total = $nTotal\n";
    echo "birth\n";
    echo "  date = $nDate\n";
    echo "  lg lat = $nLgLat\n";
//    echo "dates [$date_min - $date_max]\n";
    echo "  date AND lg lat = $nDateLgLat\n";
    echo "---\n";
    echo "death date = $nDeath\n";
}

