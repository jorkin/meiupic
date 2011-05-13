<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class json_cla{
    
    function encode($str){
        if(!function_exists('json_encode')){
            include_once('Services_JSON.php');
            return Services_JSON::encode($str);
        }
        return json_encode($str);
    }

    function decode($str){
        if(!function_exists('json_decode')){
            include_once('Services_JSON.php');
            return Services_JSON::decode($str);
        }
        return json_decode($str);
    }
}
