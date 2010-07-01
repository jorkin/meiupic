<?php
/**
 * $Id: default.php 22 2010-06-06 15:50:07Z lingter $
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
            $this->view->display('admin/setting.php');
        }
    }

    function changepass(){
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
            showInfo('密码修改成功！',true,'admin.php?ctl=setting');
        }else{
            showInfo('密码修改失败！',false);
        }
    
    }
}