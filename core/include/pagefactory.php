<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

require_once(LIBDIR.'view.class.php');
require_once(LIBDIR.'auth.class.php');
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