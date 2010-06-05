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
        $album = intval($_GET['album']);
        $row = $this->mdl_picture->get_one_pic($id);
        if(!$row){
            showInfo('您要查看的图片不存在！',false);
        }
        $imginfo = GetImageInfo(imgSrc($row['path']));
        if($imginfo){
            $nimginfo['相机品牌'] = $imginfo['制造商'];
            $nimginfo['相机型号'] = $imginfo['型号'];
            $nimginfo['曝光模式'] = $imginfo['曝光模式'];
            $nimginfo['闪光灯'] = $imginfo['闪光灯'];
            $nimginfo['焦距'] = $imginfo['焦距'];
            $nimginfo['光圈'] = $imginfo['快门光圈'];
            $nimginfo['快门速度'] = $imginfo['曝光时间'].' sec';
            $nimginfo['ISO感光度'] = $imginfo['ISO感光度'];
            $nimginfo['白平衡'] = $imginfo['白平衡'];
            $nimginfo['曝光补偿'] = $imginfo['曝光补偿'];
            $nimginfo['拍摄时间'] = $imginfo['拍摄时间']; 
        }else{
            $nimginfo = false;
        }
        $this->output->set('pic',$row);
        $this->output->set('album',$album);
        $this->output->set('pre_pic',$this->mdl_picture->get_pre_pic($id,$album));
        $this->output->set('next_pic',$this->mdl_picture->get_next_pic($id,$album));
        $this->output->set('imgexif',$nimginfo);
        $this->output->set('album_name',$this->mdl_album->get_album_name($row['album']));
        $this->view->display('admin/viewphoto.php');
    }
    
    function resize(){
        //sleep(2);
        $size = $_GET['size'];
        $id = $_GET['id'];
        $pic = $this->mdl_picture->get_one_pic($id);
        if(!in_array($size,array('small','square','medium','big','thumb')) || !$pic){
            header('Location: '.imgSrc('nopic.jpg'));
            exit;
        }
        
        $square = false;
        if($size=='small'){
            $width = '240';
            $height = '240';
        }elseif($size=='thumb'){
            $width = '110';
            $height = '110';
        }elseif($size=='square'){
            $width = '75';
            $height = '75';
            $square = true;
        }elseif($size=='medium'){
            $width = '500';
            $height = '500';
        }elseif($size=='big'){
            $width = '700';
            $height = '700';
        }
        $namearr = explode('.',$pic['path']);
        $resized_img = $namearr[0].'_'.$size.'.'.$namearr[1];
        if(file_exists(DATADIR.$resized_img)){
            header('Location: '.imgSrc($resized_img));
            exit;
        }
        
        ResizeImage(DATADIR.$pic['path'],$width,$height,DATADIR.$resized_img);
        header('Location: '.imgSrc($resized_img));
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