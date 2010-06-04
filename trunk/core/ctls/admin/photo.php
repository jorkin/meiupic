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
    
    function view(){
        $id = intval($_GET['id']);
        
        $row = $this->mdl_picture->get_one_pic($id);
        if(!$row){
            showInfo(false,'您要查看的图片不存在！');
        }
        
        $this->output->set('pic',$row);
        $this->output->set('album_name',$this->mdl_album->get_album_name($row['album']));
        $this->view->display('admin/viewphoto.php');
    }
    
    function bat(){
        $action = $_POST['do_action'];
        $pics = $_POST['picture'];
        $referfunc = $_GET['referf'];
        $referpage = $_GET['referp'];
        $album_id = isset($_GET['album'])?$_GET['album']:0;
        if(!is_array($pics)){
            if($referfunc=='default'){
                header('Location: index.php?page='.$referpage.'&flag=1');
            }elseif($referfunc=='album'){
                header('Location: index.php?ctl=album&act=photos&album='.$album.'&page='.$referpage.'&flag=1');
            }
            exit;
        }
        if($action == 'delete'){
            foreach($pics as $v){
                $row = $this->mdl_picture->get_one_pic($v);
                if($row){
                    @unlink(DATADIR.$row['path']);
                    @unlink(DATADIR.$row['thumb']);

                    $this->mdl_album->remove_cover($v);
                    $this->mdl_picture->del_pic($v);
                }
            }
        }elseif($action == 'move'){
            $album = intval($_POST['albums']);
            if(!$album || $album == '-1'){
                 header('Location: index.php?ctl=album&act=photos&album='.$album_id.'&page='.$referpage.'&flag=2');
                 exit;
            }
            
            foreach($pics as $v){
                $row = $this->mdl_picture->get_one_pic($v);
                if($row){
                    $this->mdl_album->remove_cover($v);
                    $this->mdl_picture->update_pic($v,$row['name'],$album);
                }
            }
        }
        if($referfunc=='default'){
            header('Location: index.php?page='.$referpage.'&flag=3');
        }elseif($referfunc=='album'){
            header('Location: index.php?ctl=album&act=photos&album='.$album_id.'&page='.$referpage.'&flag=3');
        }
        exit;
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