<?php

class photos_ctl extends pagecore{
    
    function _init(){
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo = & loader::model('photo');
    }
    
    function _sort_setting(){
        return array(lang('upload_time') => 'tu',lang('taken_time') => 'tt',lang('hits')=>'h',lang('comments_nums')=>'c',lang('photo_name')=>'n');
    }
    
    function index(){
        $this->normal();
    }
    
    function normal(){
        $search['name'] = $this->getRequest('sname');
        $search['album_id'] = $album_id = $this->getGet('aid');
        
        $album_info = $this->mdl_album->get_info($album_id);
        if(!$album_info){
            showError(lang('album_not_exists'));
        }
        if(!$this->mdl_album->check_album_priv($album_id,$album_info)){
            $this->_priv_page($album_id,$album_info);
            exit;
        }
        $par['page'] = '[#page#]';
        $par['aid'] = $album_id;
        if($search['name']){
            $par['sname'] = $search['name'];
            $this->output->set('is_search',true);
        }else{
            $this->output->set('is_search',false);
        }
        
        $pageurl = site_link('photos','index',$par);

        $sort_setting = $this->_sort_setting();
        list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
        list($pageset,$page_setting_str) = get_page_setting('photo');
        
        $page = $this->getGet('page',1);
        $this->mdl_photo->set_pageset($pageset);
        $photos = $this->mdl_photo->get_all($page,$search,$sort);
        
        if(is_array($photos['ls'])){
            foreach($photos['ls'] as $k=>$v){
                $photos['ls'][$k]['photo_control_icons'] = $this->plugin->filter('photo_control_icons','',$v['album_id'],$v['id']);
                $img_thumb = '<a href="'.site_link('photos','view',array('id'=>$v['id'])).'">
                <img src="'.img_path($v['thumb']).'" alt="'.$v['name'].'" /></a>';
                $photos['ls'][$k]['img_thumb'] = $this->plugin->filter('photo_list_thumb',$img_thumb,$v['id'],$v['thumb'],$v['path']);
                
            }
        }
        
        $view_type = '<div class="f_right selectlist viewtype">
        <span class="label">'.lang('view_type').':</span>
        <div class="selected"></div>
        <ul class="optlist">
        <li class="current"><a href="'.site_link('photos','index',array('aid'=>$album_id)).'">'.lang('flat_mode').'</a></li>
        <li><a href="'.site_link('photos','slide',array('aid'=>$album_id)).'">'.lang('slide_mode').'</a></li>
        </ul>
        </div>';
        $album_nav = '<li><a href="'.
                        site_link('photos','index',array('aid'=>$album_id)).
                      '" class="current">'.$album_info['name'].'</a></li>';
        
        //load comments
        if($this->setting->get_conf('system.enable_comment') && $album_info['enable_comment']==1){
            $cpage = $this->getGet('cpage',1);
            $mdl_comment =& loader::model('comment');
            $album_comments = $mdl_comment->get_all($cpage,array('status'=>1,'pid'=>0,'ref_id'=>$album_id,'type'=>'1'));
            if($album_comments['ls']){
                foreach($album_comments['ls'] as $k=>$v){
                    $sub_comments = $mdl_comment->get_sub($v['id']);
                    if($sub_comments){
                        foreach($sub_comments as $kk=>$vv){
                            $sub_comments[$kk]['content'] = $this->plugin->filter('comment_content',$vv['content'],$vv['id']);
                        }
                    }
                    $album_comments['ls'][$k]['content'] = $this->plugin->filter('comment_content',$v['content'],$v['id']);
                    $album_comments['ls'][$k]['sub_comments'] = $sub_comments;
                }
            }
            $this->output->set('comments_list',$album_comments['ls']);
            $this->output->set('comments_total_page',$album_comments['total']);
            $this->output->set('comments_current_page',$album_comments['current']);
            $this->output->set('ref_id',$album_id);
            $this->output->set('comments_type',1);
            
            $this->output->set('enable_comment',true);
        }else{
            $this->output->set('enable_comment',false);
        }
        
        $album_info['tags_list'] = parse_tag($album_info['tags']);
        $album_info['desc'] = $this->plugin->filter('album_desc',$album_info['desc'],$album_id);
        
        $this->output->set('photo_col_menu',$this->plugin->filter('photo_col_menu',$view_type.$page_setting_str.$sort_list,$album_id));
        $this->output->set('photo_multi_opt',$this->plugin->filter('photo_multi_opt','',$album_id));
        $this->output->set('photo_sidebar',$this->plugin->filter('photo_sidebar','',$album_id));
        
        $this->output->set('photos',$photos['ls']);
        $this->output->set('search',$search);
        $page_obj =& loader::lib('page');
        $this->output->set('pagestr',$page_obj->fetch($photos['total'],$photos['current'],$pageurl));
        $this->output->set('total_num',$photos['count']);
        $this->output->set('album_info',$album_info);
        $this->output->set('album_nav',$album_nav);
        $this->output->set('show_takentime',($sort=='tt_desc'||$sort=='tt_asc')?true:false);
        
        
        $page_title = $album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = ($album_info['tags']?implode(',',$album_info['tags_list']).',':'').$this->setting->get_conf('site.keywords');
        $page_description = $album_info['desc']?strip_tags($album_info['desc']):$this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,$album_id);
        
        $this->render();
    }
    
    function check_priv(){
        $album_id = $this->getPost('album_id');
        $album_info = $this->mdl_album->get_info($album_id);
        $enter_album = $this->getPost('enter_album');
        
        $key = 'Mpic_album_priv_'.$album_id;
        $go_url = $enter_album?site_link('photos','index',array('aid'=>$album_id)):$_SERVER['HTTP_REFERER'];
        if($album_info['priv_type'] == 1){
            $priv_pass = $this->getPost('priv_pass');
            if($album_info['priv_pass'] != $priv_pass){
                form_ajax_failed('text',lang('album_pass_error'));
            }
            setCookie($key,md5($priv_pass),0,'/');
            form_ajax_success('box',lang('validate_success'),null,0.5,$go_url);
        }elseif($album_info['priv_type'] == 2){
            $priv_answer = $this->getPost('priv_answer');
            if($album_info['priv_answer'] != $priv_answer){
                form_ajax_failed('text',lang('album_answer_error'));
            }
            setCookie($key,md5($album_info['priv_question'].$priv_answer),0,'/');
            form_ajax_success('box',lang('validate_success'),null,0.5,$go_url);
        }
        form_ajax_failed('text',lang('album_priv_error'));
    }
    
    function auth_priv(){
        $aid = $this->getGet('aid');
        $this->_priv_page($aid);
    }
    
    function _priv_page($id,$album_info=null){
        if(is_null($album_info)){
            $album_info = $this->mdl_album->get_info($id);
        }
        $this->output->set('album_info',$album_info);
        
        $ajax = $this->getGet('ajax');
        if($ajax == 'true'){
            $this->output->set('ajax',true);
            if($this->mdl_album->check_album_priv($id,$album_info)){
                ajax_box(lang('has_validate'),null,0.5,site_link('photos','index',array('aid'=>$id)));
            }
        }else{
            $this->output->set('ajax',false);
            $page_title = lang('title_need_validate').' - '.lang('system_notice').' - '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');
            $this->page_init($page_title,$page_keywords,$page_description,$id);
        }
        
        loader::view('photos/priv_page');
    }
    
    function slide(){
        $album_id = $this->getGet('aid');
        $album_info = $this->mdl_album->get_info($album_id);
        $refer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:site_link('photos','index',array('aid'=>$album_id));
        
        $page_title = $album_info['name'].' - '.lang('slideshow').' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,$album_id);
        
        $this->output->set('refer',$refer);
        $this->output->set('album_id',$album_id);
        $this->render();
    }
    
    function gallery(){
        $album_id = $this->getGet('aid');
        $info = $this->mdl_album->get_info($album_id);
        
        if(!$this->mdl_album->check_album_priv($album_id,$info)){
            exit(lang('no_access'));
        }
        
        $title = $info['name'];

        
        echo '<?xml version="1.0" encoding="UTF-8"?>
<simpleviewergallery 
 title="'.$title.'"
 textColor="FFFFFF"
 frameColor="FFFFFF"
 thumbPosition="BOTTOM"
 galleryStyle="MODERN"
 thumbColumns="10"
 thumbRows="1"
 showOpenButton="FALSE"
 showFullscreenButton="TRUE"
 frameWidth="6"
 maxImageWidth="1600"
 maxImageHeight="1200"
 imagePath="data/"
 thumbPath="data/"
 useFlickr="false"
 flickrUserName=""
 flickrTags=""
 languageCode="AUTO"
 languageList="">'."\n";
        
        $sort_setting = $this->_sort_setting();
        list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
        
        $pictures = $this->mdl_photo->get_all(NULL,array('album_id'=>$album_id),$sort);
        if(is_array($pictures)){
            foreach($pictures as $v){
                echo '    <image imageURL="'.img_path($v['path']).'" thumbURL="'.img_path($v['thumb']).'" linkURL="'.img_path($v['path']).'" linkTarget="">
        <caption><![CDATA['.$v['name'].']]></caption>	
    </image>'."\n";
            }
        }

        echo '</simpleviewergallery>';
    }
    
    function search(){
        $searchtype = $this->getPost('searchtype');
        $search['name'] = safe_convert($this->getRequest('sname')); 

        if($searchtype && $searchtype == 'album'){
            $album_id = $this->getPost('album_id');
            if($search['name']){
                $url = site_link('photos','index',array('aid'=>$album_id,'sname'=>$search['name']));
            }else{
                $url = site_link('photos','index',array('aid'=>$album_id));
            }
            header('Location: '.$url);
            exit;
        }else{
            $page = $this->getGet('page',1);
            $search['tag'] = safe_convert($this->getRequest('tag'));
            $par['page'] = '[#page#]';
            if($search['name']){
                $par['sname'] = $search['name'];
            }
            if($search['tag']){
                $par['tag'] = $search['tag'];
            }
            $pageurl = site_link('photos','search',$par);

            $sort_setting = $this->_sort_setting();
            list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
            list($pageset,$page_setting_str) = get_page_setting('photo');
            $this->mdl_photo->set_pageset($pageset);

            $photos = $this->mdl_photo->get_all($page,$search,$sort);
            if(is_array($photos['ls'])){
                foreach($photos['ls'] as $k=>$v){
                    $photos['ls'][$k]['photo_priv'] = $this->mdl_album->check_album_priv($v['album_id']);
                    $img_thumb = '<a href="'.site_link('photos','view',array('id'=>$v['id'])).'">
                    <img src="'.img_path($v['thumb']).'" /></a>';
                    $photos['ls'][$k]['img_thumb'] = $this->plugin->filter('photo_list_thumb',$img_thumb,$v['id'],$v['thumb'],$v['path']);
                }
            }
            $page_obj =& loader::lib('page');
            $pagestr = $page_obj->fetch($photos['total'],$photos['current'],$pageurl);

            $this->output->set('photo_col_menu',$this->plugin->filter('photo_col_menu',$page_setting_str.$sort_list,null));
            $this->output->set('photos',$photos['ls']);
            $this->output->set('search',$search);
            $this->output->set('pagestr',$pagestr);
            $this->output->set('total_num',$photos['count']);
            unset($par['page']);
            $this->output->set('album_nav','<li><a href="'.
                            site_link('photos','search',$par).
                          '" class="current">'.lang('search_result').'</a></li>');

            $page_title = (isset($par['tag'])?$par['tag']:$search['name']).' - '.lang('search_result').' - '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');
            $this->page_init($page_title,$page_keywords,$page_description);

            $this->render();
        }
    }
    
    function modify(){
        need_login('ajax_page');
        
        $info = $this->mdl_photo->get_info($this->getGet('id'));
        $info['desc'] = safe_invert($info['desc']);
        $this->output->set('info',$info);
        $this->render();
    }
    
    function update(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        
        $album['name'] = safe_convert($this->getPost('photo_name'));
        $album['desc'] = safe_convert($this->getPost('desc'));
        $album['tags'] = safe_convert($this->getPost('photo_tags'));
        
        if($album['name'] == ''){
            form_ajax_failed('text',lang('photo_name_empty'));
        }
        
        if($this->mdl_photo->update($id,$album)){
            $tag_mdl =& loader::model('tag');
            $tag_mdl->save_tags($id,$album['tags'],2);
            
            $this->plugin->trigger('modified_photo',$id);
            form_ajax_success('box',lang('modify_photo_success'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            form_ajax_failed('text',lang('modify_photo_failed'));
        }
    }
    
    function move(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $this->output->set('id',$id);
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('albums_list',$this->mdl_album->get_kv($photo_info['album_id']));
        $this->output->set('info',$photo_info);
        $this->render();
    }
    
    function do_move(){
        need_login('ajax');
        $album_id = $this->getPost('album_id');
        $id = $this->getGet('id');
        if(!$album_id || $album_id==''){
            form_ajax_failed('box',lang('havnt_sel_album'));
        }
        if($this->mdl_photo->move($id,$album_id)){
            
            $this->plugin->trigger('moved_photo',$id);
            form_ajax_success('box',lang('move_photo_success'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            form_ajax_failed('box',lang('move_photo_failed'));
        }
    }
    
    function move_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        
        if(!$ids || count($ids) == 0){
            ajax_box(lang('pls_sel_photo_want_to_move'));
        }
        $ids = array_keys($ids);
        $this->output->set('sel_ids',implode(',',$ids));
        $photo_info = $this->mdl_photo->get_info($ids[0]);
        $this->output->set('albums_list',$this->mdl_album->get_kv($photo_info['album_id']));
        $this->output->set('info',$photo_info);
        $this->render();
    }
    
    function do_move_batch(){
        need_login('ajax');
        
        $ids = $this->getPost('ids');
        $album_id = $this->getPost('album_id');
        if(!$ids){
            form_ajax_failed('box',lang('pls_sel_photo_want_to_move'));
        }
        if(!$album_id || $album_id==''){
            form_ajax_failed('box',lang('havnt_sel_album'));
        }
        if($this->mdl_photo->move_batch(explode(',',$ids),$album_id)){
            $this->plugin->trigger('moved_many_photos',explode(',',$ids));
            form_ajax_success('box',lang('batch_move_photo_success'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            form_ajax_failed('box',lang('batch_move_photo_failed'));
        }
    }
    
    function rotate(){
        need_login('ajax_page');
        $id = $this->getGet('id');
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('info',$photo_info);
        $this->render();
    }
    
    function do_rotate(){
        need_login('ajax_page');

        $rot_type = $this->getPost('rot','0');
        $id = $this->getGet('id');
        if($rot_type == '1'){
            $degree = '270';
        }elseif($rot_type == '2'){
            $degree = '180';
        }elseif($rot_type == '3'){
            $degree = '90';
        }else{
            form_ajax_success('box',lang('do_nothing'),null,0.5,$_SERVER['HTTP_REFERER']);
        }

        if($this->mdl_photo->rotate_photo($id,$degree)){
            form_ajax_success('box',lang('rotate_image_success').'<script>setTimeout(function (){
                window.location.reload();
                },500);</script>');
        }else{
            form_ajax_failed('box',lang('rotate_image_failed'));
        }
    }

    function reupload(){
        need_login('ajax_page');

        $id = $this->getGet('id');
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('info',$photo_info);
        $this->render();
    }

    function confirm_delete(){
        need_login('ajax_page');
        
        $id = $this->getGet('id');
        $this->output->set('id',$id);
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('picture_name',$photo_info['name']);
        $this->render();
    }
    
    function delete(){
        need_login('ajax_page');
        $id = $this->getGet('id');
        if($this->mdl_photo->trash($id)){
            $this->plugin->trigger('trashed_photo',$id);
            
            ajax_box(lang('delete_photo_success'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box(lang('delete_photo_failed'));
        }
    }
    
    function confirm_delete_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            ajax_box(lang('pls_sel_photo_want_to_delete'));
        }
        $this->render();
    }
    
    function delete_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            ajax_box(lang('pls_sel_photo_want_to_delete'));
        }else{
            if($this->mdl_photo->trash_batch(array_keys($ids))){
                $this->plugin->trigger('trashed_many_photos',array_keys($ids));
                
                ajax_box(lang('batch_delete_photo_success'),null,0.5,$_SERVER['HTTP_REFERER']);
            }else{
                ajax_box(lang('batch_delete_photo_failed'));
            }
        }
    }
    
    function rename(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        $arr['name'] = safe_convert($this->getPost('name'));
        if($arr['name'] == ''){
            form_ajax_failed('text',lang('photo_name_empty'));
        }
        if($this->mdl_photo->update($id,$arr)){
            $this->plugin->trigger('renamed_photo',$id);
            
            form_ajax_success('text',$arr['name']);
        }else{
            form_ajax_failed('text',lang('save_photo_name_failed'));
        }
        return;
    }
    
    
    function view(){
        $id = $this->getGet('id');
        $info = $this->mdl_photo->get_info($id);
        
        if(!$info){
            showError(lang('photo_not_exists'));
        }
        
        if($info['exif']){
            $info['exif'] = unserialize($info['exif']);
        }
        
        $info['tags_list'] = parse_tag($info['tags']);
        
        $album_info = $this->mdl_album->get_info($info['album_id']);
        if(!$this->mdl_album->check_album_priv($album_info['id'],$album_info)){
            $this->_priv_page($album_info['id'],$album_info);
            exit;
        }
        
        $album_nav = '<li><a href="'.site_link('photos','index',array('aid'=>$info['album_id'])).'" class="current">'.$album_info['name'].'</a></li>';
        $photo_col_ctl = '';
        
        $this->plugin->trigger('viewed_photo',$id);
        
        $this->mdl_photo->add_hit($id);
        
        $sort_setting = $this->_sort_setting();
        list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
        
        $nav['items'] = $this->mdl_photo->get_items(array('album_id'=>$info['album_id']),$sort);
        $nav['rank_of'] = array_flip($nav['items']);
        $nav['first_rank']   = 0;
        $nav['last_rank']    = count($nav['items']) - 1;
        $nav['current_item'] = $id;
        $nav['current_rank'] = $nav['rank_of'][$id];
        if($nav['current_rank'] != $nav['first_rank']){
          $nav['previous_item'] = $nav['items'][ $nav['current_rank'] - 1 ];
          $nav['first_item'] = $nav['items'][ $nav['first_rank'] ];
        }
        if($nav['current_rank'] != $nav['last_rank']){
          $nav['next_item'] = $nav['items'][ $nav['current_rank'] + 1 ];
          $nav['last_item'] = $nav['items'][ $nav['last_rank'] ];
        }
        $ids = array();
        if (isset($nav['previous_item'])) {
          array_push($ids, $nav['previous_item']);
          array_push($ids, $nav['first_item']);
        }
        if (isset($nav['next_item'])) {
          array_push($ids, $nav['next_item']);
          array_push($ids, $nav['last_item']);
        }
        $p_result = $this->mdl_photo->get_info($ids);
        $picture = array(
            'previous' =>false,
            'next' => false,
            'first' => false,
            'last' => false
        );
        if($p_result){
            foreach($p_result as $v){
                  if (isset($nav['previous_item']) and $v['id'] == $nav['previous_item']){
                    $i = 'previous';
                  }else if (isset($nav['next_item']) and $v['id'] == $nav['next_item']){
                    $i = 'next';
                  }else if (isset($nav['first_item']) and $v['id'] == $nav['first_item']){
                    $i = 'first';
                  }else if (isset($nav['last_item']) and $v['id'] == $nav['last_item']){
                    $i = 'last';
                  }
                  $picture[$i] = $v;
            }
        }
        if($this->setting->get_conf('system.enable_comment') && $album_info['enable_comment']==1){
            $cpage = $this->getGet('cpage',1);
            $mdl_comment =& loader::model('comment');
            $comments = $mdl_comment->get_all($cpage,array('status'=>1,'pid'=>0,'ref_id'=>$id,'type'=>2));
            if($comments['ls']){
                foreach($comments['ls'] as $k=>$v){
                    $sub_comments = $mdl_comment->get_sub($v['id']);
                    if($sub_comments){
                        foreach($sub_comments as $kk=>$vv){
                            $sub_comments[$kk]['content'] = $this->plugin->filter('comment_content',$vv['content'],$vv['id']);
                        }
                    }
                    $comments['ls'][$k]['content'] = $this->plugin->filter('comment_content',$v['content'],$v['id']);
                    $comments['ls'][$k]['sub_comments'] = $sub_comments;
                }
            }
            $this->output->set('comments_list',$comments['ls']);
            $this->output->set('comments_total_page',$comments['total']);
            $this->output->set('comments_current_page',$comments['current']);
            $this->output->set('ref_id',$id);
            $this->output->set('comments_type',2);
            $this->output->set('enable_comment',true);
        }else{
            $this->output->set('enable_comment',false);
        }
        
        $this->output->set('photo_body_append',$this->plugin->filter('photo_body','',$id));
        $info['desc'] = $this->plugin->filter('photo_desc',$info['desc'],$album_info['id'],$id);
        $this->output->set('picture',$picture);
        $this->output->set('info',$info);
        $this->output->set('photo_col_ctl',$this->plugin->filter('photo_col_ctl',$photo_col_ctl,$id));
        $view_nav = loader::view('photos/view_nav',false);
        $this->output->set('view_nav',$this->plugin->filter('photo_view_nav',$view_nav,$album_info['id'],$id));
        $this->output->set('photo_view_sidebar',$this->plugin->filter('photo_view_sidebar','',$album_info['id'],$id));
        
        $this->output->set('current_rank',$nav['current_rank']);
        $this->output->set('current_photo',$nav['current_rank']+1);
        $this->output->set('album_info',$album_info);
        $this->output->set('album_nav',$album_nav);
        
        $page_title = $info['name'].' - '.$album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = ($info['tags']?implode(',',$info['tags_list']).',':'').$this->setting->get_conf('site.keywords');
        $page_description = $info['desc']?strip_tags($info['desc']):$this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,$info['album_id'],$id);
        
        $this->render();
    }
    
    function meta(){
        $id = $this->getGet('id');
        $info = $this->mdl_photo->get_info($id);
        
        if(!$info){
            showError(lang('photo_not_exists'));
        }
        if(!$info['exif']){
            showError(lang('no_access_view_exif'));
        }
        if(!$this->mdl_album->check_album_priv($info['album_id'])){
            $this->_priv_page($info['album_id']);
            exit;
        }
        
        $info['exif'] = unserialize($info['exif']);
        $exif_obj =& loader::lib('exif');
        $exif = $exif_obj->parse_exif($info['exif']);
        foreach($exif as $k=>$v){
            $metas[] = array(
                'key' =>$k,
                'value' =>$v,
                'cname' => lang('exif_'.$k)
            );
        }
        $this->output->set('metas',$metas);
        $this->output->set('info',$info);
                
        $page_title = lang('view_photo_exif',$info['name']).' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,$info['album_id'],$id);
        
        $this->render();
    }
    
    function modify_name_inline(){
        need_login('ajax_inline');
        
        $id = $this->getGet('id');
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('info',$photo_info);
        $this->render();
    }
    
    function modify_tags_inline(){
        need_login('ajax_inline');
        
        $id = $this->getGet('id');
        $photo_info = $this->mdl_photo->get_info($id);
        $this->output->set('info',$photo_info);
        $this->render();
    }
    function save_tags(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        $tags = safe_convert($this->getPost('tags'));
        
        if( $this->mdl_photo->update($id,array('tags'=>$tags)) ){
            $tag_mdl =& loader::model('tag');
            $tag_mdl->save_tags($id,$tags,2);
            $this->plugin->trigger('modified_photo_tags',$id);
            
            form_ajax_success('text',lang('tags').': '.$tags);
        }else{
            form_ajax_failed('text',lang('modify_photo_tags_failed'));
        }
        return;
    }
    function modify_desc_inline(){
        need_login('ajax_inline');
        
        $id = $this->getGet('id');
        $info = $this->mdl_photo->get_info($id);
        $info['desc'] = safe_invert($info['desc']);
        $this->output->set('info',$info);
        $this->render();
    }
    
    function save_desc(){
        need_login('ajax');
        
        $id = $this->getGet('id');
        $desc = safe_convert($this->getPost('desc'));
        if($desc == ''){
            form_ajax_failed('text',lang('empty_photo_desc'));
        }
        if( $this->mdl_photo->update($id,array('desc'=>$desc)) ){
            $this->plugin->trigger('modified_photo_desc',$id);
            
            form_ajax_success('text',$desc);
        }else{
            form_ajax_failed('text',lang('modify_photo_desc_failed'));
        }
        return;
    }
    
}
