<?php

class albums_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
    }
    
    function index($arg){
        $search['name'] = $this->getRequest('search_name');
        $sort = isset($arg['sort'])?$arg['sort']:'t_desc';
        
        $page = isset($arg['page'])?$arg['page']:1;
        $pageurl = loader::lib('uri')->mk_uri('albums','index',array('sort'=>$sort,'page'=>'[#page#]'));
        
        $albums = $this->mdl_album->get_all($page,$search,$sort);
        if(is_array($albums['ls'])){
            foreach($albums['ls'] as $k=>$v){
                $albums['ls'][$k]['album_control_icons'] = $this->plugin->filter('album_control_icons','',$v['id']);
                if($v['cover_id']){
                    $albums['ls'][$k]['cover_path'] = $this->plugin->filter('picture_path',$GLOBALS['base_path'].$v['cover_path'],$v['cover_path']);
                }
            }
        }

        $sort_setting = array('时间排序' => 't','照片数' => 'p');

        $sort_url = loader::lib('uri')->mk_uri('albums','index',array('sort'=>'[#sort#]'));
        $sort_list = $this->mdl_album->get_sort_list($sort_setting,$sort_url,$sort);
        
        $this->output->set('list_order',$sort_list);
        
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',loader::lib('page')->fetch($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        
        $this->output->set('search',$search);
        $this->output->set('album_menu',$this->plugin->filter('album_menu',''));
        
        $page_title = $this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function ajaxlist($arg){
        $albums = $this->mdl_album->get_kv();
        if($albums){
            $ret = array('ret'=>true,'list'=>$albums);
        }else{
            $ret = array('ret'=>false,'msg'=>'没有记录！');
        }
        echo loader::lib('json')->encode($ret);
    }
    
    function create($arg){
        $this->render();
    }
    
    function save($arg){
        $album['name'] = trim($this->getPost('album_name'));
        $album['desc'] = trim($this->getPost('desc'));
        $album['priv_type'] = $this->getPost('priv_type','0');
        $album['tags'] = trim($this->getPost('album_tags'));
        $album['priv_pass'] = $this->getPost('priv_pass');
        $album['priv_question'] = trim($this->getPost('priv_question'));
        $album['priv_answer'] = trim($this->getPost('priv_answer'));
        $album['create_time'] = $album['up_time'] = time();
        
        if($album['name'] == ''){
            ajax_box_failed('相册名不能为空！');
        }
        if($album['priv_type'] == '1'){
            if($album['priv_pass']==''){
                ajax_box_failed('密码不能为空！');
            }
        }
        if($album['priv_type'] == '2'){
            if($album['priv_question'] == ''){
                ajax_box_failed('问题不能为空！');
            }
            if($album['priv_answer'] == ''){
                ajax_box_failed('答案不能为空！');
            }
        }
        
        if($this->mdl_album->save($album)){
            ajax_box_success('创建相册成功！',null,1,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('创建相册失败！');
        }
    }
    
    function modify($arg){
        $info = $this->mdl_album->get_info($arg['id']);
        $this->output->set('info',$info);
        $this->render();
    }
    
    function update($arg){
        $album['name'] = trim($this->getPost('album_name'));
        $album['desc'] = trim($this->getPost('desc'));
        $album['priv_type'] = $this->getPost('priv_type','0');
        $album['tags'] = trim($this->getPost('album_tags'));
        $album['priv_pass'] = $this->getPost('priv_pass');
        $album['priv_question'] = trim($this->getPost('priv_question'));
        $album['priv_answer'] = trim($this->getPost('priv_answer'));
        //$album['create_time'] = $album['up_time'] = time();
        
        if($album['name'] == ''){
            ajax_box_failed('相册名不能为空！');
        }
        if($album['priv_type'] == '1'){
            if($album['priv_pass']==''){
                ajax_box_failed('密码不能为空！');
            }
        }
        if($album['priv_type'] == '2'){
            if($album['priv_question'] == ''){
                ajax_box_failed('问题不能为空！');
            }
            if($album['priv_answer'] == ''){
                ajax_box_failed('答案不能为空！');
            }
        }
        
        if($this->mdl_album->update($arg['id'],$album)){
            ajax_box_success('修改相册成功！',null,1,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('修改相册失败！');
        }
    }
    //set cover
    function update_cover($arg){        
        $pic_id = $arg['pic_id'];
        $pic_info = loader::model('photo')->get_info($pic_id);
        $arr['cover_path'] = $pic_info['thumb'];
        $arr['cover_id'] = $pic_id;
        if($this->mdl_album->update($pic_info['album_id'],$arr)){
            echo ajax_box('成功设为封面',null,1);
        }else{
            echo ajax_box('未能成功设为封面！');
        }
    }
    
    function confirm_delete($arg){
        $this->output->set('id',$arg['id']);
        $album_info = $this->mdl_album->get_info($arg['id']);
        $this->output->set('album_name',$album_info['name']);
        $this->render();
    }
    
    function delete($arg){
        if($this->mdl_album->trash($arg['id'])){
            echo ajax_box('成功删除相册!',null,1,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('删除相册失败!');
        }
    }
    
    function confirm_delete_batch(){
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的相册!');
            return ;
        }
        $this->render();
    }
    
    function delete_batch(){
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的相册!');
        }else{
            if($this->mdl_album->trash_batch(array_keys($ids))){
                echo ajax_box('成功批量删除相册!',null,1,$_SERVER['HTTP_REFERER']);
            }else{
                echo ajax_box('批量删除相册失败!');
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
        if($this->mdl_album->update($id,$arr)){
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