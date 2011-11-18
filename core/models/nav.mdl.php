<?php
/**
 * $Id: nav.mdl.php 208 2011-11-15 10:37:19Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010-2011 meiu.cn lingter@gmail.com
 */

class nav_mdl extends modelfactory{
    var $table_name = '#@nav';
    var $default_order = 'sort asc';

    function get_enabled_navs(){
        $this->db->select($this->table_name,$this->default_cols,'enable=1',$this->default_order);
        return $this->db->getAll();
    }
}