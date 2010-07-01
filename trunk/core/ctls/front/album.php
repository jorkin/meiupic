<?php
/**
 * $Id: album.php 35 2010-06-28 15:58:35Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends frontpage{    
    function index(){
        $this->view->display('front/album.php');
    }
}