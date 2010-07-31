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
        $this->mdl_album = & load_model('album');
        
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
        
        $this->output->set('current_nav','index');
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',pageshow($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        
        $site_title = $this->output->get('site_title').' - 相册列表';
        $this->output->set('site_title',$site_title);
        
        $this->view->display('front/album.php');
    }
    
    function newphotos(){
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        
        $pageurl='index.php?page=[#page#]';
        
        $mdl_picture = & load_model('picture');
        $piclist = $mdl_picture->get_all_pic($page,0,'time_desc',0,true);
        $this->output->set('piclist',$piclist['ls']);
        $this->output->set('pageset',pageshow($piclist['total'],$piclist['start'],$pageurl));
        $this->output->set('total_num',$piclist['count']);
        $this->output->set('current_nav','newphotos');
        $this->view->display('front/newphotos.php');
    }
    
    function hotphotos(){
        $this->output->set('current_nav','hotphotos');
        
        $mdl_picture = & load_model('picture');
        $piclist = $mdl_picture->get_all_pic(NULL,0,'hot',10,true);
        $this->output->set('piclist',$piclist);
        $site_title = $this->output->get('site_title').' - 热门图片';
        $this->output->set('site_title',$site_title);
        $this->view->display('front/hotphotos.php');
    }
    
}