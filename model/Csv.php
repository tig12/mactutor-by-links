<?php
/******************************************************************************
    
    Code relative to generated csv file.
    
    @license    GPL
    @history    2019-04-29 02:28:01+02:00, Thierry Graff : Creation
********************************************************************************/

class Csv{
    
    /** 
        Separator in the generated csv file.
        Could be in config.
    **/
    const SEP = ';';
    
    /** 
        Fields in the generated csv file.
    **/
    const FIELDS = [
        # MacTutor id, corresponds to P1563 in wikidata.org
        # For example Godel.html has id Godel
        'ID',
        'NAME',
        'N_LINKS',
        'B_DATE',
        'B_PLACE',
        'B_LG',
        'B_LAT',
        'B_WIKIPEDIA',
        'D_DATE',
        'D_PLACE',
    ];
    
}// end class
