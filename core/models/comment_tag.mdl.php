<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class comment_tag_mdl{
    function comment_tag_mdl(){
        $this->comment =& loader::model('comment');
    }
    
    function lists($data){
        $filters = array();

        if(isset($data['album_id'])){
            $filters['type'] = 1;
            $filters['ref_id'] = $data['album_id'];
        }elseif(isset($data['photo_id'])){
            $filters['type'] = 2;
            $filters['ref_id'] = $data['photo_id'];
        }
        
        $order = isset($data['order'])?$data['order']:null;

        if(array_key_exists('page',$data)){
            $page = intval($data['page']);
            $page = $page<1?1:$page;
            $pageset = intval($data['pagesize']);
            return $this->comment->get_all($page,$filters,$order,$pageset);
        }else{
            return $this->comment->get_top($data['limit'],$filters,$order);
        }
    }

    function load($data){
        $photo_id = $data['id'];
        $fields = isset($data['fields'])?$data['fields']:'*';
        return $this->album->get_info($photo_id,$fields);
    }
}