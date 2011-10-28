<?php

class tags_ctl extends pagecore{
    
    function _init(){
        $this->mdl_tag = & loader::model('tag');
    }
    
    function index(){
        $page = $this->getGet('page',1);
        $type = $this->getGet('type');
        
        $pageurl = site_link('tags','index',array('type'=>$type,'page'=>'[#page#]'));
        $this->mdl_tag->set_pageset(40);
        
        $par = array();
        if(in_array($type,array(1,2))){
            $par['type'] = $type;
        }
        $tags = $this->mdl_tag->get_all($page,$par);
        if($tags['ls']){
            foreach($tags['ls'] as $k =>$v){
                $tags['ls'][$k]['fontsize'] = $this->mdl_tag->get_fontsize($v['count']);
            }
        }
        $page_obj =& loader::lib('page');
        $pagestr = $page_obj->fetch($tags['total'],$tags['current'],$pageurl);
        $this->output->set('pagestr',$pagestr);
        $this->output->set('tag_list',$tags['ls']);
        $this->output->set('tag_type',$type);
        
        $page_title = lang('tag_list').' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description);
        
        $this->render();
    }
    
}
