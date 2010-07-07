<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends frontpage{
    function controller(){
        parent::frontpage();
        $this->mdl_album = & load_model('album');
    }
    
    function index(){
        
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        
        $albums = $this->mdl_album->get_all_album($page,true);
        
        $pageurl='index.php?ctl=album&page=[#page#]';
        if($albums['ls']){
            foreach($albums['ls'] as $k=>$v){
                $cover = $this->mdl_album->get_cover($v['id'],$v['cover']);
                $albums['ls'][$k]['cover'] = $cover?mkImgLink($cover['dir'],$cover['pickey'],$cover['ext'],'thumb'):'img/nopic.jpg';
            }
        }
        
        $this->output->set('current_nav','album');
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',pageshow($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->view->display('front/album.php');
    }
}