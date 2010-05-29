<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class album extends modelfactory{
    
    function get_all_album($page = NULL){
        $query_arr['table'] = 'albums';
        $query_arr['orderby'] = array('id','asc');
        if($page){
            $arr = $this->txtsql_topage($query_arr,$page);
        }else{
            $arr = $this->db->select($query_arr);
        }
        return $arr;
    }
    
    function get_albums_assoc($album_id = 0){
        $query_arr['table'] = 'albums';
        $query_arr['orderby'] = array('id','asc');
        if($album_id>0){
            $query_arr['where'] = array('id <> '.intval($album_id));
        }
        $data = $this->db->select($query_arr);
        $retdata = array();
        foreach($data as $v){
            $retdata[$v['id']] = $v['name'];
        }
        return $retdata;
    }
    
    function get_one_album($id){
        $query_arr['table'] = 'albums';
        $query_arr['where'] = array('id='.intval($id));
        $query_arr['limit'] = array(1);
        $data = $this->db->select($query_arr);
        if($data){
            return $data[0];
        }else{
            return false;
        }
    }
    
    function get_cover($album_id){
        $query_arr['table'] = 'imgs';
        $query_arr['limit'] =array(1);
        $query_arr['where'] = array('album = '.intval($album_id));
        $data = $this->db->select($query_arr);
        if($data){
            return $data[0]['thumb'];
        }else{
            return 'nopic.jpg';
        }
    }
    
    function set_cover($id,$thumb){
        $query_arr['table'] = 'albums';
        $query_arr['where'] = array('id = '.intval($id));
        $query_arr['values'] = array('cover'=>$thumb);
        
        return $this->db->update($query_arr);
    }
    
    function get_album_name($id){
        $query_arr['table'] = 'albums';
        $query_arr['where'] = array('id='.intval($id));
        $query_arr['limit'] = array(1);
        $data = $this->db->select($query_arr);
        if($data){
            return $data[0]['name'];
        }else{
            return false;
        }
    }
    
    function del_album($id){
        $query_arr['table'] = 'imgs';
        $query_arr['where'] = array('album='.intval($id));
        
        $this->db->delete($query_arr);
        
        $query_arr['table'] = 'albums';
        $query_arr['where'] = array('id='.intval($id));

        return $this->db->delete($query_arr);
    }
    
    function insert_album($arr){
        
        return $this->db->insert(array(
                    'table' => 'albums',
                    'values' => $arr
                    ));
    }
    
    function update_album($id,$name){
        $query_arr['table'] = 'albums';
        $query_arr['values'] = array(
                'name' => $name
            );
        $query_arr['where'] = array('id='.intval($id));
        return $this->db->update($query_arr);
    }
    
    function remove_cover($picid){        
        $query_arr['table'] = 'imgs';
        $query_arr['where'] = array('id='.intval($picid));
        
        $data = $this->db->select($query_arr);
        $row = $data[0];
        
        $query_update['table'] = 'albums';
        $query_update['where'] = array('id='.intval($row['album']),'and',"cover = ".$row['thumb']);
        $query_update['values'] = array(
                                'cover'=>''
                                );
        return $this->db->update($query_update);
    }
}