<?php
/**
 * $Id: default.php 22 2010-06-06 15:50:07Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends frontpage{
    
    function index(){
        $mdl_picture = & load_model('picture');
        $piclist = $mdl_picture->get_all_pic(NULL,0,'hot',9,true);
        $this->output->set('piclist',$piclist);
        $this->view->display('front/default.php');
    }
    
    function newphotos(){
        $this->view->display('front/default.php');
    }
    
}