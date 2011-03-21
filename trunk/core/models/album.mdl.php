<?php
/**
 * $Id: album.mdl.php 43 2010-07-07 01:42:43Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class album_mdl extends modelfactory{
    var $table_name = '#@albums';
    
    function _filters($filters){
        $str = 'deleted=0';
        if(isset($filters['name']) && $filters['name']!=''){
            $str .= " and name like '%".$this->db->q_str($filters['name'],false)."%'";
        }
        return $str;
    }
    
    function get_kv($album_id = 0){
        $where = 'deleted=0';
        if($album_id>0){
            $where = ' and id <> '.intval($album_id);
        }
        $this->db->select('#@albums','id,name',$where,'id desc');
        return $this->db->getAssoc();
    }
    
    function trash($id){
        return $this->update($id,array('deleted'=>1));
    }
    
    function trash_batch($ids){
        if(!is_array($ids)){
            return false;
        }
        $this->db->update('#@albums','id in ('.implode(',',$ids).')',array('deleted'=>1));
        if(!$this->db->query()){
            return false;
        }
        return true;
    }
    
    function update_photos_num($id,$up=true){
        $this->db->select('#@photos','count(id)','album_id='.intval($id).' and deleted=0');
        $arr['photos_num'] = $this->db->getOne();
        if($up){
            $arr['up_time'] = time();
        }
        return $this->update($id,$arr);
    }
    
    function check_repare_cover($id){
        $info = $this->get_info($id);
        $photo = loader::model('photo')->get_info($info['cover_id']);
        if($photo && $photo['deleted']==0 && $photo['album_id']==$id){
            return true;
        }
        $this->db->select('#@photos','id as cover_id,thumb as cover_path','album_id='.intval($id).' and deleted=0');
        $this->db->selectLimit(null,1);
        $cover_info = $this->db->getRow();
        return $this->update($id,$cover_info);
    }
}