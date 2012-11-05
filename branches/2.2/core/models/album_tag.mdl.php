<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class album_tag_mdl{
    function album_tag_mdl(){
        $this->album =& loader::model('album');
    }
    
    function lists($data){
        $filters = array();
        if(isset($data['cate_id'])){
            $filters['cate_id'] = $data['cate_id'];
        }
        if(isset($data['type'])){
            $filters['priv_type'] = $data['type'];
        }
        
        $order = isset($data['order'])?$data['order']:null;

        if(array_key_exists('page',$data)){
            $page = intval($data['page']);
            $page = $page<1?1:$page;
            $pageset = intval($data['pagesize']);
            return $this->album->get_all($page,$filters,$order,$pageset);
        }else{
            return $this->album->get_top($data['limit'],$filters,$order);
        }
    }

    function load($data){
        $album_id = $data['id'];
        $fields = isset($data['fields'])?$data['fields']:'*';
        return $this->album->get_info($album_id,$fields);
    }

    function get_next($data){
        $album_id = $data['id'];
        $fields = isset($data['fields'])?$data['fields']:'*';
        return $this->album->get_next($album_id,$fields);
    }

    function get_prev($data){
        $album_id = $data['id'];
        $fields = isset($data['fields'])?$data['fields']:'*';
        return $this->album->get_prev($album_id,$fields);
    }
}