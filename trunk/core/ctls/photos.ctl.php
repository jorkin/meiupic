<?php

class photos_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo = & loader::model('photo');
    }
    
    function index(){
        $this->normal();
    }
    
    function story(){
        
    }
    
    function normal(){
        $album_id = $this->getGet('aid');
        $page = $this->getGet('page',1);
        
        $pageurl = site_link('photos','index',array('aid'=>$album_id,'page'=>'[#page#]'));

        $sort_setting = array('上传时间' => 'tu','拍摄时间' => 'tt','浏览数'=>'h','评论数'=>'c');
        list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
        
        list($pageset,$page_setting_str) = get_page_setting('photo');
        $this->mdl_photo->set_pageset($pageset);
        
        $view_type = '<div class="f_right selectlist viewtype">
        <span class="label">浏览模式:</span>
        <div class="selected"></div>
        <ul class="optlist">
        <li class="current"><a href="'.site_link('photos','index',array('aid'=>$album_id)).'">平铺模式</a></li>
        <li><a href="'.site_link('photos','story',array('aid'=>$album_id)).'">故事模式</a></li>
        </ul>
        </div>';
        
        $album_info = $this->mdl_album->get_info($album_id);
        if(!$album_info){
            exit('相册不存在！');
        }
        $photos = $this->mdl_photo->get_all($page,array('album_id'=>$album_id),$sort);
        if(is_array($photos['ls'])){
            foreach($photos['ls'] as $k=>$v){
                $photos['ls'][$k]['photo_control_icons'] = $this->plugin->filter('photo_control_icons','',$v['id']);
                $photos['ls'][$k]['thumb'] = $this->plugin->filter('photo_path',$GLOBALS['base_path'].$v['thumb'],$v['thumb']);
            }
        }
        
        $pagestr = loader::lib('page')->fetch($photos['total'],$photos['current'],$pageurl);
        $album_menu = '<li><a href="'.
                        site_link('photos','index',array('aid'=>$album_id)).
                      '" class="current">'.$album_info['name'].'</a></li>';
        
        $album_info['tags_list'] = explode(',',$album_info['tags']);
        
        $mdl_comment =& loader::model('comment');
        $album_comments = $mdl_comment->get_all(1,array('ref_id'=>$album_id,'type'=>'1'));
        if($album_comments['ls']){
            foreach($album_comments['ls'] as $k=>$v){
                $album_comments['ls'][$k]['sub_comments'] = $mdl_comment->get_sub($v['id']);
            }
        }
        $this->output->set('comments_list',$album_comments['ls']);
        $this->output->set('comments_total_page',$album_comments['total']);
        $this->output->set('comments_current_page',$album_comments['current']);
        $this->output->set('ref_id',$album_id);
        $this->output->set('comments_type',1);
        
        $this->output->set('photo_col_menu',$this->plugin->filter('photo_col_menu',$view_type.$page_setting_str.$sort_list));
        $this->output->set('photos',$photos['ls']);
        $this->output->set('pagestr',$pagestr);
        $this->output->set('total_num',$photos['count']);
        $this->output->set('album_info',$album_info);
        $this->output->set('album_menu',$this->plugin->filter('album_menu',$album_menu,$album_id));
        
        $page_title = $album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,array('aid'=>$album_id));
        
        $this->render();
    }
    
    function modify(){
        $info = $this->mdl_photo->get_info($this->getGet('id'));
        $info['desc'] = safe_invert($info['desc']);
        $this->output->set('info',$info);
        $this->render();
    }
    
    function update(){
        $id = $this->getGet('id');
        
        $album['name'] = safe_convert($this->getPost('photo_name'));
        $album['desc'] = safe_convert($this->getPost('desc'));
        $album['tags'] = safe_convert($this->getPost('photo_tags'));
        
        if($album['name'] == ''){
            ajax_box_failed('照片名不能为空！');
        }
        
        if($this->mdl_photo->update($id,$album)){
            ajax_box_success('修改照片成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('修改照片失败！');
        }
    }
    
    function confirm_delete(){
        $id = $this->getGet('id');
        $this->output->set('id',$id);
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('picture_name',$photo_info['name']);
        $this->render();
    }
    
    function delete(){
        if($this->mdl_photo->trash($this->getGet('id'))){
            echo ajax_box('成功删除照片!',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('删除照片失败!');
        }
    }
    
    function confirm_delete_batch(){
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的照片!');
            return ;
        }
        $this->render();
    }
    
    function delete_batch(){
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的照片!');
        }else{
            if($this->mdl_photo->trash_batch(array_keys($ids))){
                echo ajax_box('成功批量删除照片!',null,0.5,$_SERVER['HTTP_REFERER']);
            }else{
                echo ajax_box('批量删除照片失败!');
            }
        }
    }
    
    function rename(){
        $id = $this->getGet('id');
        $arr['name'] = safe_convert($this->getPost('name'));
        if($arr['name'] == ''){
            $return = array(
                'ret'=>false,
                'msg'=>'照片名不能为空！'
            );
            echo loader::lib('json')->encode($return);
            return;
        }
        if($this->mdl_photo->update($id,$arr)){
            $return = array(
                'ret'=>true,
                'html'=> $arr['name']
            );
        }else{
            $return = array(
                'ret'=>false,
                'msg'=>'照片名保存失败！'
            );
        }
        echo loader::lib('json')->encode($return);
        return;
    }
}
