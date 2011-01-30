<?php


class plugin_cla{
    var $plugin_pool = array();
    var $plugin_contents = array();
    
    function plugin_cla(){
        $this->db = & loader::database();
    }
    
    function plugin_info($plugin_name){
        $this->db->select('#plugins','*',"plugin_id='".$plugin_name."'");
        return $this->db->getRow();
    }
    
    function get_hooks($hook_name,$where=''){
        if(!isset($this->plugin_contents[$hook_name])){
            $this->db->select('#plugin_contents','*',"available='true' and content_type='".$hook_name."'".$where);
            $this->plugin_contents[$hook_name]=$this->db->getAll();
        }
        return $this->plugin_contents[$hook_name];
    }
    
    function trigger($hook_name){
        $pars = func_get_args();
        $hook_name = array_shift($pars);
        
        if($hook_name=='custom_page'){
            $hooks = $this->get_hooks($hook_name," and content_path='".IN_CTL.'.'.IN_ACT."'");
        }else{
            $hooks = $this->get_hooks($hook_name);
        }
        if($hooks){
            $return = array();
            foreach($hooks as $v){
                $return[] = $this->invoke($v['plugin_id'],$v['func_name'],$pars);
            }
            return $return;
        }else{
            return false;
        }
    }
    
    function invoke($plugin_name,$plugin_func,$pars=array()){
        if(!isset($this->plugin_pool[$plugin_name])){
            $plugin_path = PLUGINDIR.$plugin_name.'/'.$plugin_name.'.php';
            $plugin_class = 'plugin_'.$plugin_name;
            
            $info = $this->plugin_info($plugin_name);
            include_once($plugin_path);
            $this->plugin_pool[$plugin_name] = new $plugin_class(unserialize($info['plugin_config']));
        }
        return call_user_func_array(array($this->plugin_pool[$plugin_name],$plugin_func),$pars);
    }
}