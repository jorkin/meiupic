<?php

class cache_cla{
    var $cache = array();
    
    function cache_cla(){
        $this->config =& Loader::config();
        $engine = $this->config['cache_engine'];
        include_once(LIBDIR.'/cache/'.$engine.'.php');
        $enginename = 'cache_'.$engine;
        $cache_policy = isset($this->config['cache_policy'])?$this->config['cache_policy']:array();
        $this->_cache = new $enginename($cache_policy);
    }

    function set($id,$data,$policy = null){
        $this->cache[$id] = $data;
        $this->_cache->set($id,$data,$policy);
    }
    function get($id){
        if(isset($cache[$id])){
            return $this->cache[$id];
        }
        $this->cache[$id] = $this->_cache->get($id);
        return $this->cache[$id];
    }
    function remove($id){
        unset($this->cache[$id]);
        $this->_cache->remove($id);
    }
}