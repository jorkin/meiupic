<?php

class photos_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo = & loader::model('photo');
    }
    
    function index($arg){
        $album_id = $arg['aid'];
        $album_info = $this->mdl_album->get_info($album_id);
        
        $sort = isset($arg['sort'])?$arg['sort']:'tu_desc';
        
        $page = isset($arg['page'])?$arg['page']:1;
        $pageurl = loader::lib('uri')->mk_uri('photos','index',array('aid'=>$album_id,'page'=>'[#page#]'));
        
        $photos = $this->mdl_photo->get_all($page,array('album_id'=>$album_id),$sort);
        if(is_array($photos['ls'])){
            foreach($photos['ls'] as $k=>$v){
                $photos['ls'][$k]['photo_control_icons'] = $this->plugin->filter('photo_control_icons','',$v['id']);
                $photos['ls'][$k]['thumb'] = $this->plugin->filter('photo_path',$GLOBALS['base_path'].$v['thumb'],$v['thumb']);
            }
        }
        
        $sort_setting = array('上传时间' => 'tu','拍摄时间' => 'tt','浏览数'=>'h','评论数'=>'c');
        $sort_url = loader::lib('uri')->mk_uri('photos','index',array('aid'=>$album_id,'sort'=>'[#sort#]'));
        $sort_list = $this->mdl_album->get_sort_list($sort_setting,$sort_url,$sort);
        $this->output->set('list_order',$sort_list);
        
        $this->output->set('photos',$photos['ls']);
        $this->output->set('pageset',loader::lib('page')->fetch($photos['total'],$photos['start'],$pageurl));
        $this->output->set('total_num',$photos['count']);
        $this->output->set('album_info',$album_info);
        
        $album_menu = '<li><a href="'.loader::lib('uri')->mk_uri('photos','index',array('aid'=>$album_id)).'" class="current">'.$album_info['name'].'</a></li>';
        $this->output->set('album_menu',$this->plugin->filter('album_menu',$album_menu,$album_id));
        
        $page_title = $album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        page_init($page_title,$page_keywords,$page_description,array('tid'=>$arg['aid']));
        
        $this->render();
    }
    
    function modify($arg){
        $info = $this->mdl_photo->get_info($arg['id']);
        $this->output->set('info',$info);
        $this->render();
    }
    
    function update($arg){
        $album['name'] = trim($this->getPost('photo_name'));
        $album['desc'] = trim($this->getPost('desc'));
        $album['tags'] = trim($this->getPost('photo_tags'));
        
        if($album['name'] == ''){
            ajax_box_failed('照片名不能为空！');
        }
        
        if($this->mdl_photo->update($arg['id'],$album)){
            ajax_box_success('修改照片成功！',null,1,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('修改照片失败！');
        }
    }
    
    function confirm_delete($arg){
        $this->output->set('id',$arg['id']);
        $photo_info = $this->mdl_photo->get_info($arg['id']);
        $this->output->set('picture_name',$photo_info['name']);
        $this->render();
    }
    
    function delete($arg){
        if($this->mdl_photo->trash($arg['id'])){
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
    
    function rename($arg){
        $id = $arg['id'];
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
