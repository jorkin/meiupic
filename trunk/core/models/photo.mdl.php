<?php
/**
 * $Id: photo.php 56 2010-07-09 08:13:40Z lingter $
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
        $album_mdl->update_photos_num($info['album_id']);
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
        $album_mdl->update_photos_num($info['album_id']);
        $album_mdl->check_repare_cover($info['album_id']);
        return true;
    }
    
    
    
    
    function get_tmp_pic(){
        $this->db->select('#@photos','*','status=0','id asc');
        return $this->db->getAll();
    }
    
    function get_one_pic($id){
        $this->db->select('#@photos','*','id='.intval($id));
        return $this->db->getRow();
    }
    
    function get_one_pic_by_key($key){
        $this->db->select('#@photos','*','pickey="'.$key.'"');
        return $this->db->getRow();
    }
    
    function get_pre_pic($id,$album=0){
        $where = '';
        if($album>0){
            $where = ' and album='.intval($album);
        }
        $this->db->select('#@photos','*','id>'.intval($id).$where,'id asc limit 1');
        return $this->db->getRow();
    }
    function get_next_pic($id,$album=0){
        $where = '';
        if($album>0){
            $where = ' and album='.intval($album);
        }
        $this->db->select('#@photos','*','id<'.intval($id).$where,'id desc limit 1');
        return $this->db->getRow();
    }
    
    
    
    function update_pic($id,$name,$album=0){
        $arr['name'] = $name;
        $arr['status'] = 1;
        if($album>0){
            $album_model = & load_model('album');
            $album_arr = $album_model->get_one_album(intval($album));
            if($album_arr){
                $arr['private'] = $album_arr['private'];
                $arr['album'] = intval($album);
            }
        }
        
        $this->db->update('#@photos','id='.intval($id),$arr);
        return $this->db->query();
    }
    
    function del_pic($id){
        $this->db->delete('#@photos','id='.intval($id));
        return $this->db->query();
    }
    
    function addHit($id){
        $this->db->update('#@photos','id='.intval($id),array('hits'=>new DB_Expr('hits+1')));
        return $this->db->query();
    }
}