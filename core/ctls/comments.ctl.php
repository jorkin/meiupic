<?php

class comments_ctl extends pagecore{
    
    function _init(){
        $this->mdl_comment = & loader::model('comment');
    }
    function post(){
        if(!$this->setting->get_conf('system.enable_comment')){
            form_ajax_failed('text',lang('album_comment_closed'));
        }
        
        $comment['email'] = safe_convert($this->getPost('email'));
        $comment['author'] = safe_convert($this->getPost('author'));
        $comment['content'] = safe_convert($this->getPost('content'));
        $comment['ref_id'] = intval($this->getPost('ref_id'));
        $comment['type'] = intval($this->getPost('type'));
        
        if($comment['email'] && !check_email($comment['email'])){
            form_ajax_failed('text',lang('error_email'));
        }
        if(!$comment['author']){
            form_ajax_failed('text',lang('error_comment_author'));
        }
        if(!$comment['content']){
            form_ajax_failed('text',lang('empty_content'));
        }
        if(!$comment['ref_id'] || !$comment['type']){
            form_ajax_failed('text',lang('miss_argument'));
        }
        $comment['post_time'] = time();
        $comment['author_ip'] = get_real_ip();
        
        if($comment_id = $this->mdl_comment->save($comment)){
            if($comment['type'] == 1){
                loader::model('album')->update_comments_num($comment['ref_id']);
            }elseif($comment['type'] == 2){
                loader::model('photo')->update_comments_num($comment['ref_id']);
            }
            
            $this->plugin->add_trigger('post_comment',$comment_id);
            form_ajax_success('box',lang('post_comment_success'),null,0.5);
        }else{
            form_ajax_failed('text',lang('post_comment_failed'));
        }
    }
    
    function reply(){
        $id = intval($this->getGet('id'));
        $comment_info = $this->mdl_comment->get_info($id);
        $comment_info['author'] = safe_invert($comment_info['author']);
        $comment_info['pid'] = $comment_info['pid']?  $comment_info['pid']:$comment_info['id'];
        $this->output->set('info',$comment_info);
        $this->render();
    }
    
    function save_reply(){
        if(!$this->setting->get_conf('system.enable_comment')){
            form_ajax_failed('text',lang('album_comment_closed'));
        }
        
        $comment['email'] = safe_convert($this->getPost('email'));
        $comment['author'] = safe_convert($this->getPost('author'));
        $comment['content'] = safe_convert($this->getPost('content'));
        $comment['ref_id'] = intval($this->getPost('ref_id'));
        $comment['type'] = intval($this->getPost('type'));
        $comment['reply_author'] = safe_convert($this->getPost('reply_author'));
        $comment['pid'] = intval($this->getPost('pid'));
        
        if($comment['email'] && !check_email($comment['email'])){
            form_ajax_failed('text',lang('error_email'));
        }
        if(!$comment['author']){
            form_ajax_failed('text',lang('error_comment_author'));
        }
        if(!$comment['content']){
            form_ajax_failed('text',lang('empty_content'));
        }
        if(!$comment['ref_id'] || !$comment['type'] || !$comment['pid'] || !$comment['reply_author']){
            form_ajax_failed('text',lang('miss_argument'));
        }
        $comment['post_time'] = time();
        $comment['author_ip'] = get_real_ip();
        
        if($reply_id = $this->mdl_comment->save($comment)){
            $comment['id'] = $this->mdl_comment->last_insert_id();
            if($comment['type'] == 1){
                loader::model('album')->update_comments_num($comment['ref_id']);
            }elseif($comment['type'] == 2){
                loader::model('photo')->update_comments_num($comment['ref_id']);
            }
            
            $this->output->set('info',$comment);
            
            $this->plugin->add_trigger('reply_comment',$reply_id);
            form_ajax_success('text',loader::view('comments/view',false));
        }else{
            form_ajax_failed('text',lang('reply_failed'));
        }
    }
    
    function confirm_delete(){
        need_login('ajax_page');

        $id = $this->getGet('id');
        $this->output->set('id',$id);
        
        $this->render();
    }
    
    function delete(){
        need_login('ajax_page');

        $id = $this->getGet('id');
        $info = $this->mdl_comment->get_info($id);
        if($this->mdl_comment->delete($id)){
            if($info['type'] == 1){
                loader::model('album')->update_comments_num($info['ref_id']);
            }elseif($info['type'] == 2){
                loader::model('photo')->update_comments_num($info['ref_id']);
            }
            
            $this->plugin->add_trigger('deleted_comment',$id);
            ajax_box(lang('delete_comment_success'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box(lang('delete_comment_failed'));
        }
    }
    
    function more(){
        $ref_id = intval($this->getGet('ref_id'));
        $type = intval($this->getGet('type'));
        $page = $this->getGet('page',1);
        $comments = $this->mdl_comment->get_all($page,array('ref_id'=>$ref_id,'type'=>$type));
        if($comments['ls']){
            foreach($comments['ls'] as $k=>$v){
                $sub_comments = $this->mdl_comment->get_sub($v['id']);
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
        $this->output->set('ref_id',$ref_id);
        $this->output->set('comments_type',$type);
        
        $this->render();
    }
}