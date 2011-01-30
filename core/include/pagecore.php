<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
class pagecore{
    
    function pagecore(){
        /*global $setting;
        $this->setting = $setting;*/
        $this->output =& loader::lib('output');
        $this->db =& loader::database();
        
        $this->output->set('site_title','美优相册系统2.0');
        $this->output->set('site_keyword','相册,php');
        $this->output->set('site_description','美优相册系统是一个单用户的在线相册管理工具。');
    }
    
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