<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010-2011 meiu.cn lingter@gmail.com
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
        if(isset($filters['tag']) && $filters['tag'] != ''){
            $tag_info = loader::model('tag')->get_by_type_name($filters['tag'],2);
            if($tag_info){
                $str .= " and id in (select rel_id from ".$this->db->stripTpre('#@tag_rel')." where tag_id=".intval($tag_info['id']).")";
            }else{
                $str .= " and 1=0";
            }
        }
        return $str;
    }
    
    function _sort($sort){
        switch($sort){
            case 'tu_asc':
                $str = 'create_time asc,id desc';
                break;
            case 'tu_desc':
                $str = 'create_time desc,id desc';
                break;
            case 'tt_asc':
                $str = 'taken_time asc,id desc';
                break;
            case 'tt_desc':
                $str = 'taken_time desc,id desc';
                break;
            case 'h_asc':
                $str = 'hits asc,id desc';
                break;
            case 'h_desc':
                $str = 'hits desc,id desc';
                break;
            case 'c_asc':
                $str = 'comments_num asc,id desc';
                break;
            case 'c_desc':
                $str = 'comments_num desc,id desc';
                break;
            case 'n_asc':
                $str = 'name asc,id desc';
                break;
            case 'n_desc':
                $str = 'name desc,id desc';
                break;
            default:
                $str = $this->default_order;
        }
        return $str;
    }
    
    function restore($id){
        if(!$this->update($id,array('deleted'=>0))){
            return false;
        }
        $info = $this->get_info($id);
        $album_mdl =& loader::model('album');
        $album_mdl->update_photos_num($info['album_id'],false);
        $album_mdl->check_repare_cover($info['album_id']);
        return true;
    }
    
    function real_delete($id,$info=null){
        if(is_null($info)){
            $info = $this->get_info($id);
        }
        $mdl_comment =& loader::model('comment');
        $mdl_comment->delete_by_ref(2,$id);
        
        @unlink(ROOTDIR.$info['thumb']);
        @unlink(ROOTDIR.$info['path']);
        return $this->delete($id);
    }
    
    function get_items($filters = array(),$sort = null){
        $where = $this->_filters($filters);
        if($sort){
            $sort = $this->_sort($sort);
        }else{
            $sort = $this->default_order;
        }
        $this->db->select($this->table_name,'id',$where,$sort);
        $data = $this->db->getCol(0);
        return $data;
    }
    
    function get_all_items($aid){
        $this->db->select('#@photos','*','album_id='.intval($aid));
        return $this->db->getAll();
    }
    
    function get_trash_count(){
        $this->db->select('#@photos','count(*)','deleted=1');
        return $this->db->getOne();
    }
    
    function get_trash($page=null){
        $this->db->select('#@photos','*','deleted=1');
        if($page){
            $data = $this->db->toPage($page,10);
        }else{
            $data = $this->db->getAll();
        }
        return $data;
    }
    
    function trash($id){
        $info = $this->get_info($id);
        if(!$this->update($id,array('deleted'=>1))){
            return false;
        }
        trash_status(1);
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
        trash_status(1);
        $album_mdl =& loader::model('album');
        $album_mdl->update_photos_num($info['album_id'],false);
        $album_mdl->check_repare_cover($info['album_id']);
        return true;
    }
    
    function move($id,$album_id){
        $photo_info = $this->get_info($id);
        $old_album  = $photo_info['album_id'];
        if($this->update($id,array('album_id'=>$album_id,'is_cover'=>0))){
            $album_mdl =& loader::model('album');
            $album_mdl->update_photos_num($old_album);
            $album_mdl->update_photos_num($album_id);
            $album_mdl->check_repare_cover($old_album);
            $album_mdl->check_repare_cover($album_id);
            
            return true;
        }else{
            return false;
        }
    }
    
    function move_batch($ids,$album_id){
        $photo_info = $this->get_info($ids[0]);
        $old_album  = $photo_info['album_id'];
        
        $this->db->update('#@photos','id in ('.implode(',',$ids).')',array('album_id'=>$album_id,'is_cover'=>0));
        if(!$this->db->query()){
            return false;
        }
        
        $album_mdl =& loader::model('album');
        $album_mdl->update_photos_num($old_album);
        $album_mdl->update_photos_num($album_id);
        $album_mdl->check_repare_cover($old_album);
        $album_mdl->check_repare_cover($album_id);
        return true;
    }
    
    function add_hit($id){
        return $this->update($id,array('hits'=>new DB_Expr('hits+1')));
    }
    
    function update_comments_num($id){
        $this->db->select('#@comments','count(id)','ref_id='.intval($id).' and type=2');
        $arr['comments_num'] = $this->db->getOne();
        return $this->update($id,$arr);
    }
}