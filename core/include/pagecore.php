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
        $this->plugin =& loader::lib('plugin');
        
        $this->plugin->trigger('controller_init');
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
    function page_init($title = '',$keywords = '',$description='',$album_id=null,$photo_id=null){
        $plugin =& loader::lib('plugin');
        
        $head_str = "<title>{$title} - Powered by MeiuPic</title>\n";
        $head_str .= "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
        $head_str .= "<meta name=\"description\" content=\"{$description}\" />\n";
        $meu_head = $plugin->filter('meu_head',$head_str,$album_id,$photo_id);
        $meu_head .= "\n".'<meta name="generator" content="Mei'.'u'.'Pic '.MPIC_VERSION.'" />'."\n";
        
        $feed_url = $album_id?site_link('feed','index',array('aid'=>$album_id)):site_link('feed');
        $feed_title = $this->setting->get_conf('site.title');
        $meu_head .= "<link rel=\"alternate\" title=\"{$feed_title}\" href=\"".$feed_url."\" type=\"application/rss+xml\" />\n";
        $this->output->set('meu_head',$meu_head);

        if(!$this->user->loggedin()){
            $user_status = '<a href="'.site_link('users','login').'" title="'.lang('login_title').'" onclick="Mui.box.show(this.href,true);return false;">'.lang('login').'</a>';
        }else{
            $user_status = '<span class="name">'.$this->user->get_field('user_nicename').'</span>
            <span class="pipe">|</span>
            <a title="'.lang('profile_title').'" href="'.site_link('users','profile').'">'.lang('profile').'</a>
            <span class="pipe">|</span>
            <a title="'.lang('sys_setting_title').'" href="'.site_link('setting').'">'.lang('sys_setting').'</a>
            <span class="pipe">|</span>
            <a title="'.lang('logout_title').'" href="'.site_link('users','logout').'" onclick="Mui.box.show(this.href);return false;">'.lang('logout').'</a>';
        }
        $this->output->set('user_status',$plugin->filter('user_status',$user_status));
        $page_head = $plugin->filter('page_head','',$album_id,$photo_id);
        $page_foot = $plugin->filter('page_foot','',$album_id,$photo_id);
        
        $this->output->set('page_head',$page_head);
        $this->output->set('page_foot',$page_foot);
        $this->output->set('trash_status',has_trash());
        
        $main_menu = loader::view('block/main_menu',false);
        $this->output->set('main_menu',$plugin->filter('main_menu',$main_menu,$album_id,$photo_id));
    }
    
    function page_crumb($nav){
        $crumb_nav[] = array('name'=>'首页','link'=>site_link('default','index'));
        $crumb_nav = array_merge($crumb_nav,$nav);

        $this->output->set('crumb_nav',$crumb_nav);
        $crumb = loader::view('block/crumb',false);

        $this->output->set('page_crumb',$crumb);
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