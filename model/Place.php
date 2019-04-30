<?php
/******************************************************************************
    
    Operations done on a page containing a place in Mac Tutor

    @license    GPL
    @history    2019-04-29 01:35:10+02:00, Thierry Graff : Creation from MacTutor.php
********************************************************************************/

class Place{
    
    
    // ******************************************************
    /**
        @param  $file_place Relative path of a place file ; ex "Amiens.html"
        @return  Array with 3 elements : longitude, latitude, wikipedia url
                  If element(s) can't be computed, contain a empty string
    **/
    public static function get_infos($file_place){
        $lg = $lat = $wikipedia = '';
        $fullpath_place = MacTutor::$config['directories']['places'] . DS . $file_place;
        $raw = file_get_contents($fullpath_place);
        // longitude, latitude
        $pLgLat = '@Its latitude and longitude are <b>(\d+)&#176;(\d+\'[N|S]) (\d+)&#176;(\d+\'[E|W])</b>@';
        preg_match($pLgLat, $raw, $m1);
        if(count($m1) == 5){
            $lg = Place::compute_lat($m1[1], $m1[2]);
            $lat = Place::compute_lg($m1[3], $m1[4]);
        }
        // wikipedia
        $pWikipedia ='@<a href=(https://en.wikipedia.org/wiki/.*?) @';
        preg_match($pWikipedia, $raw, $m2);
        
        if(count($m2) == 2){
            $wikipedia = $m2[1];
        }
        return [$lg, $lat, $wikipedia];
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
