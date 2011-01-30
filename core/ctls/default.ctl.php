<?php

class default_ctl extends pagecore{
    
    function index($par=array()){
        //echo 'sdf';
        //print_r($par);
        //loader::view('album');
        $page = isset($par['page'])?$par['page']:1;
        
        $pageurl='index.php?page=[#page#]';
        
        $mdl_picture = & loader::model('picture');
        $piclist = $mdl_picture->get_all_pic($page,0,'time_desc',0,true);
        //print_r($piclist['ls']);
        $this->output->set('piclist',$piclist['ls']);
        //$this->output->set('pageset',pageshow($piclist['total'],$piclist['start'],$pageurl));
        $this->output->set('pageset','分页');
        $this->output->set('total_num',$piclist['count']);
        $this->output->set('current_nav','index');
        //$this->view->display('newphotos');
        
        loader::view('newphotos');
    }
}