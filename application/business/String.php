<?php

class Business_String{

    public static function formatShowMoney($str){
        $strFormat = str_replace('.',',',$str);
        echo 'R$ '.$strFormat;
    }

    public static function formatPermalink($str){
        $string = str_replace(array('[\', \']'), '', $str);

        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);

        return strtolower(trim($string, '-'));
    }

}