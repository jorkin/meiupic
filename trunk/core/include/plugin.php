<?php

class plugin{
    var $config = array();
    
    function plugin($config = null){
        if(!is_null($config)){
            $this->config = array_merge($this->config, $config);
        }
        $this->db =& loader::database();
        $this->plugin_mgr =& loader::lib('plugin');
    }
    
    function init(){
        ;
    }
}