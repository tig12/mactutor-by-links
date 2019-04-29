<?php 
// Software released under the General Public License (version 2 or later), available at
// http://www.gnu.org/copyleft/gpl.html
/****************************************************************************************
    
    @license    GPL
    @copyright  jetheme.org
    @history    2017-02-02 10:44:42+01:00, Thierry Graff : Creation
    @history    2017-03-18 19:25:11+01:00, Thierry Graff : replace old function (found at http://php.net/manual/en/function.str-getcsv.php)
                                                           by new version because of memory overflow

****************************************************************************************/
class jth_csvAssociative{
    
    
    /**
        Fills a csv file to an array of associative arrays
        The first line of the array is considered as the header, containing the field names
        @param      $filename Absolute path to the csv file
        @param      $delimiter field delimiter (one character only).
    **/
    public static function csvAssociative($filename, $delimiter=';'){
        $lines = @file($filename, FILE_IGNORE_NEW_LINES);
        if(!$lines ){
            return false;
        }
        $n = count($lines);
        $fields = explode($delimiter, $lines[0]);
        $nfields = count($fields);
        $res = [];
        $cur = [];
        for($i=1; $i < $n; $i++){
            $tmp = explode($delimiter, $lines[$i]);
            for($j=0; $j < $nfields; $j++){
                $cur[$fields[$j]] = $tmp[$j];
            }
            $res[] = $cur;
        }
        return $res;
    }
    
    
}// end class