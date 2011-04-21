<?php
/**
 * $Id: plugin.class.php 97 2011-01-30 02:54:36Z lingter $
 *
 * The plugin API is located in this file
 *
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */


class plugin_cla{
    var $plugin_pool = array();
    var $plugin_contents = array();
    var $plugin_filters = array();
    var $merged_filters = array();
    var $current_filter = array();
    
    function plugin_cla(){
        $this->db = & loader::database();
    }
    /**
     * Load enabled plugins
     *
     * @return void
     * @author Lingter
     */
    function init_plugins(){
        $cache =& loader::lib('cache');
        
        $plugins = $cache->get('plugins');
        if($plugins === false){
            $this->db->select('#@plugins','plugin_id,plugin_name,plugin_config',"available='true'");
            $plugins = $this->db->getAll();
            $cache->set('plugins',$plugins);
        }
        include_once(INCDIR.'plugin.php');
        foreach((array) $plugins as $v){
            $plugin_path = PLUGINDIR.$v['plugin_id'].'/'.$v['plugin_id'].'.php';
            if(file_exists($plugin_path)){
                $plugin_class = 'plugin_'.$v['plugin_id'];
                include_once($plugin_path);
                $plugin_config = $v['plugin_config']?unserialize($v['plugin_config']):array();
                $this->plugin_pool['plugin_'.$v['plugin_id']] = new $plugin_class($plugin_config);
                $this->plugin_pool['plugin_'.$v['plugin_id']]->init();
            }
        }
    }
    /**
     * Check whether trigget exists!
     *
     * @param string $hook_name 
     * @param bool $function_to_check 
     * @return int priority
     * @author Linter
     */
    function has_trigger($hook_name,$function_to_check = false){
        return $this->has_filter($hook_name,$function_to_check);
    }
    /**
     * Check whether filter exists!
     *
     * @param string $hook_name 
     * @param bool $function_to_check 
     * @return int priority
     * @author Linter
     */
    function has_filter($hook_name,$function_to_check = false){
        $has = !empty($this->plugin_filters[$hook_name]);
        if ( false === $function_to_check || false == $has )
            return $has;

        if ( !$idx = $this->_build_unique_id($hook_name, $function_to_check, false) )
            return false;
        
        
        foreach ( (array) array_keys($this->plugin_filters[$hook_name]) as $priority ) {
            if ( isset($this->plugin_filters[$hook_name][$priority][$idx]) )
                return $priority;
        }

        return false;
    }
    
    function add_trigger($hook_name,$func,$priority = 10){
        return $this->add_filter($hook_name,$func,$priority);
    }
    
    function add_filter($hook_name,$func,$priority = 10){
        $idx = $this->_build_unique_id($hook_name,$func,$priority);
        $this->plugin_filters[$hook_name][$priority][$idx] = $func;
        unset($this->merged_filters[ $hook_name ]);
        return true;
    }
    
    function remove_trigger($hook_name,$func,$priority = 10) {
        return $this->remove_filter($hook_name,$func,$priority);
    }
    
    function remove_filter($hook_name,$func,$priority = 10) {
        $function_to_remove = $this->_build_unique_id($hook_name, $func, $priority);

        $r = isset($this->plugin_filters[$hook_name][$priority][$function_to_remove]);

        if ( true === $r) {
            unset($this->plugin_filters[$hook_name][$priority][$function_to_remove]);
            if ( empty($this->plugin_filters[$hook_name][$priority]) )
                unset($this->plugin_filters[$hook_name][$priority]);
            if( isset($this->merged_filters[$hook_name]) )
                unset($this->merged_filters[$hook_name]);
        }

        return $r;
    }
    
    function remove_all_triggers($hook_name, $priority = false){
        return $this->remove_all_filters($hook_name, $priority);
    }
    
    function remove_all_filters($hook_name, $priority = false) {

        if( isset($this->plugin_filters[$hook_name]) ) {
            if( false !== $priority && isset($this->plugin_filters[$hook_name][$priority]) )
                unset($this->plugin_filters[$hook_name][$priority]);
            else
                unset($this->plugin_filters[$hook_name]);
        }

        if( isset($this->merged_filters[$hook_name]) )
            unset($this->merged_filters[$hook_name]);

        return true;
    }
    
    function _build_unique_id($hook_name,$func,$priority){
        $idx = '';
        if(is_array($func)){
            $class_name = is_object($func[0])?get_class($func[0]):'plugin_'.$func[0];
            $func_name = isset($func[1])?$func[1]:'';
            $idx = $class_name.'_'.$func_name;
        }elseif(is_string($func)){
            $idx = $func;
        }
        return $idx;
    }
    
    function trigger($hook_name){
        $pars = func_get_args();
        $hook_name = array_shift($pars);
        
        $this->current_filter[] = $hook_name;
        
        if(!isset($this->plugin_filters[$hook_name])){
            array_pop($this->current_filter);
            return false;
        }
        
        // Sort
        if ( !isset( $this->merged_filters[ $hook_name ] ) ) {
            ksort($this->plugin_filters[$hook_name]);
            $this->merged_filters[ $hook_name ] = true;
        }
        
        reset($this->plugin_filters[$hook_name]);
        do {
            foreach((array)current($this->plugin_filters[$hook_name]) as $v){
                if(is_array($v)){
                    $plugin_name = is_object($v[0])?get_class($v[0]):'plugin_'.$v[0];
                    $func = $v[1];
                    if(isset($this->plugin_pool[$plugin_name]) && method_exists($this->plugin_pool[$plugin_name],$func)){
                        call_user_func_array(array($this->plugin_pool[$plugin_name],$func),$pars);
                    }else{
                        exit(lang('plugin_can_not_call',$plugin_name.'::'.$func));
                    }
                }elseif(is_string($v)){
                    if(function_exists($v)){
                        call_user_func_array($v,$pars);
                    }else{
                        exit(lang('plugin_can_not_call',$plugin_name.','.$func));
                    }
                }
            }
        } while ( next($this->plugin_filters[$hook_name]) !== false );
        
        array_pop($this->current_filter);
        return true;
    }
    
    function filter($hook_name, $value){
        $pars = func_get_args();
        $hook_name = array_shift($pars);
        $this->current_filter[] = $hook_name;
        if(!isset($this->plugin_filters[$hook_name])){
            array_pop($this->current_filter);
            return $value;
        }
        
        // Sort
        if ( !isset( $this->merged_filters[ $hook_name ] ) ) {
            ksort($this->plugin_filters[$hook_name]);
            $this->merged_filters[ $hook_name ] = true;
        }
        
        reset($this->plugin_filters[$hook_name]);
        do {
            foreach((array)current($this->plugin_filters[$hook_name]) as $v){
                $pars[0] = $value;
                if(is_array($v)){
                    $plugin_name = is_object($v[0])?get_class($v[0]):'plugin_'.$v[0];
                    $func = $v[1];
                    if(isset($this->plugin_pool[$plugin_name]) && method_exists($this->plugin_pool[$plugin_name],$func)){
                        $value = call_user_func_array(array($this->plugin_pool[$plugin_name],$func),$pars);
                    }else{
                        exit(lang('plugin_can_not_call',$plugin_name.'::'.$func));
                    }
                }elseif(is_string($v)){
                    if(function_exists($v)){
                        $value = call_user_func_array($v,$pars);
                    }else{
                        exit(lang('plugin_can_not_call',$plugin_name.','.$func));
                    }
                }
            }
        } while ( next($this->plugin_filters[$hook_name]) !== false );
        array_pop($this->current_filter);
        return $value;
    }
    
    function current_trigger(){
        return $this->current_filter();
    }
    
    function current_filter(){
        return end($this->current_filter);
    }
}