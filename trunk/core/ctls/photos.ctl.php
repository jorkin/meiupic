<?php

class photos_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo = & loader::model('photo');
    }
    
    function index(){
        $album_id = $this->getGet('aid');
        $album_info = $this->mdl_album->get_info($album_id);
        
        $sort = $this->getGet('sort','tu_desc');
        $page = $this->getGet('page',1);
        
        $pageurl = site_link('photos','index',array('aid'=>$album_id,'page'=>'[#page#]'));
        $sort_url = site_link('photos','index',array('aid'=>$album_id,'sort'=>'[#sort#]'));

        $sort_setting = array('上传时间' => 'tu','拍摄时间' => 'tt','浏览数'=>'h','评论数'=>'c');
        $sort_list = $this->mdl_album->get_sort_list($sort_setting,$sort_url,$sort);
        $this->output->set('list_order',$this->plugin->filter('photo_sort_list',$sort_list));
        
        $photos = $this->mdl_photo->get_all($page,array('album_id'=>$album_id),$sort);
        if(is_array($photos['ls'])){
            foreach($photos['ls'] as $k=>$v){
                $photos['ls'][$k]['photo_control_icons'] = $this->plugin->filter('photo_control_icons','',$v['id']);
                $photos['ls'][$k]['thumb'] = $this->plugin->filter('photo_path',$GLOBALS['base_path'].$v['thumb'],$v['thumb']);
            }
        }
        
        $this->output->set('photos',$photos['ls']);
        $this->output->set('pageset',loader::lib('page')->fetch($photos['total'],$photos['start'],$pageurl));
        $this->output->set('total_num',$photos['count']);
        $this->output->set('album_info',$album_info);
        
        $album_menu = '<li><a href="'.site_link('photos','index',array('aid'=>$album_id)).'" class="current">'.$album_info['name'].'</a></li>';
        $this->output->set('album_menu',$this->plugin->filter('album_menu',$album_menu,$album_id));
        
        $page_title = $album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,array('aid'=>$album_id));
        
        $this->render();
    }
    
    function modify(){
        $info = $this->mdl_photo->get_info($this->getGet('id'));
        $this->output->set('info',$info);
        $this->render();
    }
    
    function update(){
        $id = $this->getGet('id');
        
        $album['name'] = trim($this->getPost('photo_name'));
        $album['desc'] = trim($this->getPost('desc'));
        $album['tags'] = trim($this->getPost('photo_tags'));
        
        if($album['name'] == ''){
            ajax_box_failed('照片名不能为空！');
        }
        
        if($this->mdl_photo->update($id,$album)){
            ajax_box_success('修改照片成功！',null,1,$_SERVER['HTTP_REFERER']);
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
            echo ajax_box('成功删除照片!',null,1,$_SERVER['HTTP_REFERER']);
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
                echo ajax_box('成功批量删除照片!',null,1,$_SERVER['HTTP_REFERER']);
            }else{
                echo ajax_box('批量删除照片失败!');
            }
        }
    }
    
    function rename(){
        $id = $this->getGet('id');
        $arr['name'] = trim($this->getPost('name'));
        if($arr['name'] == ''){
            $return = array(
                'ret'=>false
            );
            echo loader::lib('json')->encode($return);
            return;
        }
        if($this->mdl_photo->update($id,$arr)){
            $return = array(
                'ret'=>true,
                'name'=>$arr['name']
            );
            echo loader::lib('json')->encode($return);
        }else{
            $return = array(
                'ret'=>false
            );
            echo loader::lib('json')->encode($return);
        }
        return;
    }
}
