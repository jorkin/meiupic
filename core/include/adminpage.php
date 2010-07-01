<?php
/**
 * $Id: pagefactory.php 18 2010-06-05 17:03:28Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

require_once(LIBDIR.'view.class.php');
require_once(LIBDIR.'auth.class.php');
require_once(INCDIR.'pagecore.php');
class adminpage extends pagecore{
    
    function adminpage(){
        global $setting;
        $this->setting = $setting;
        $this->output =& get_output();
        $this->view = new View();
        $this->db =& db();
        $this->auth = new auth();
        
        $this->output->set('open_photo_setting',$this->setting['open_photo']);
    }
}