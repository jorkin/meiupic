<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class picture extends modelfactory{
    
    function get_all_pic($page = NULL,$album=0){
        $query_arr['table'] = 'imgs';
        $query_arr['orderby'] = array('id','asc');
        if($album > 0){
            $query_arr['where'] = array('album = '.intval($album));
        }
        if($page){
            $arr = $this->txtsql_topage($query_arr,$page);
        }else{
            $arr = $this->db->select($query_arr);
        }
        return $arr;
    }
    
    function get_tmp_pic(){
        $query_arr['table'] = 'imgs';
        $query_arr['orderby'] = array('id','asc');
        $query_arr['where'] = array('status = 0');

        $arr = $this->db->select($query_arr);
        
        return $arr;
    }
    
    function get_one_pic($id){
        $query_arr['table'] = 'imgs';
        $query_arr['limit'] = array(1);
        $query_arr['where'] = array('id='.intval($id));
        
        $arr = $this->db->select($query_arr);
        if($arr){
            return $arr[0];
        }else{
            return false;
        }
    }
    
    function insert_pic($arr){
        return $this->db->insert(array(
                    'table' => 'imgs',
                    'values' => $arr
                    ));
    }
    
    function update_pic($id,$name,$album=0){
        $query_arr['table'] = 'imgs';
        $query_arr['values'] = array(
                'name' => $name,
                'status' => 1
            );
        if($album>0){
            $query_arr['values']['album'] = intval($album);
        }
        $query_arr['where'] = array('id='.intval($id));
        return $this->db->update($query_arr);
    }
    
    function del_pic($id){
        $query_arr['table'] = 'imgs';
        $query_arr['where'] = array('id='.intval($id));
        
        return $this->db->delete($query_arr);
    }
}