<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
class pagecore{
    function isPost(){
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            return true;
        }
        return false;
    }

    function getGet($key,$default=''){
        if(isset($_GET[$key])){
            if(!get_magic_quotes_gpc())
            {
                if(is_array($_GET[$key])){
                    return array_map('addslashes',$_GET[$key]);
                }else{
                    return addslashes($_GET[$key]);
                }
            }
            return $_GET[$key];
        }
        return $default;
    }

    function getPost($key,$default=''){
        if(isset($_POST[$key])){
            if(!get_magic_quotes_gpc())
            {
                if(is_array($_POST[$key])){
                    return array_map('addslashes',$_POST[$key]);
                }else{
                    return addslashes($_POST[$key]);
                }
            }
            return $_POST[$key];
        }
        return $default;
    }

    function getRequest($key,$default=''){
        if(isset($_REQUEST[$key])){
            if(!get_magic_quotes_gpc())
            {
                if(is_array($_REQUEST[$key])){
                    return array_map('addslashes',$_REQUEST[$key]);
                }else{
                    return addslashes($_REQUEST[$key]);
                }
            }
            return $_REQUEST[$key];
        }
        return $default;
    }
}