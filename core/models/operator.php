<?php
/**
 * $Id: setting.php 43 2010-07-07 01:42:43Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class operator extends modelfactory{
    function get_list($page = NULL){
        $this->db->select('#admin',"*",'','id desc');
        if($page){
            $list = $this->db->toPage($page,10);
        }else{
            $list = $this->db->getAll();
        }
        return $list;
    }

    function get_one_operator($id){
        $this->db->select('#admin',"*",'id='.intval($id));
        return $this->db->getRow();
    }

    function change_pass($id,$pass){
        $this->db->update('#admin','id='.intval($id),array('userpass'=>$pass));
        return $this->db->query();
    }

    function add_operator($username,$pass){
        $this->db->insert('#admin',array('username'=>$username,'userpass'=>$pass,'create_time'=>time()));
        return $this->db->query();
    }

    function del_one($id){
        $this->db->delete('#admin','id='.intval($id));
        return $this->db->query();
    }
}