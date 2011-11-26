<?php

class category_ctl extends pagecore{
    function _init(){
        $this->mdl_cate = & loader::model('category');
    }
    
    function index(){
        $categorylist = $this->mdl_cate->get_flat_category();
        $this->output->set('categorylist',$categorylist);

        //面包屑
        $crumb_nav = array();
        $crumb_nav[] = array('name'=>lang('category_list'));

        $this->page_crumb($crumb_nav);

        $page_title =  lang('all_category').' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        $this->page_init($page_title,$page_keywords,$page_description);
        
        $this->render();
    }

    function create(){
        need_login('ajax_page');
        $from = $this->getGet('from');
        if($from){
            $from = base64_decode($from);
        }
        $this->output->set('pid',$this->getGet('pid'));
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
        $data['sort'] = intval($this->getPost('sort'));
        if($data['name'] == ''){
            form_ajax_failed('text',lang('category_name_empty'));
        }
        
        if(($id = $this->mdl_cate->save($data)) == true ){
            if($this->getPost('add_nav')){
                $nav_data['type'] = 1;
                $nav_data['name'] = $data['name'];
                $nav_data['url'] = site_link('albums','index',array('cate'=>$id));
                $nav_data['sort'] = 100;
                $nav_data['enable'] = 1;

                $mdl_nav =& loader::model('nav');
                $mdl_nav->save($nav_data);
            }

            if($from){
                form_ajax_success('box',lang('create_category_succ').'<script>setTimeout(function(){ Mui.box.show("'.$from.'",true); },1000)</script>');
            }else{
                form_ajax_success('box',lang('create_category_succ'),null,0.5,$_SERVER['HTTP_REFERER']);
            }
        }else{
            form_ajax_failed('text',lang('create_category_fail'));
        }
    }

    function edit(){
        need_login('ajax_page');
        $id = $this->getGet('id');

        $info = $this->mdl_cate->get_info($id);

        $this->output->set('info',$info);
        $cate_list = $this->mdl_cate->get_flat_category();
        $this->output->set('cate_list',$cate_list);
        $this->render();        
    }

    function update(){
        need_login('ajax');
        $id = $this->getGet('id');
        $data['par_id'] = $this->getPost('par_id') > 0 ? $this->getPost('par_id'):0;
        $data['name'] = $this->getPost('cate_name');
        $data['sort'] = intval($this->getPost('sort'));

        if($this->mdl_cate->update(intval($id),$data)){
            if($this->getPost('add_nav')){
                $nav_data['type'] = 1;
                $nav_data['name'] = $data['name'];
                $nav_data['url'] = site_link('albums','index',array('cate'=>$id));
                $nav_data['sort'] = 100;
                $nav_data['enable'] = 1;

                $mdl_nav =& loader::model('nav');
                $mdl_nav->save($nav_data);
            }
            
            form_ajax_success('box',lang('edit_category_succ'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            form_ajax_failed('text',lang('edit_category_fail'));
        }
    }

    function confirm_delete(){
        need_login('ajax_page');

        $id = $this->getGet('id');
        $this->output->set('id',$id);
        $data = $this->mdl_cate->get_info($id);
        $this->output->set('cate_name',$data['name']);
        $this->render();
    }

    function delete(){
        need_login('ajax_page');
        $id = $this->getGet('id');
        
        if($this->mdl_cate->delete($id)){
            $mdl_album =& Loader::model('album');
            $mdl_album->set_default_cate($id);
            ajax_box(lang('delete_cate_succ'),null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box(lang('delete_cate_fail'));
        }
    }
}