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
    var $pageset = 10;
    
    function modelfactory(){
        $this->db =& loader::database();
    }
    
    function _filters($filters){
        return null;
    }
    
    function _sort($sort){
        return $this->default_order;
    }
    
    function set_pageset($s){
        $s = intval($s);
        $this->pageset = $s?$s:$this->pageset;
    }
    
    
    
    function get_all($page = NULL,$filters = array(),$sort = null){
        $where = $this->_filters($filters);
        if($sort){
            $sort = $this->_sort($sort);
        }else{
            $sort = $this->default_order;
        }
        $this->db->select($this->table_name,$this->default_cols,$where,$sort);
        if($page){
            $data = $this->db->toPage($page,$this->pageset);
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