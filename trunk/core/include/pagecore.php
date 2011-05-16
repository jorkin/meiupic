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
        $this->user =& loader::model('user');
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
        $meu_head = loader::lib('plugin')->filter('meu_head',$head_str,$arr);
        $meu_head .= "\n".'<meta name="generator" content="Mei'.'u'.'Pic '.MPIC_VERSION.'" />'."\n";
        $this->output->set('meu_head',$meu_head);

        if(!$this->user->loggedin()){
            $user_status = '<a href="'.site_link('users','login').'" onclick="Mui.box.show(this.href,true);return false;">登录</a>';
        }else{
            $user_status = '<span class="name">'.$this->user->get_field('user_nicename').'</span>
            <span class="pipe">|</span>
            <a title="查看和修改我的个人资料" href="'.site_link('users','profile').'">我的资料</a>
            <span class="pipe">|</span>
            <a title="登出系统" href="'.site_link('users','logout').'" onclick="Mui.box.show(this.href);return false;">登出</a>';
        }
        $this->output->set('user_status',loader::lib('plugin')->filter('user_status',$user_status));
        $this->output->set('trash_status',has_trash());
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
            if(!MAGIC_GPC)
            {
                return arr_addslashes($_GET[$key]);
            }
            return $_GET[$key];
        }
        return $default;
    }
    
    function getPost($key,$default=''){
        if(isset($_POST[$key])){
            if(!MAGIC_GPC)
            {
                return arr_addslashes($_POST[$key]);
            }
            return $_POST[$key];
        }
        return $default;
    }

    function getRequest($key,$default=''){
        if(isset($_REQUEST[$key])){
            if(!MAGIC_GPC)
            {
                return arr_addslashes($_REQUEST[$key]);
            }
            return $_REQUEST[$key];
        }
        return $default;
    }
    
    function getPosts(){
        if(!MAGIC_GPC)
        {
            return arr_addslashes($_POST);
        }
        return $_POST;
    }
    
    function getRequests(){
        if(!MAGIC_GPC)
        {
            return arr_addslashes($_REQUEST);
        }
        return $_REQUEST;
    }
    
    function getGets(){
        if(!MAGIC_GPC)
        {
            return arr_addslashes($_GET);
        }
        return $_GET;
    }
}