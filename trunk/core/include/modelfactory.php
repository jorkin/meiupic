<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
class modelfactory{
    var $id_col = 'id';
    var $text_column = 'name';
    var $default_cols = '*';
    var $default_order = 'id desc';
    var $table_name = null;
    
    function modelfactory(){
        $this->db =& loader::database();
    }
    
    function _filters($filters){
        return null;
    }
    
    function get_all($page = NULL,$filters = array()){
        $where = $this->_filters($filters);
        
        $this->db->select($this->table_name,$this->default_cols,$where,$this->default_order);
        if($page){
            $data = $this->db->toPage($page,10);
        }else{
            $data = $this->db->getAll();
        }
        return $data;
    }
    
    function save($arr){
        $this->db->insert($this->table_name,$arr);
        return $this->db->query();
    }
    
    function update($id,$arr){
        $this->db->update($this->table_name,$this->id_col.'='.intval($id),$arr);
        return $this->db->query();
    }
    
    function delete($id){        
        $this->db->delete($this->table_name,$this->id_col.'='.intval($id));
        return $this->db->query();
    }
    
    function get_info($id,$fileds='*'){
        $this->db->select($this->table_name,$fileds,$this->id_col.'='.intval($id));
        return $this->db->getRow();
    }
}