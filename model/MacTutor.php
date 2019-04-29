<?php
/******************************************************************************

    Initialization and utilities for mac-tutor code.
    @license    GPL
    @history    2019-04-29, Thierry Graff : Creation
********************************************************************************/

define('DS', DIRECTORY_SEPARATOR);

MacTutor::init(); // ??? I thought there was static initializer in php7

class MacTutor{
    
    const BASE_URL = 'https://www-history.mcs.st-andrews.ac.uk';

    public static $config = [];
    
    // ******************************************************
    /**
    
    **/
    public static function init(){
        $yaml = file_get_contents(dirname(__DIR__) . DS . 'config.yml');
        self::$config = yaml_parse($yaml);
    }
    
    // ******************************************************
    /**
        Fix problems of white space in place names, both in Bio page and in Place page.
    **/
    public static function clean_place($str){
        if(substr($str, 0, 1) == ' '){
            // happens for New-York, Washington
            return trim(substr($str, 1));
        }
        else if(strpos($str, ' ') !==  false){
            // happens for Rimavska Sobota
            return trim(str_replace(' ', '_', $str));
        }
        return trim($str);
    }
    
    public static function zzz_clean_place($str){
        $clean = $str;
        if(substr($str, 0, 1) == ' '){
            // happens for New-York, Washington
            $clean = substr($str, 1);
        }
        else if(strpos($str, ' ') !==  false){
            // happens for Rimavska Sobota
            $clean = str_replace(' ', '_', $str);
        }
        return trim($clean);
    }
    
    
    // ******************************************************
    /** 
        Equivalent to sleep(), but echoes a message ; seconds don't need to be integer
        @param  $x  positive number ; seconds
    **/
    public static function dosleep($x){
        echo "  dosleep($x) ";
        usleep($x * 1000000);
        echo " - end sleep\n";
    }
    
}// end class
