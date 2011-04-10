<?php
/**
 * $Id: photo.mdl.php 56 2010-07-09 08:13:40Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class photo_mdl extends modelfactory{
    var $table_name = '#@photos';
    
    function _filters($filters){
        $str = 'deleted=0';
        if(isset($filters['album_id'])){
            $str .= ' and album_id='.intval($filters['album_id']);
        }
        if(isset($filters['name']) && $filters['name']!=''){
            $str .= " and name like '%".$this->db->q_str($filters['name'],false)."%'";
        }
        return $str;
    }
    
    function _sort($sort){
        switch($sort){
            case 'tu_asc':
                $str = 'create_time asc';
                break;
            case 'tu_desc':
                $str = 'create_time desc';
                break;
            case 'tt_asc':
                $str = 'taken_time asc';
                break;
            case 'tt_desc':
                $str = 'taken_time desc';
                break;
            case 'h_asc':
                $str = 'hits asc';
                break;
            case 'h_desc':
                $str = 'hits desc';
                break;
            case 'c_asc':
                $str = 'comments_num asc';
                break;
            case 'c_desc':
                $str = 'comments_num desc';
                break;
            default:
                $str = $this->default_order;
        }
        return $str;
    }
    
    function trash($id){
        $info = $this->get_info($id);
        if(!$this->update($id,array('deleted'=>1))){
            return false;
        }
        $album_mdl =& loader::model('album');
        $album_mdl->update_photos_num($info['album_id'],false);
        $album_mdl->check_repare_cover($info['album_id']);
        return true;
    }
    
    function trash_batch($ids){
        if(!is_array($ids)){
            return false;
        }
        $info = $this->get_info($ids[0]);
        $this->db->update('#@photos','id in ('.implode(',',$ids).')',array('deleted'=>1));
        if(!$this->db->query()){
            return false;
        }
        $album_mdl =& loader::model('album');
        $album_mdl->update_photos_num($info['album_id'],false);
        $album_mdl->check_repare_cover($info['album_id']);
        return true;
    }
    
    function add_hit($id){
        return $this->update($id,array('hits'=>new DB_Expr('hits+1')));
    }
}