<?php

class default_ctl extends pagecore{
    
    function index(){

        $index_tpl = ROOTDIR.TPLDIR.DIRECTORY_SEPARATOR.'index.htm';
        if(file_exists($index_tpl)){
            $crumb_nav = array();
            //$crumb_nav[] = array('name'=>lang('category_list'));

            $this->page_crumb($crumb_nav);

            $page_title =  lang('all_category').' - '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');
            $this->page_init($page_title,$page_keywords,$page_description);

            loader::view('index');
        }else{
            $url = site_link('albums');
            redirect($url);
        }
    }
}