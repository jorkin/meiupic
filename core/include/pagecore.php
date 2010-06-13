<?php
/**
 * $Id: pagefactory.php 18 2010-06-05 17:03:28Z lingter $
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
            return $_GET[$key];
        }
        return $default;
    }

    function getPost($key,$default=''){
        if(isset($_POST[$key])){
            return $_POST[$key];
        }
        return $default;
    }

    function getRequest($key,$default=''){
        if(isset($_REQUEST[$key])){
            return $_REQUEST[$key];
        }
        return $default;
    }
}