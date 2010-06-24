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
        if(!$this->auth->isLogedin() && IN_ACT!='login'){
            redirect_c('default','login');
        }
    }
    
    function index(){
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $flag = $this->getGet('flag',0);
        
        switch($flag){
            case '1':
            $msginfo = '<div id="msginfo" class="fail">操作失败！请先选择要操作的图片！ <a href="javascript:void(0)"onclick="$(\'#msginfo\').hide()">[关闭]</a></div>';
            break;
            case '2':
            $msginfo = '<div id="msginfo" class="fail">操作失败！请选择要移动的相册！<a href="javascript:void(0)"onclick="$(\'#msginfo\').hide()">[关闭]</a></div>';
            break;
            case '3':
            $msginfo = '<div id="msginfo" class="succ">操作成功！<a href="javascript:void(0)"onclick="$(\'#msginfo\').hide()">[关闭]</a></div>';
            break;
            default:
            $msginfo = '';
        }
        
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        $sort = $this->getGet('sort','id_desc');
        $mdl_picture = & load_model('picture');
        $mdl_album =& load_model('album');
        $pics = $mdl_picture->get_all_pic($page,0,$sort);
        $pageurl='admin.php?page=[#page#]&sort='.$sort;
        $this->output->set('albums_list',$mdl_album->get_albums_assoc());
        $this->output->set('pics',$pics['ls']);
        $this->output->set('page',$page);
        $this->output->set('sort',$sort);
        $this->output->set('msginfo',$msginfo);
        $this->output->set('pageset',pageshow($pics['total'],$pics['start'],$pageurl));
        $this->output->set('total_num',$pics['count']);
        $this->output->set('current_nav','all');
        $this->view->display('admin/default.php');
    }
    
    function login(){
        if($this->isPost()){
            $username = addslashes($this->getPost('loginname'));
            $userpass = $this->getPost('operatorpw');
            $remember = $this->getPost('remember');
            if($remember){
                $expire_time = time()+86400*365;
            }else{
                $expire_time = 0;
            }
            if($this->auth->setLogin($username,md5($userpass),$expire_time)){
                redirect_c('album','index');
            }else{
                redirect('admin.php?ctl=default&act=login&flag=1');
            }
        }else{
            $flag = $this->getGet('flag');
            $this->output->set('flag',$flag);
            $this->view->display('admin/login.php');
        }
    }
    
    function logout(){
        $this->auth->clearLogin();
        redirect('admin.php?ctl=default&act=login&flag=2');
    }
}
?>