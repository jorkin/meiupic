<?php

class comments_ctl extends pagecore{
    
    function _init(){
        $this->mdl_comment = & loader::model('comment');
    }
    function post(){
        if(!$this->setting->get_conf('system.enable_comment')){
            form_ajax_failed('text','相册关闭了评论！');
        }
        
        $comment['email'] = safe_convert($this->getPost('email'));
        $comment['author'] = safe_convert($this->getPost('author'));
        $comment['content'] = safe_convert($this->getPost('content'));
        $comment['ref_id'] = intval($this->getPost('ref_id'));
        $comment['type'] = intval($this->getPost('type'));
        
        if($comment['email'] && !check_email($comment['email'])){
            form_ajax_failed('text','请输入有效的Email地址！');
        }
        if(!$comment['author']){
            form_ajax_failed('text','请输入评论者名字！');
        }
        if(!$comment['content']){
            form_ajax_failed('text','内容不能为空！');
        }
        if(!$comment['ref_id'] || !$comment['type']){
            form_ajax_failed('text','参数丢失！');
        }
        $comment['post_time'] = time();
        $comment['author_ip'] = get_real_ip();
        
        if($this->mdl_comment->save($comment)){
            if($comment['type'] == 1){
                loader::model('album')->update_comments_num($comment['ref_id']);
            }elseif($comment['type'] == 2){
                loader::model('photo')->update_comments_num($comment['ref_id']);
            }
            
            form_ajax_success('box','评论成功！',null,0.5);
        }else{
            form_ajax_failed('text','评论失败！');
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
            form_ajax_failed('text','相册关闭了评论！');
        }
        
        $comment['email'] = safe_convert($this->getPost('email'));
        $comment['author'] = safe_convert($this->getPost('author'));
        $comment['content'] = safe_convert($this->getPost('content'));
        $comment['ref_id'] = intval($this->getPost('ref_id'));
        $comment['type'] = intval($this->getPost('type'));
        $comment['reply_author'] = safe_convert($this->getPost('reply_author'));
        $comment['pid'] = intval($this->getPost('pid'));
        
        if($comment['email'] && !check_email($comment['email'])){
            form_ajax_failed('text','请输入有效的Email地址！');
        }
        if(!$comment['author']){
            form_ajax_failed('text','请输入评论者名字！');
        }
        if(!$comment['content']){
            form_ajax_failed('text','内容不能为空！');
        }
        if(!$comment['ref_id'] || !$comment['type'] || !$comment['pid'] || !$comment['reply_author']){
            form_ajax_failed('text','参数丢失！');
        }
        $comment['post_time'] = time();
        $comment['author_ip'] = get_real_ip();
        
        if($this->mdl_comment->save($comment)){
            $comment['id'] = $this->mdl_comment->last_insert_id();
            if($comment['type'] == 1){
                loader::model('album')->update_comments_num($comment['ref_id']);
            }elseif($comment['type'] == 2){
                loader::model('photo')->update_comments_num($comment['ref_id']);
            }
            
            $this->output->set('info',$comment);

            form_ajax_success('text',loader::view('comments/view',false));
        }else{
            form_ajax_failed('text','回复失败！');
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
            }elseif($comment['type'] == 2){
                loader::model('photo')->update_comments_num($info['ref_id']);
            }
            ajax_box('成功删除评论!',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box('删除评论失败!');
        }
    }
    
    function more(){
        $ref_id = intval($this->getGet('ref_id'));
        $type = intval($this->getGet('type'));
        $page = $this->getGet('page',1);
        $comments = $this->mdl_comment->get_all($page,array('ref_id'=>$ref_id,'type'=>$type));
        if($comments['ls']){
            foreach($comments['ls'] as $k=>$v){
                $comments['ls'][$k]['sub_comments'] = $this->mdl_comment->get_sub($v['id']);
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