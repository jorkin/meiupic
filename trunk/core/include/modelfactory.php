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
    
    function get_page_setting($type){
        $arr = array(12,30,56);
        $current = isset($_COOKIE['_pageset_'.$type])?$_COOKIE['_pageset_'.$type]:'12';
        
        $str = '<div class="f_right pset">
            <span>显示数:</span>';
        foreach($arr as $v){
            $str .= '<a href="javascript:void(0);" onclick="page_setting(\''.$type.'\','.$v.');" '.($current==$v?'class="on"':'').'>'.$v.'</a>';
        }
        $str .= '</div>';
        return array($current,$str);
    }
    
    function set_pageset($s){
        $s = intval($s);
        $this->pageset = $s?$s:$this->pageset;
    }
    
    function get_sort_list($setting,$url,$sort){
        $str = '<div class="listorder f_right">';
        $token = rawurlencode('[#sort#]');
        foreach($setting as $k=>$v){
            if($v.'_asc' == $sort){
                $str .= '<a href="'.str_replace($token,$v.'_desc',$url).'" class="list_asc_on"><span>'.$k.'</span></a>';
            }elseif($v.'_desc' == $sort){
                $str .= '<a href="'.str_replace($token,$v.'_asc',$url).'" class="list_desc_on"><span>'.$k.'</span></a>';
            }else{
                $str .= '<a href="'.str_replace($token,$v.'_asc',$url).'" class="list_asc"><span>'.$k.'</span></a>';
            }
        }
        return $str.'</div>';
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