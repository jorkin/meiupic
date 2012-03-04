<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class photo_tag_mdl{
    function photo_tag_mdl(){
        $this->photo =& loader::model('photo');
    }
    
    function lists($data){
        $filters = array();
        if(isset($data['album_id'])){
            $filters['album_id'] = intval($data['album_id']);
        }
        if(isset($data['is_open'])){
            $filters['is_open'] = intval($data['is_open']);
        }
        
        $order = isset($data['order'])?$data['order']:null;

        if(array_key_exists('page',$data)){
            $page = intval($data['page']);
            $page = $page<1?1:$page;
            $pageset = intval($data['pagesize']);
            return $this->photo->get_all($page,$filters,$order,$pageset);
        }else{
            return $this->photo->get_top($data['limit'],$filters,$order);
        }
    }
    
    function load($data){
        $photo_id = $data['id'];
        $fields = isset($data['fields'])?$data['fields']:'*';
        return $this->photo->get_info($photo_id,$fields);
    }
}