<?php

require_once(LIBDIR.'auth.php');
class modelfactory{
    
    function modelfactory(){
        $this->output =& get_output();
        $this->db =& db();
        $this->auth = new auth();
    }
}