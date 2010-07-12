<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends adminpage{
    
    function controller(){
        parent::adminpage();
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
    }
    function index(){        
        $mdl_setting =& load_model('setting');
    
        if($this->isPost()){
            $new_setting = $this->getPost('setting');
            
            foreach($new_setting as $k=>$v){
                $new_setting[$k] = trim($v);
            }
            if(empty($new_setting['url'])){
                showInfo('网站url不能为空！',false);
            }
            if(isset($new_setting['open_pre_resize'])){
                $new_setting['open_pre_resize'] = 'true';
            }else{
                $new_setting['open_pre_resize'] = 'false';
            }
            if(isset($new_setting['demand_resize'])){
                $new_setting['demand_resize'] = 'true';
            }else{
                $new_setting['demand_resize'] = 'false';
            }
            if(isset($new_setting['open_photo'])){
                $new_setting['open_photo'] = 'true';
            }else{
                $new_setting['open_photo'] = 'false';
            }
            if(isset($new_setting['access_ctl'])){
                $new_setting['access_ctl'] = 'true';
            }else{
                $new_setting['access_ctl'] = 'false';
            }
            
            if(empty($new_setting['resize_img_width']) || !is_numeric($new_setting['resize_img_width'])){
                showInfo('图片宽只能为数字！',false);
            }
            if(empty($new_setting['resize_img_height']) || !is_numeric($new_setting['resize_img_height'])){
                showInfo('图片高只能为数字！',false);
            }
            if($new_setting['resize_quality']<0 || $new_setting['resize_quality']>100){
                showInfo('图片质量只能在0-100之间！',false);
            }
            if(empty($new_setting['size_allow']) || !is_numeric($new_setting['size_allow'])){
                showInfo('普通上传允许的图片大小只能为数字！',false);
            }
            if(empty($new_setting['pageset']) || !is_numeric($new_setting['pageset'])){
                showInfo('分页设置只能为数字！',false);
            }
            if(empty($new_setting['gallery_limit']) || !is_numeric($new_setting['gallery_limit'])){
                showInfo('幻灯片图片限制只能为数字！',false);
            }
            if($mdl_setting->save_setting($new_setting)){
                showInfo('修改配置成功！',true,'admin.php?ctl=setting');
            }else{
                showInfo('写入配置失败,请检查conf/setting.php文件是否可写！',false);
            }
        
        }else{
            $this->output->set('setting',$mdl_setting->get_setting());
            $this->output->set('setting_nav','index');
            $this->view->display('admin/setting.php');
        }
    }

    function password(){
        if($this->isPost()){
            $oldpass=$this->auth->getInfo('userpass');
            $post_oldpass = $this->getPost('oldpass');
            $new_pass = $this->getPost('newpass');
            $new_pass_again = $this->getPost('passagain');
            if(md5($post_oldpass) != $oldpass){
                showInfo('旧密码输入错误！',false);
            }
            if(empty($new_pass)){
                showInfo('新密码不能为空！',false);
            }
            if($new_pass != $new_pass_again){
                showInfo('两次密码输入不一致！',false);
            }
            $id = $this->auth->getInfo('id');
            $loginname = $this->auth->getInfo('username');
            $newpass = md5($new_pass);
            $mdl_setting =& load_model('setting');
            if($mdl_setting->change_admin_pass(intval($id),$newpass)){
                $this->auth->setLogin($loginname,$newpass);
                showInfo('密码修改成功！',true,'admin.php?ctl=setting&act=password');
            }else{
                showInfo('密码修改失败！',false);
            }
        }else{
            $this->output->set('setting_nav','password');
            $this->view->display('admin/setting_password.php');
        }
    
    }
    
    function operator(){
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        $pageurl = 'admin.php?ctl=setting&act=operator&page=[#page#]';
        
        $list = load_model('operator')->getList($page);
        $this->output->set('mlist',$list['ls']);
        $this->output->set('pageset',pageshow($list['total'],$list['start'],$pageurl));
        $this->output->set('setting_nav','operator');
        $this->view->display('admin/setting_operator.php');
    }
    
    function operator_add(){
        if($this->isPost()){
            ;
        } else {
            $this->output->set('setting_nav','operator');
            $this->view->display('admin/setting_operator_add.php');
        }
    }
    
    function operator_edit(){
        if($this->isPost()){
            ;
        } else {
            $this->output->set('setting_nav','operator');
            $this->view->display('admin/setting_operator_edit.php');
        }
    }
    
    function operator_del(){
        ;
    }
}