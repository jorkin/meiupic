<?php

class category_ctl extends pagecore{
    function _init(){
        $this->mdl_cate = & loader::model('category');
    }
    
    function index(){
        $categorylist = $this->mdl_cate->get_flat_category();

        $this->output->set('categorylist',$categorylist);

        $page_title =  '所有分类 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description);
        
        $this->render();
    }

    function create(){
        need_login('ajax_page');
        $from = $this->getGet('from');
        $this->output->set('from',$from);
        
        $cate_list = $this->mdl_cate->get_flat_category();
        $this->output->set('cate_list',$cate_list);
        $this->render();
    }
    
    function save(){
        need_login('ajax');
        $from = $this->getPost('from');
        
        $data['name'] = safe_convert($this->getPost('cate_name'));
        $data['par_id'] = $this->getPost('par_id');
        if($data['name'] == ''){
            form_ajax_failed('text',lang('category_name_empty'));
        }
        
        if($this->mdl_cate->save($data)){
            form_ajax_success('box','创建分类成功!'.'<script>setTimeout(function(){ Mui.box.show("'.$from.'",true); },1000)</script>');
        }else{
            form_ajax_failed('text','创建分类失败！');
        }
    }
}