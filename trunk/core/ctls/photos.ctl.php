<?php

class photos_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo = & loader::model('photo');
    }
    
    function _sort_setting(){
        return array('上传时间' => 'tu','拍摄时间' => 'tt','浏览数'=>'h','评论数'=>'c','照片名'=>'n');
    }
    
    function index(){
        $this->normal();
    }
    
    function normal(){
        $page = $this->getGet('page',1);
        $search['name'] = $this->getRequest('sname');
        $search['album_id'] = $album_id = $this->getGet('aid');
        
        $album_info = $this->mdl_album->get_info($album_id);
        if(!$album_info){
            showError('您要访问的相册不存在！');
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
        $this->mdl_photo->set_pageset($pageset);
        
        $view_type = '<div class="f_right selectlist viewtype">
        <span class="label">浏览模式:</span>
        <div class="selected"></div>
        <ul class="optlist">
        <li class="current"><a href="'.site_link('photos','index',array('aid'=>$album_id)).'">平铺模式</a></li>
        <li><a href="'.site_link('photos','slide',array('aid'=>$album_id)).'">幻灯模式</a></li>
        </ul>
        </div>';
        
        
        
        $photos = $this->mdl_photo->get_all($page,$search,$sort);
        if(is_array($photos['ls'])){
            foreach($photos['ls'] as $k=>$v){
                $photos['ls'][$k]['photo_control_icons'] = $this->plugin->filter('photo_control_icons','',$v['id']);
            }
        }
        
        $pagestr = loader::lib('page')->fetch($photos['total'],$photos['current'],$pageurl);
        $album_menu = '<li><a href="'.
                        site_link('photos','index',array('aid'=>$album_id)).
                      '" class="current">'.$album_info['name'].'</a></li>';
        
        $album_info['tags_list'] = explode(' ',$album_info['tags']);
        
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
        $this->output->set('search',$search);
        $this->output->set('pagestr',$pagestr);
        $this->output->set('total_num',$photos['count']);
        $this->output->set('album_info',$album_info);
        $this->output->set('album_menu',$this->plugin->filter('album_menu',$album_menu,$album_id));
        $this->output->set('show_takentime',($sort=='tt_desc'||$sort=='tt_asc')?true:false);
        
        $page_title = $album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,array('aid'=>$album_id));
        
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
                ajax_box_failed('相册密码输入错误！');
            }
            setCookie($key,md5($priv_pass));
            ajax_box_success('验证成功！',null,0.5,$go_url);
        }elseif($album_info['priv_type'] == 2){
            $priv_answer = $this->getPost('priv_answer');
            if($album_info['priv_answer'] != $priv_answer){
                ajax_box_failed('相册答案输入错误！');
            }
            setCookie($key,md5($album_info['priv_question'].$priv_answer));
            ajax_box_success('验证成功！',null,0.5,$go_url);
        }
        ajax_box_failed('相册类别错误！');
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
                echo ajax_box('已认证，正在转入...',null,0.5,site_link('photos','index',array('aid'=>$id)));
                exit;
            }
        }else{
            $this->output->set('ajax',false);
            $page_title = '访问需要验证 - 系统信息 - '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');
            $this->page_init($page_title,$page_keywords,$page_description);
        }
        
        loader::view('photos/priv_page');
    }
    
    function slide(){
        $album_id = $this->getGet('aid');
        $album_info = $this->mdl_album->get_info($album_id);
        $refer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:site_link('photos','index',array('aid'=>$album_id));
        
        $page_title = $album_info['name'].' - 幻灯片 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,array('aid'=>$album_id));
        
        $this->output->set('refer',$refer);
        $this->output->set('album_id',$album_id);
        $this->render();
    }
    
    function gallery(){
        $album_id = $this->getGet('aid');
        $info = $this->mdl_album->get_info($album_id);
        
        if(!$this->mdl_album->check_album_priv($album_id,$info)){
            exit('对不起你没有权限！');
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
                $par['name'] = $search['name'];
            }
            $pageurl = site_link('photos','index',$par);

            $sort_setting = $this->_sort_setting();
            list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
            list($pageset,$page_setting_str) = get_page_setting('photo');
            $this->mdl_photo->set_pageset($pageset);

            $photos = $this->mdl_photo->get_all($page,$search,$sort);
            if(is_array($photos['ls'])){
                foreach($photos['ls'] as $k=>$v){
                    $photos['ls'][$k]['photo_control_icons'] = $this->plugin->filter('photo_control_icons','',$v['id']);
                    $photos['ls'][$k]['photo_priv'] = $this->mdl_album->check_album_priv($v['album_id']);
                }
            }

            $pagestr = loader::lib('page')->fetch($photos['total'],$photos['current'],$pageurl);

            $this->output->set('photo_col_menu',$this->plugin->filter('photo_col_menu',$page_setting_str.$sort_list));
            $this->output->set('photos',$photos['ls']);
            $this->output->set('search',$search);
            $this->output->set('pagestr',$pagestr);
            $this->output->set('total_num',$photos['count']);
            $this->output->set('album_menu',$this->plugin->filter('album_menu','<li><a href="'.
                            site_link('photos','search',array('sname'=>$search['name'])).
                          '" class="current">搜索结果</a></li>'));

            $page_title = $search['name'].' - 搜索结果 - '.$this->setting->get_conf('site.title');
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
            ajax_box_failed('照片名不能为空！');
        }
        
        if($this->mdl_photo->update($id,$album)){
            loader::model('tag')->save_tags($id,$album['tags'],2);
            
            ajax_box_success('修改照片成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('修改照片失败！');
        }
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
        
        if($this->mdl_photo->trash($this->getGet('id'))){
            echo ajax_box('成功删除照片!',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            echo ajax_box('删除照片失败!');
        }
    }
    
    function confirm_delete_batch(){
        need_login('ajax_page');
        
        $ids = $this->getPost('sel_id');
        if(!$ids || count($ids) == 0){
            echo ajax_box('请先选择要删除的照片!');
            return ;
        }
        $this->render();
    }
    
    function delete_batch(){
        need_login('ajax_page');
        
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
        need_login('ajax');
        
        $id = $this->getGet('id');
        $arr['name'] = safe_convert($this->getPost('name'));
        if($arr['name'] == ''){
            $return = array(
                'ret'=>false,
                'html'=>'照片名不能为空！'
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
                'html'=>'照片名保存失败！'
            );
        }
        echo loader::lib('json')->encode($return);
        return;
    }
    
    
    function view(){
        $id = $this->getGet('id');
        $info = $this->mdl_photo->get_info($id);
        
        if(!$info){
            showError('您要访问的照片不存在！');
        }
        
        $info['exif'] = unserialize($info['exif']);
        $info['tags_list'] = explode(' ',$info['tags']);
        
        $album_info = $this->mdl_album->get_info($info['album_id']);
        if(!$this->mdl_album->check_album_priv($album_info['id'],$album_info)){
            $this->_priv_page($album_info['id'],$album_info);
            exit;
        }
        
        $album_menu = '<li><a href="'.site_link('photos','index',array('aid'=>$info['album_id'])).'" class="current">'.$album_info['name'].'</a></li>';
        $photo_col_ctl = '';

        $this->mdl_photo->add_hit($id);
        
        $mdl_comment =& loader::model('comment');
        $comments = $mdl_comment->get_all(1,array('ref_id'=>$id,'type'=>2));
        if($comments['ls']){
            foreach($comments['ls'] as $k=>$v){
                $comments['ls'][$k]['sub_comments'] = $mdl_comment->get_sub($v['id']);
            }
        }
        
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
        
        $this->output->set('comments_list',$comments['ls']);
        $this->output->set('comments_total_page',$comments['total']);
        $this->output->set('comments_current_page',$comments['current']);
        $this->output->set('ref_id',$id);
        $this->output->set('comments_type',2);
        
        $this->output->set('picture',$picture);
        $this->output->set('current_rank',$nav['current_rank']);
        $this->output->set('current_photo',$nav['current_rank']+1);
        
        $this->output->set('album_info',$album_info);
        $this->output->set('info',$info);
        $this->output->set('album_menu',$this->plugin->filter('album_menu',$album_menu,$info['album_id']));
        $this->output->set('photo_col_ctl',$this->plugin->filter('photo_col_ctl',$photo_col_ctl,$id));
        
        $page_title = $info['name'].' - '.$album_info['name'].' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,array('id'=>$id));
        
        $this->render();
    }
    
    function meta(){
        $id = $this->getGet('id');
        $info = $this->mdl_photo->get_info($id);
        
        if(!$info){
            exit('照片不存在！');
            showError('您要访问的照片不存在！');
        }
        if(!$info['exif']){
            showError('无权查看EXIF！');
        }
        if(!$this->mdl_album->check_album_priv($info['album_id'])){
            $this->_priv_page($info['album_id']);
            exit;
        }
        
        $info['exif'] = unserialize($info['exif']);
        $exif = loader::lib('exif')->parse_exif($info['exif']);
        foreach($exif as $k=>$v){
            $metas[] = array(
                'key' =>$k,
                'value' =>$v,
                'cname' => lang('exif_'.$k)
            );
        }
        $this->output->set('metas',$metas);
        $this->output->set('info',$info);
        
        $this->output->set('album_menu',$this->plugin->filter('album_menu','',$info['album_id']));
        
        $page_title = '查看看照片'.$info['name'].'的EXIF信息 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description,array('id'=>$id));
        
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
            loader::model('tag')->save_tags($id,$tags,2);
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
            $return = array(
                'ret'=>false,
                'html' => '相册描述不能为空！'
            );
            echo loader::lib('json')->encode($return);
            return;
        }
        if( $this->mdl_photo->update($id,array('desc'=>$desc)) ){
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
    
    function nav(){
        $aid = $this->getGet('aid');
        $rank_id = $this->getPost('rank_id');
        $direction = $this->getPost('direction');
        
        $sort_setting = $this->_sort_setting();
        list($sort,$sort_list) =  get_sort_list($sort_setting,'photo','tu_desc');
        
        $nav['items'] = $this->mdl_photo->get_items(array('album_id'=>$aid),$sort);
        $nav['rank_of'] = array_flip($nav['items']);
        $nav['first_rank']   = 0;
        $nav['last_rank']    = count($nav['items']) - 1;
        $nav['current_rank'] = $rank_id;
        
        $first_str = '<li id="pic_first" class="navitem"><a href="javascript:void(0);">这是首张</a></li>';
        $last_str = '<li id="pic_last" class="navitem"><a href="javascript:void(0);">这是末张</a></li>';
        if($rank_id==0 && $direction == 'up'){
            echo $first_str;
            return;
        }elseif($rank_id == $nav['last_rank'] && $direction == 'down'){
            echo $last_str;
            return;
        }
        if($direction == 'up'){
            if($rank_id-3<0){
                $start = 0;
                echo $first_str;
            }else{
                $start = $rank_id-3;
            }
            for($i = $start;$i<$rank_id;$i++){
                $item_id = $nav['items'][$i];
                $item = $this->mdl_photo->get_info($item_id);
                echo '<li class="navitem" id="pic_'.$i.'"><a style="background:url(\''.img_path($item['thumb']).'\') center no-repeat;" href="'.site_link('photos','view',array('id'=>$item['id'])).'#pic_block"></a></li>';
            }
            
        }elseif($direction == 'down'){
            $nums = 0;
            $end = $rank_id+3>$nav['last_rank']?$nav['last_rank']:$rank_id+3;
            for($i = $rank_id+1;$i<=$end;$i++){
                $item_id = $nav['items'][$i];
                $item = $this->mdl_photo->get_info($item_id);
                echo '<li class="navitem" id="pic_'.$i.'"><a style="background:url(\''.img_path($item['thumb']).'\') center no-repeat;" href="'.site_link('photos','view',array('id'=>$item['id'])).'#pic_block"></a></li>';
                $nums++;
            }
            if($nums<3){
                echo $last_str;
            }
        }
    }
}
