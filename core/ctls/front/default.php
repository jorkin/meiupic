<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends frontpage{
    
    function index(){
        $this->output->set('current_nav','index');
        
        $mdl_picture = & load_model('picture');
        $piclist = $mdl_picture->get_all_pic(NULL,0,'hot',9,true);
        $this->output->set('piclist',$piclist);
        $this->view->display('front/default.php');
    }
    
    function newphotos(){
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        
        $pageurl='index.php?act=newphotos&page=[#page#]';
        
        $mdl_picture = & load_model('picture');
        $piclist = $mdl_picture->get_all_pic($page,0,'time_desc',0,true);
        $this->output->set('piclist',$piclist['ls']);
        $this->output->set('pageset',pageshow($piclist['total'],$piclist['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->output->set('current_nav','newphotos');
        $this->view->display('front/newphotos.php');
    }
    
}