<?php

class trash_ctl extends pagecore{
    
    function _init(){
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo = & loader::model('photo');
    }
    
    function index(){
        need_login('page');
        
        $type = $this->getGet('type','1');
        $page = $this->getGet('page',1);
        
        $deleted_albums = $this->mdl_album->get_trash_count();
        $deleted_photos = $this->mdl_photo->get_trash_count();
        if($deleted_albums <= 0 && $deleted_photos<= 0){
            trash_status(2);
            $this->output->set('isempty',true);
        }else{
            if($type == 1){
                $data = $this->mdl_album->get_trash($page);
                if(is_array($data['ls'])){
                    foreach($data['ls'] as $k=>$v){
                        if($v['cover_id']){
                            $data['ls'][$k]['cover_path'] = get_album_cover($v['id'],$v['cover_ext']);
                        }
                    }
                }
            }elseif($type == 2){
                $data = $this->mdl_photo->get_trash($page);
            }
            $pageurl = site_link('trash','index',array('type'=>$type,'page'=>'[#page#]'));
        
            $pagestr = loader::lib('page')->fetch($data['total'],$data['current'],$pageurl);
            $this->output->set('isempty',false);
            $this->output->set('pagestr',$pagestr);
            $this->output->set('data',$data['ls']);
            $this->output->set('deleted_albums',$deleted_albums);
            $this->output->set('deleted_photos',$deleted_photos);
            $this->output->set('type',$type);
        }
        $page_title = '回收站 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description);
        
        $this->render();
    }
    
    function confirm_delete(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $type = $this->getGet('type');
        $this->output->set('id',$id);
        $this->output->set('type',$type);
        if($type == 1){
            $info = $this->mdl_album->get_info($id);
        }else{
            $info = $this->mdl_photo->get_info($id);
        }
        $this->output->set('name',$info['name']);
        $this->render();
    }
    
    function delete(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $type = $this->getGet('type');
        if($type == 1){
            $ret = $this->mdl_album->real_delete($id);
        }else{
            $ret = $this->mdl_photo->real_delete($id);
        }
        if($ret){
            echo ajax_box('成功删除!',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('删除失败!');
        }
    }
    
    function confirm_delete_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        $type = $this->getGet('type');
        $this->output->set('type',$type);
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的照片/相册!');
            return ;
        }
        $this->render();
    }
    
    
    function delete_batch(){
        need_login('ajax_page');
        
        $type = $this->getGet('type');
        $ids = $this->getPost('sel_id');
        if(is_array($ids)){
            foreach($ids as $id => $v){
                if($type == 1){
                    $ret = $this->mdl_album->real_delete($id);
                }else{
                    $ret = $this->mdl_photo->real_delete($id);
                }
            }
        }
        echo ajax_box('成功批量删除!',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function restore(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $type = $this->getGet('type');
        if($type == 1){
            $ret = $this->mdl_album->restore($id);
        }else{
            $ret = $this->mdl_photo->restore($id);
        }
        if($ret){
            echo ajax_box('成功还原!',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('还原失败!');
        }
    }
    
    function confirm_restore_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        $type = $this->getGet('type');
        $this->output->set('type',$type);
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要还原的照片/相册!');
            return ;
        }
        $this->render();
    }
    
    function restore_batch(){
        need_login('ajax_page');
        
        $type = $this->getGet('type');
        $ids = $this->getPost('sel_id');
        if(is_array($ids)){
            foreach($ids as $id => $v){
                if($type == 1){
                    $ret = $this->mdl_album->restore($id);
                }else{
                    $ret = $this->mdl_photo->restore($id);
                }
            }
        }
        echo ajax_box('成功批量还原!',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function confirm_emptying(){
        need_login('ajax_page');
        
        $this->render();
    }
    
    function emptying(){
        need_login('ajax_page');
        
        $albums = $this->mdl_album->get_trash();
        if($albums){
            foreach($albums as $v){
                $ret = $this->mdl_album->real_delete($v['id'],$v);
            }
        }
        $photos = $this->mdl_photo->get_trash();
        if($photos){
            foreach($photos as $v){
                $ret = $this->mdl_photo->real_delete($v['id'],$v);
            }
        }
        echo ajax_box('成功清空回收站!',null,0.5,$_SERVER['HTTP_REFERER']);
    }
}