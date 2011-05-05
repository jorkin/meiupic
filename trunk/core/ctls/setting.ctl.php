<?php

class setting_ctl extends pagecore{
    
    function _init(){
        //$this->plugin =& loader::lib('plugin');
        //$this->mdl_album = & loader::model('album');
    }
    
    function index(){
        need_login('page');
        
        $site = $this->setting->get_conf('site');
        $site['description'] = safe_invert($site['description']);
        $this->output->set('site',$site);
        $this->output->set('enable_comment',$this->setting->get_conf('system.enable_comment'));
        
        $page_title = '基本设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function save_basic(){
        need_login('ajax');
        
        $site = $this->getPost('site');
        $site['title'] = safe_convert($site['title']);
        $site['url'] = safe_convert($site['url']);
        $site['keywords'] = safe_convert($site['keywords']);
        $site['description'] = safe_convert($site['description']);
        
        if($site['title'] == ''){
            ajax_box_failed('站点名称不能为空！');
        }
        if($site['url'] == ''){
            ajax_box_failed('相册URL不能为空！');
        }
        $this->setting->set_conf('site.title',$site['title']);
        $this->setting->set_conf('site.url',$site['url']);
        $this->setting->set_conf('site.keywords',$site['keywords']);
        $this->setting->set_conf('site.description',$site['description']);
        if($this->getPost('enable_comment')){
            $this->setting->set_conf('system.enable_comment',true);
        }else{
            $this->setting->set_conf('system.enable_comment',false);
        }
        ajax_box_success('保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function upload(){
        $page_title = '上传设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
}