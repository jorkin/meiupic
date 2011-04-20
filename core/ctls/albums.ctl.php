<?php

class albums_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
    }
    
    function index(){
        $search['name'] = safe_convert($this->getRequest('sname'));
        $search['tag'] = safe_convert($this->getRequest('tag'));
        $page = $this->getGet('page','1');
        
        $par['page'] = '[#page#]';
        if($search['name']){
            $par['sname'] = $search['name'];
        }
        if($search['tag']){
            $par['tag'] = $search['tag'];
        }
        $pageurl = site_link('albums','index',$par);
        
        if($search['name'] || $search['tag']){
            $this->output->set('is_search',true);
        }else{
            $this->output->set('is_search',false);
        }
        
        
        list($pageset,$page_setting_str) = get_page_setting('album');
        $this->mdl_album->set_pageset($pageset);
        
        $sort_setting = array('创建时间' => 'ct','上传时间' => 'ut','照片数' => 'p');
        list($sort,$sort_list) =  get_sort_list($sort_setting,'album','ct_desc');
        
        $albums = $this->mdl_album->get_all($page,$search,$sort);
        if(is_array($albums['ls'])){
            foreach($albums['ls'] as $k=>$v){
                $albums['ls'][$k]['album_control_icons'] = $this->plugin->filter(
                                                        'album_control_icons','',$v['id']);
                if($v['cover_id']){
                    $albums['ls'][$k]['cover_path'] = get_album_cover($v['id'],$v['cover_ext']);
                }
            }
        }
        
        $pagestr = loader::lib('page')->fetch($albums['total'],$albums['current'],$pageurl);
        
        $this->output->set('album_col_menu',$this->plugin->filter('album_col_menu',$page_setting_str.$sort_list));
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pagestr',$pagestr);
        $this->output->set('total_num',$albums['count']);
        $this->output->set('search',arr_stripslashes($search));
        $this->output->set('album_menu',$this->plugin->filter('album_menu',''));
        
        $page_title = $this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function ajaxlist(){
        $albums = $this->mdl_album->get_kv();
        if($albums){
            $ret = array('ret'=>true,'list'=>$albums);
        }else{
            $ret = array('ret'=>false,'msg'=>'没有记录！');
        }
        echo loader::lib('json')->encode($ret);
    }
    
    function create(){
        need_login('ajax_page');
        
        $this->render();
    }
    
    function save(){
        need_login('ajax');
        
        $album['name'] = safe_convert($this->getPost('album_name'));
        $album['desc'] = safe_convert($this->getPost('desc'));
        $album['priv_type'] = $this->getPost('priv_type','0');
        $album['tags'] = safe_convert($this->getPost('album_tags'));
        $album['priv_pass'] = $this->getPost('priv_pass');
        $album['priv_question'] = safe_convert($this->getPost('priv_question'));
        $album['priv_answer'] = safe_convert($this->getPost('priv_answer'));
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
        
        if($album_id = $this->mdl_album->save($album)){
            loader::model('tag')->save_tags($album_id,$album['tags'],1);
            ajax_box_success('创建相册成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('创建相册失败！');
        }
    }
    
    function modify(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $info = $this->mdl_album->get_info($id);
        $info['desc'] = safe_invert($info['desc']);
        $this->output->set('info',$info);
        $this->render();
    }
    
    function update(){
        need_login('ajax');
        
        $album_id = $this->getGet('id');
        $album['name'] = safe_convert($this->getPost('album_name'));
        $album['desc'] = safe_convert($this->getPost('desc'));
        $album['priv_type'] = $this->getPost('priv_type','0');
        $album['tags'] = safe_convert($this->getPost('album_tags'));
        $album['priv_pass'] = $this->getPost('priv_pass');
        $album['priv_question'] = safe_convert($this->getPost('priv_question'));
        $album['priv_answer'] = safe_convert($this->getPost('priv_answer'));
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
        
        if($this->mdl_album->update($album_id,$album)){
            loader::model('tag')->save_tags($album_id,$album['tags'],1);
            
            ajax_box_success('修改相册成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('修改相册失败！');
        }
    }
    //set cover
    function update_cover(){
        need_login('ajax_page');
        
        $pic_id = $this->getGet('pic_id');
        
        if($this->mdl_album->set_cover($pic_id)){
            echo ajax_box('成功设为封面',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('未能成功设为封面！');
        }
    }
    
    function confirm_delete(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $this->output->set('id',$id);
        $album_info = $this->mdl_album->get_info($id);
        $this->output->set('album_name',$album_info['name']);
        $this->render();
    }
    
    function delete(){
        need_login('ajax_page');
        
        if($this->mdl_album->trash($this->getGet('id'))){
            echo ajax_box('成功删除相册!',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('删除相册失败!');
        }
    }
    
    function confirm_delete_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的相册!');
            return ;
        }
        $this->render();
    }
    
    function delete_batch(){
        need_login('ajax_page');
        
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
    
    function modify_name_inline(){
        need_login('ajax_inline');
        
        $id = $this->getGet('id');
        $album_info = $this->mdl_album->get_info($id);
        $this->output->set('info',$album_info);
        $this->render();
    }
    
    function rename(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        $arr['name'] = safe_convert($this->getPost('name'));
        if($arr['name'] == ''){
            $return = array(
                'ret'=>false,
                'html'=>'相册名不能为空！'
            );
            echo loader::lib('json')->encode($return);
            return;
        }
        if($this->mdl_album->update($id,$arr)){
            $return = array(
                'ret'=>true,
                'html'=>$arr['name']
            );
            echo loader::lib('json')->encode($return);
        }else{
            $return = array(
                'ret'=>false,
                'html'=>'保存相册名失败！'
            );
            echo loader::lib('json')->encode($return);
        }
        return;
    }
    
    function modify_tags_inline(){
        need_login('ajax_inline');
        
        $id = $this->getGet('id');
        $album_info = $this->mdl_album->get_info($id);
        $this->output->set('info',$album_info);
        $this->render();
    }
    function save_tags(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        $tags = safe_convert($this->getPost('tags'));
        
        if( $this->mdl_album->update($id,array('tags'=>$tags)) ){
            loader::model('tag')->save_tags($id,$tags,1);
            $return = array(
                'ret'=>true,
                'html' => '标签: '.$tags
            );
        }else{
            $return = array(
                'ret'=>false,
                'html' => '编辑相册标签失败！'
            );
        }
        echo loader::lib('json')->encode($return);
        return;
    }
    function modify_desc_inline(){
        need_login('ajax_inline');
        
        $id = $this->getGet('id');
        $album_info = $this->mdl_album->get_info($id);
        $album_info['desc'] = safe_invert($album_info['desc']);
        $this->output->set('info',$album_info);
        $this->render();
    }
    
    function save_desc(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        $desc = safe_convert($this->getPost('desc'));
        if($desc == ''){
            $return = array(
                'ret'=>false,
                'html' => '相册描述不能为空！'
            );
            echo loader::lib('json')->encode($return);
            return;
        }
        if( $this->mdl_album->update($id,array('desc'=>$desc)) ){
            $return = array(
                'ret'=>true,
                'html' => $desc
            );
        }else{
            $return = array(
                'ret'=>false,
                'html' => '编辑相册描述失败！'
            );
        }
        echo loader::lib('json')->encode($return);
        return;
        
    }
    
    function modify_priv(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $album_info = $this->mdl_album->get_info($id);
        $this->output->set('info',$album_info);
        $this->render();
    }
    
    function save_priv(){
        need_login('ajax');
        
        $album['priv_type'] = $this->getPost('priv_type','0');
        $album['priv_pass'] = $this->getPost('priv_pass');
        $album['priv_question'] = safe_convert($this->getPost('priv_question'));
        $album['priv_answer'] = safe_convert($this->getPost('priv_answer'));
        
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
        
        if($this->mdl_album->update($this->getGet('id'),$album)){
            ajax_box_success('修改相册权限成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('修改相册权限失败！');
        }
    }
}