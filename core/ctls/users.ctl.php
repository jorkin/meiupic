<?php

class users_ctl extends pagecore{
    
    function _init(){
        
    }
    
    function login(){
        $ajax = $this->getGet('ajax');
        if($ajax == 'true'){
            $this->output->set('ajax',true);
        }else{
            $this->output->set('ajax',false);
            $page_title = '用户登录 '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');
            $this->page_init($page_title,$page_keywords,$page_description);
        }
        $this->render();
    }
    
    function check_login(){
        $login_name = safe_convert($this->getPost('login_name'));
        $login_pass = $this->getPost('login_pass');
        $remember_pass = $this->getPost('remember_pass');
        $normal = $this->getPost('normal');
        if(!$login_name){
            ajax_box_failed('请输入用户名！');
        }
        if(!$login_pass){
            ajax_box_failed('请输入密码！');
        }
        if($remember_pass){
            $expire_time = time()+86400*30; //记住密码30天
        }else{
            $expire_time = 0;
        }
        $go_url = $normal?site_link('default'):$_SERVER['HTTP_REFERER'];
        if($this->user->set_login($login_name,md5($login_pass),$expire_time)){
            ajax_box_success('登录成功！',null,0.5,$go_url);
        }else{
            ajax_box_failed('请验证用户名和密码是否正确！');
        }
    }
    
    function profile(){
        if(!$this->user->loggedin()){
            redirect(site_link('users','login'));
        }
        
        $this->output->set('info',$this->user->get_all_field());
        
        $page_title = '修改个人资料 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function save_profile(){
        $current_id = $this->user->get_field('id');
        
        $arr['user_nicename'] = safe_convert($this->getPost('user_nicename'));
        $new_pass = $this->getPost('new_pass');
        $old_pass = $this->getPost('old_pass');
        $new_pass_again = $this->getPost('new_pass_again');
        if($new_pass){
            if(!$this->user->check_pass($current_id,md5($old_pass))){
                ajax_box_failed('旧密码输入错误！');
            }
            if($new_pass != $new_pass_again){
                ajax_box_failed('两次密码输入不一致！');
            }
            $arr['user_pass'] = md5($new_pass);
        }
        if($this->user->update($current_id,$arr)){
            ajax_box_success('修改成功！'.($new_pass?'您的密码已经修改，请重新登录！':''),null,0.5,site_link('users','login'));
        }else{
            ajax_box_failed('保存失败！');
        }
    }
    
    function logout(){
        $this->user->clear_login();
        echo ajax_box('退出登录成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
}