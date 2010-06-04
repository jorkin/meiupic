<?php
/**
 * $Id: album.php 13 2010-05-29 16:42:01Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends pagefactory{
    
    function controller(){
        parent::pagefactory();
        $this->mdl_album = & load_model('album');
        $this->mdl_picture = & load_model('picture');
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $this->output->set('current_nav','album');
    }
    
    function index(){
        
        $page = $_GET['page'];
        if(!$page){
            $page = 1;
        }
        
        $albums = $this->mdl_album->get_all_album($page);
        
        $pageurl='index.php?page=[#page#]';
        if($albums['ls']){
            foreach($albums['ls'] as $k=>$v){
                if(!$v['cover']){
                    $albums['ls'][$k]['cover'] = $this->mdl_album->get_cover($v['id']);
                }
            }
        }
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',pageshow($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->view->display('admin/album.php');
    }
    
    function gallery(){
        $album = intval($_GET['album']);
        if($album > 0){
            $title = $this->mdl_album->get_album_name($album);
        }else{
            $title = '所有图片';
        }
        echo '<?xml version="1.0" encoding="UTF-8"?>
<simpleviewergallery 
 title="'.$title.'"
 textColor="FFFFFF"
 frameColor="FFFFFF"
 thumbPosition="BOTTOM"
 galleryStyle="MODERN"
 thumbColumns="10"
 thumbRows="1"
 showOpenButton="TRUE"
 showFullscreenButton="TRUE"
 frameWidth="10"
 maxImageWidth="1280"
 maxImageHeight="1024"
 imagePath="data/"
 thumbPath="data/"
 useFlickr="false"
 flickrUserName=""
 flickrTags=""
 languageCode="AUTO"
 languageList="">'."\n";
        $pictures = $this->mdl_picture->get_all_pic(NULL,$album);
        if(is_array($pictures)){
            foreach($pictures as $v){
                echo '    <image imageURL="'.imgSrc($v['path']).'" thumbURL="'.imgSrc($v['thumb']).'" linkURL="" linkTarget="">
        <caption><![CDATA['.$v['name'].']]></caption>	
    </image>'."\n";
            }
        }

        echo '</simpleviewergallery>';
    }
}