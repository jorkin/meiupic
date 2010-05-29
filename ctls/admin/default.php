<?php

class controller extends pagefactory{
    
    function index(){
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $page = $_GET['page'];
        if(!$page){
            $page = 1;
        }
        $mdl_picture = & load_model('picture');
        $pics = $mdl_picture->get_all_pic($page);
        $pageurl='index.php?page=[#page#]';
        $this->output->set('pics',$pics['ls']);
        $this->output->set('pageset',pageshow($pics['total'],$pics['start'],$pageurl));
        $this->output->set('total_num',$pics['count']);
        $this->output->set('current_nav','all');
        $this->view->display('default.php');
    }
    
    function login(){
        if($this->isPost()){
            $username = addslashes($_POST['loginname']);
            $userpass = $_POST['operatorpw'];
            $remember = $_POST['remember'];
            if($remember){
                $expire_time = time()+86400*365;
            }else{
                $expire_time = 0;
            }
            if($this->auth->setLogin($username,md5($userpass),$expire_time)){
                redirect_c('default','index');
            }else{
                redirect('index.php?ctl=default&act=login&flag=1');
            }
        }else{
            $flag = $_GET['flag'];
            $this->output->set('flag',$flag);
            $this->view->display('login.php');
        }
    }
    
    function logout(){
        $this->auth->clearLogin();
        redirect('index.php?ctl=default&act=login&flag=2');
    }
    
    function setting(){        
        $mdl_setting =& load_model('setting');
        
        if($this->isPost()){
            $new_setting = $_POST['setting'];
            foreach($new_setting as $k=>$v){
                $new_setting[$k] = trim($v);
            }
            if(empty($new_setting['url'])){
                showInfo('网站url不能为空！',false);
            }
            if(empty($new_setting['imgdir'])){
                showInfo('图片保存目录不能为空！',false);
            }
            if(!is_dir(ROOTDIR.$new_setting['imgdir']) || !is_writable(ROOTDIR.$new_setting['imgdir'])){
                showInfo('图片保存目录不存在或不可写！',false);
            }
            if(empty($new_setting['upload_runtimes'])){
                showInfo('高级上传引擎不能为空！',false);
            }
            $runtimes = explode(',',$new_setting['upload_runtimes']);
            foreach($runtimes as $r){
                if(!in_array($r,array('html5','flash','gears','silverlight','browserplus','html4'))){
                    showInfo('不支持的上传引擎：'.$r.'！',false);
                }
            }
            if($new_setting['open_pre_resize'] == 1){
                $new_setting['open_pre_resize'] = 'true';
            }else{
                $new_setting['open_pre_resize'] = 'false';
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
            if(empty($new_setting['extension_allow'])){
                showInfo('允许的图片格式不能为空！',false);
            }
            $pic_exts = explode(',',$new_setting['extension_allow']);
            foreach($pic_exts as $r){
                if(!in_array($r,array('jpg','jpeg','png','gif'))){
                    showInfo('程序暂时不支持此种格式：'.$r.'！',false);
                }
            }
            if(empty($new_setting['size_allow']) || !is_numeric($new_setting['size_allow'])){
                showInfo('普通上传允许的图片大小只能为数字！',false);
            }
            if(empty($new_setting['pageset']) || !is_numeric($new_setting['size_allow'])){
                showInfo('分页设置只能为数字！',false);
            }
            
            if($mdl_setting->save_setting($new_setting)){
                showInfo('修改网站配置成功！',true,'index.php?ctl=default&act=setting');
            }else{
                showInfo('写入配置失败,请检查conf/setting.php文件是否可写！',false);
            }
            
        }else{
            $this->output->set('setting',$mdl_setting->get_setting());
            $this->view->display('setting.php');
        }
    }
    
    function changepass(){
        $oldpass=$this->auth->getInfo('userpass');
        if(md5($_POST['oldpass']) != $oldpass){
            showInfo('旧密码输入错误！',false);
        }
        if(empty($_POST['newpass'])){
            showInfo('新密码不能为空！',false);
        }
        if($_POST['newpass'] != $_POST['passagain']){
            showInfo('两次密码输入不一致！',false);
        }
        $id = $this->auth->getInfo('id');
        $loginname = $this->auth->getInfo('username');
        $newpass = md5($_POST['newpass']);
        $mdl_setting =& load_model('setting');
        if($mdl_setting->change_admin_pass(intval($id),$newpass)){
            $this->auth->setLogin($loginname,$newpass);
            showInfo('密码修改成功！',true,'index.php?ctl=default&act=setting');
        }else{
            showInfo('密码修改失败！',false);
        }
        
    }
}
?>