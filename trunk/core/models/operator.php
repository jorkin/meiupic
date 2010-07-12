<?php
/**
 * $Id: setting.php 43 2010-07-07 01:42:43Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class operator extends modelfactory{
    function getList($page = NULL){
        $this->db->select('#admin',"*",'','id desc');
        if($page){
            $list = $this->db->toPage($page,10);
        }else{
            $list = $this->db->getAll();
        }
        return $list;
    }
}