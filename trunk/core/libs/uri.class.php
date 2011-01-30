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
        
        $plugin =& loader::lib('plugin');
        $plugin_uri = $plugin->trigger('make_url',$ctl,$act,$pars);
        if(false === $plugin_uri){
            $arr = array();
            if($ctl!='default'){
                $arr['ctl'] = $ctl;
            }
            if($act!='index'){
                $arr['act'] = $act;
            }
            $url = '';
            $pars = array_merge($arr,$pars);
            foreach($pars as $k=>$v){
                $url .= $k.'='.rawurlencode($v).'&';
            }
            if($url){
                return $base_path.'?'.str_replace('&','&amp;',rtrim($url,'&'));
            }
            return $base_path;
        }else{
            return $plugin_uri[0];
        }
    }
    
    function parse_uri(){
        $plugin =& loader::lib('plugin');
        $plugin_uri = $plugin->trigger('parse_url');
        if(false === $plugin_uri){
            $arg['ctl'] = isset($_GET['ctl'])?$_GET['ctl']:'default';
            $arg['act'] = isset($_GET['act'])?$_GET['act']:'index';
            unset($_GET['ctl']);
            unset($_GET['act']);
            $arg['pars'] = $_GET;
            return $arg;
        }else{
            return $plugin_uri[0];
        }
    }
    
}