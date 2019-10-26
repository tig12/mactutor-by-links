<?php
/******************************************************************************
    
    @license    GPL
    @history    2019-10-26 22:25:05+02:00, Thierry Graff : Creation from MacTutor.php
********************************************************************************/

class Honour{
    
    // ******************************************************
    /**
        @param $
    **/
    public static function cleanName($str){
        $str = str_replace('&ouml;', 'ö', $str);
        $str = str_replace('&eacute;', 'é', $str);
        $str = str_replace('&aacute;', 'á', $str);
        $str = str_replace('&oacute;', 'ó', $str);
        $str = str_replace(' Winners', '', $str);
        $str = strip_tags($str);
        return $str;
    }

}// end class
