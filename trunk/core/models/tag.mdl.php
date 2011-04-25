<?php
/**
 * $Id: tag.mdl.php 56 2010-07-09 08:13:40Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010-2011 meiu.cn lingter@gmail.com
 */

class tag_mdl extends modelfactory{
    var $table_name = '#@tags';
    
    function _filters($filters){
        $str = '1';
        if(isset($filters['type'])){
            $str .= ' and type='.intval($filters['type']);
        }
        return $str;
    }
    
    function get_fontsize($v){
        if($v<=5){
            return '1.2em';
        }
        if($v<=10){
            return '2em';
        }
        if($v<=30){
            return '3em';
        }
        if($v<=100){
            return '4em';
        }
        return '5em';
    }
    
    function remove_obj_tag($tag_ids,$obj_id){
        foreach($tag_ids as $tag_id){
            $this->db->delete('#@tag_rel','tag_id='.intval($tag_id).' and rel_id='.intval($obj_id));
            $this->db->query();
        }
    }
    
    function get_by_type_name($name,$type=1){
        $this->db->select('#@tags','*','name='.$this->db->q_str($name).' and type='.intval($type));
        return $this->db->getRow();
    }
    
    function add_obj_tag($tag_id,$obj_id){
        $this->db->insert("#@tag_rel",array('tag_id'=>$tag_id,'rel_id'=>$obj_id));
        return $this->db->query();
    }
    
    function add_tags($tag_names,$obj_id,$type){
        $changed_ids = array();
        foreach($tag_names as $tag_name){
            if($tag_name != ''){
                $this->db->select('#@tags','id','name='.$this->db->q_str($tag_name).' and type='.$type);
                $tag_id = $this->db->getOne();
                if($tag_id){
                    $changed_ids[] = $tag_id;
                }else{
                    $tag_id = $this->save(array('name'=>$tag_name,'type'=>$type,'count'=>1));
                }
                $this->add_obj_tag($tag_id,$obj_id);
            }
        }
        return $changed_ids;
    }
    
    function save_tags($id,$tags,$type=1){
        $tag_arr = explode(' ',$tags);
        $tag_arr = array_unique($tag_arr);
        $this->db->select('#@tag_rel as tr left join #@tags as t on tr.tag_id=t.id','tag_id,name','t.type='.$type.' and tr.rel_id='.intval($id));
        $exist_tags = $this->db->getAll();
        
        //check exists tag and deleted
        $deleted = array();
        $exists = array();
        if($exist_tags){
            foreach($exist_tags as $et){
                $del_flag = true;
                foreach($tag_arr as  $k => $tag){
                    if($et['name'] == $tag){
                        $exists[] = $et['tag_id'];
                        $del_flag = false;
                        unset($tag_arr[$k]);
                    }
                }
                if($del_flag){
                    $deleted[] = $et['tag_id'];
                }
            }
        }
        $this->remove_obj_tag($deleted,$id);
        $changed_tag_ids = $this->add_tags($tag_arr,$id,$type);
        $this->recount($deleted);
        $this->recount($changed_tag_ids);
    }
    
    function recount($tags){
        foreach($tags as $tag_id){
            $this->db->select('#@tag_rel','count(*)','tag_id='.intval($tag_id));
            $num = $this->db->getOne();
            $this->update(intval($tag_id),array('count'=>$num));
        }
    }
}