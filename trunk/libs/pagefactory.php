<?php

require_once(LIBDIR.'view.php');
require_once(LIBDIR.'auth.php');
class pagefactory{
    
    function pagefactory(){
        global $setting;
        $this->setting = $setting;
        $this->output =& get_output();
        $this->view = new View();
        $this->db =& db();
        $this->auth = new auth();
    }
    
    function isPost(){
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            return true;
        }
        return false;
    }
}