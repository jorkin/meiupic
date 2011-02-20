<?php

class uri_cla{
    
    function pathinfo(){
        if ( ! isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == ''){
            $strlen = strlen($_SERVER['SCRIPT_NAME']);
            $totallen = strlen($_SERVER['PHP_SELF']);
            return substr($_SERVER['PHP_SELF'],$strlen,$totallen);
        }else{
            return $_SERVER['PATH_INFO'];
        }
    }
    
    function mk_uri($ctl='default',$act='index',$pars=array()){
        global $base_path;
        $arr = array();
        if($ctl!='default'){
            $arr['ctl'] = $ctl;
        }
        if($act!='index'){
            $arr['act'] = $act;
        }
        $url = '';
        $arr = array_merge($arr,$pars);
        foreach($arr as $k=>$v){
            $url .= $k.'='.rawurlencode($v).'&';
        }
        if($url){
            $url =  $base_path.'?'.str_replace('&','&amp;',rtrim($url,'&'));
        }else{
            $url = $base_path;
        }
        $plugin =& loader::lib('plugin');
        $url = $plugin->filter('make_url',$url,$ctl,$act,$pars);
        return $url;
    }
    
    function parse_uri(){
        $arg['ctl'] = isset($_GET['ctl'])?$_GET['ctl']:'default';
        $arg['act'] = isset($_GET['act'])?$_GET['act']:'index';
        unset($_GET['ctl']);
        unset($_GET['act']);
        $arg['pars'] = $_GET;
        
        $plugin =& loader::lib('plugin');
        $plugin_uri = $plugin->filter('parse_url',$arg);
        return $plugin_uri;
    }
    
}