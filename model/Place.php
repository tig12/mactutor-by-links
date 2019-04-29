<?php
/******************************************************************************
    
    Operations done on a page containing a place in Mac Tutor

    @license    GPL
    @history    2019-04-29 01:35:10+02:00, Thierry Graff : Creation from MacTutor.php
********************************************************************************/

class Place{
    
    
    // ******************************************************
    /**
        @param $
    **/
    public static function getInfo($file_place){
        $fullpath_place = $dir_places . DS . $file_place;
        $raw2 = file_get_contents($fullpath_place);
        // longitude, latitude of birth place
        preg_match($pLgLat, $raw2, $m4);
        if(count($m4) == 5){
            $lg = Place::compute_lat($m4[1], $m4[2]);
            $lat = Place::compute_lg($m4[3], $m4[4]);
        }
        else{
            echo "Cannot parse $fullpath_place\n";
            $lg = $lat = '';
        }
    }
    
    // ******************************************************
    /**
        Computes longitude of a place page.
        @param $str_deg A string like "55"
        @param $$str_min A string like "34'E"
        @return decimal degrees
    **/
    public static function compute_lg($str_deg, $str_min){
        $deg = $str_deg;
        $min = substr($str_min, 0, -2);
        $EW = substr($str_min, -1);
        switch($EW){
            case 'E' : break;
            case 'W' : $deg *= -1; break;
            default: throw new Exception("Unable to parse longitude - EW = $EW");
        }
        $deg += round($min / 60, 6);
        return $deg;
    }
    
    
    // ******************************************************
    /**
        Computes latitude from a place page.
        @param $str_deg A string like "55"
        @param $$str_min A string like "41'N"
        @return decimal degrees
    **/
    public static function compute_lat($str_deg, $str_min){
        $deg = $str_deg;
        $min = substr($str_min, 0, -2);
        $NS = substr($str_min, -1);
        switch($NS){
            case 'N' : break;
            case 'S' : $deg *= -1; break;
            default: throw new Exception("Unable to parse latitude - NS = $NS");
        }
        $deg += round($min / 60, 6);
        return $deg;
    }
    

}// end class
