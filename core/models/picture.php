<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class picture extends modelfactory{
    
    function get_all_pic($page = NULL,$album=0,$sort='id_desc',$limit=0,$filter_private=false){
        $where = '';
        if($album > 0){
            $where .= 'album='.intval($album);
        }
        if($filter_private){
            $where .= ' private=0';
        }
        if($sort == 'hot'){
            $db_sort = 'hits desc,id desc';
        }elseif($sort == 'id_asc'){
            $db_sort = 'id asc';
        }else{
            $db_sort = 'id desc';
        }
        $this->db->select('#imgs',"*",$where,$db_sort);
        if($limit > 0){
            $this->db->selectLimit(NULL,$limit);
        }
        if($page){
            $pics = $this->db->toPage($page,PAGE_SET);
        }else{
            $pics = $this->db->getAll();
        }
        return $pics;
    }
    
    function get_tmp_pic(){
        $this->db->select('#imgs','*','status=0','id asc');
        return $this->db->getAll();
    }
    
    function get_one_pic($id){
        $this->db->select('#imgs','*','id='.intval($id));
        return $this->db->getRow();
    }
    
    function get_one_pic_by_key($key){
        $this->db->select('#imgs','*','pickey="'.$key.'"');
        return $this->db->getRow();
    }
    
    function get_pre_pic($id,$album=0){
        $where = '';
        if($album>0){
            $where = ' and album='.intval($album);
        }
        $this->db->select('#imgs','*','id>'.intval($id).$where,'id asc limit 1');
        return $this->db->getRow();
    }
    function get_next_pic($id,$album=0){
        $where = '';
        if($album>0){
            $where = ' and album='.intval($album);
        }
        $this->db->select('#imgs','*','id<'.intval($id).$where,'id desc limit 1');
        return $this->db->getRow();
    }
    
    function insert_pic($arr){
        $this->db->insert('#imgs',$arr);
        return $this->db->query();
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
        
        $this->db->update('#imgs','id='.intval($id),$arr);
        return $this->db->query();
    }
    
    function del_pic($id){
        $this->db->delete('#imgs','id='.intval($id));
        return $this->db->query();
    }
}