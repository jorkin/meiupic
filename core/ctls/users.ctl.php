<?php

class users_ctl extends pagecore{
    
    function _init(){
        
    }
    
    function login(){
        $this->render();
    }
    
    function check_login(){
        $login_name = safe_convert($this->getPost('login_name'));
        $login_pass = $this->getPost('login_pass');
        if(!$login_name){
            ajax_box_failed('请输入用户名！');
        }
        if(!$login_pass){
            ajax_box_failed('请输入密码！');
        }
        
        if($this->user->set_login($login_name,md5($login_pass))){
            ajax_box_success('登录成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box_failed('请验证用户名和密码是否正确！');
        }
    }
    
    function logout(){
        $this->user->clear_login();
        echo ajax_box('退出登录成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
}