<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

require_once(LIBDIR.'view.class.php');
require_once(INCDIR.'pagecore.php');
class frontpage extends pagecore{
    
    function frontpage(){
        global $setting;
        $this->setting = $setting;
        $this->output =& get_output();
        $this->view = new View();
        $this->db =& db();
        
        $this->output->set('site_title',$this->setting['site_title']);
        $this->output->set('site_keyword',$this->setting['site_keyword']);
        $this->output->set('site_description',$this->setting['site_description']);
    }
    
}