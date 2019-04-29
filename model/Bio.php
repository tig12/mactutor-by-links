<?php
/******************************************************************************

    Operations done on a page containing a biography in Mac Tutor
    
    @license    GPL
    @history    2019-04-29 01:34:32+02:00, Thierry Graff : Creation from MacTutor.php
********************************************************************************/

class Bio{
    
    // ******************************************************
    /**
        Fix problem of quote in name.
    **/
    public static function clean_name($str){
        return str_replace("'", '', $str); 
    }
    
    
    // ******************************************************
    /** 
        In some mathematician pages, some links to other mathematician need to be fixed.
    **/
    public static function fix_broken_link($link){
        if($link == 'al-Kashi.html') $link = 'Al-Kashi.html';
        else if($link == 'al-Haytham.html') $link = 'Al-Haytham.html';
        else if($link == 'al-Khazin.html') $link = 'Al-Khazin.html';
        else if($link == 'al-Banna.html') $link = 'Al-Banna.html';
        else if($link == 'Morgan.html') $link = 'De_Morgan.html';
        else if($link == 'Lane.html') $link = 'MacLane.html';
        else if($link == 'Rham.html') $link = 'De_Rham.html';
        else if($link == 'Olubummo_Adegoke.html') $link = 'Olubummo.html';
        else if($link == 'MacLaurin.html') $link = 'Maclaurin.html';
        return $link;
    }
    
    
    // ******************************************************
    /**
        Converts a string to a date
        Ex : 5 August 1802 in Frindöe (near Stavanger), Norway returns 1802-08-05
        @param  $str The string to parse
        @return  An array with two elements :
                    - day in iso 8601 format YYYY-MM-DD
                    - place : string identical to the place in $str
    **/
    public static function compute_date($str){
        $months = [
            'January' => '01',
            'Jan' => '01',
            'February' => '02',
            'Feb' => '02',
            'March' => '03',
            'April' => '04',
            'Apr' => '04',
            'May' => '05',
            'June' => '06',
            'Jun' => '06',
            'July' => '07',
            'August' => '08',
            'Aug' => '08',
            'September' => '09',
            'October' => '10',
            'November' => '11',
            'December' => '12',
        ];
        $p1 = '/(\d+) (\w+) (\d+) in (.*)/'; // pattern for most common case
        preg_match($p1, $str, $m);
        if(count($m) == 5){
            $day = $m[3] . '-' . $months[$m[2]] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $place = $m[4];
        }
        else{
            $day = $place = '';
        }
        return [$day, $place];
    }

}// end class
