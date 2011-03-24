<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
class pagecore{
    
    function pagecore(){
        $this->output =& loader::lib('output');
        $this->db =& loader::database();
        $this->auth =& loader::model('auth');
        $this->setting =& loader::model('setting');
    }
    
    function _init(){
        ;
    }
    function _called(){
        ;
    }
    
    /*
     run page init
     initialize page head and user status
    */
    function page_init($title = '',$keywords = '',$description='',$arr=array()){
        $head_str = "<title>{$title} - Powered by MeiuPic</title>\n";
        $head_str .= "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
        $head_str .= "<meta name=\"description\" content=\"{$description}\" />\n";
        $this->output->set('meu_head',loader::lib('plugin')->filter('meu_head',$head_str,$arr));

        if(!$this->auth->loggedin()){
            $user_status = '<a href="#">登录</a>';
        }else{
            $user_status = '<span class="name">Lingter</span>
            <span class="pipe">|</span>
            <a title="查看和修改我的个人资料" href="#">我的资料</a>
            <span class="pipe">|</span>
            <a title="登出系统" href="#">登出</a>';
        }
        $this->output->set('user_status',loader::lib('plugin')->filter('user_status',$user_status));
    }
    
    function render($type = 'normal'){
        if($type == 'normal'){
            $tpl = IN_CTL.'/'.IN_ACT;
        }else{
            $tpl = IN_CTL.'/'.IN_ACT.'_'.$type;
        }
        loader::view($tpl);
    }
    
    function isPost(){
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            return true;
        }
        return false;
    }

    function getGet($key,$default=''){
        if(isset($_GET[$key])){
            if(!get_magic_quotes_gpc())
            {
                if(is_array($_GET[$key])){
                    return array_map('addslashes',$_GET[$key]);
                }else{
                    return addslashes($_GET[$key]);
                }
            }
            return $_GET[$key];
        }
        return $default;
    }

    function getPost($key,$default=''){
        if(isset($_POST[$key])){
            if(!get_magic_quotes_gpc())
            {
                if(is_array($_POST[$key])){
                    return array_map('addslashes',$_POST[$key]);
                }else{
                    return addslashes($_POST[$key]);
                }
            }
            return $_POST[$key];
        }
        return $default;
    }

    function getRequest($key,$default=''){
        if(isset($_REQUEST[$key])){
            if(!get_magic_quotes_gpc())
            {
                if(is_array($_REQUEST[$key])){
                    return array_map('addslashes',$_REQUEST[$key]);
                }else{
                    return addslashes($_REQUEST[$key]);
                }
            }
            return $_REQUEST[$key];
        }
        return $default;
    }
}