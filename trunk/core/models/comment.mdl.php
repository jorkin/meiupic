<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010-2011 meiu.cn lingter@gmail.com
 */

class comment_mdl extends modelfactory{
    var $table_name = '#@comments';
    var $pageset = 10;
    
    function _filters($filters){
        $str = 'status=1 and pid=0';
        if(isset($filters['ref_id'])){
            $str .= ' and ref_id='.intval($filters['ref_id']);
        }
        if(isset($filters['type']) && $filters['type']!=''){
            $str .= " and type=".intval($filters['type']);
        }
        return $str;
    }
    
    function get_sub($pid){
        $this->db->select('#@comments','*','status=1 and pid='.intval($pid));
        return $this->db->getAll();
    }
    
    function delete_by_ref($type,$ref_id){
        $this->db->delete('#@comments','type='.intval($type).' and ref_id='.intval($ref_id));
        return $this->db->query();
    }

    function delete($id){
        $this->db->delete($this->table_name,$this->id_col.'='.intval($id));
        $ret = $this->db->query();
        if($ret){
            $this->db->delete($this->table_name,'pid='.intval($id));
            $this->db->query();
        }
        return $ret;
    }
}