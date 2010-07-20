<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends adminpage{
    
    function controller(){
        parent::adminpage();
        $this->mdl_album = & load_model('album');
        $this->mdl_picture = & load_model('picture');
        if(!$this->auth->isLogedin() && IN_ACT!='resize'){
            redirect_c('default','login');
        }
        $this->output->set('current_nav','album');
    }
    
    function view(){
        $id = intval($this->getGet('id',0));
        $album = intval($this->getGet('album',0));
        $row = $this->mdl_picture->get_one_pic($id);
        if(!$row){
            showInfo('您要查看的图片不存在！',false);
        }
        include_once(LIBDIR.'image.class.php');
        $imgobj = new Image();
        $imginfo = $imgobj->GetImageInfo(ROOTDIR.mkImgLink($row['dir'],$row['pickey'],$row['ext'],'orig'));

        $this->output->set('pic',$row);
        $this->output->set('album',$album);
        $this->output->set('pre_pic',$this->mdl_picture->get_pre_pic($id,$album));
        $this->output->set('next_pic',$this->mdl_picture->get_next_pic($id,$album));
        $this->output->set('imgexif',$imginfo);
        $this->output->set('album_name',$this->mdl_album->get_album_name($row['album']));
        $this->view->display('admin/viewphoto.php');
    }
    
    function bat(){
        $action = $this->getPost('do_action');
        $pics = $this->getPost('picture');
        $referfunc = $this->getGet('referf');
        $referpage = $this->getGet('referp');
        $album_id = $this->getGet('album',0);
        if(!is_array($pics)){
            if($referfunc=='default'){
                header('Location: admin.php?act=all&page='.$referpage.'&flag=1');
            }elseif($referfunc=='album'){
                header('Location: admin.php?ctl=album&act=photos&album='.$album.'&page='.$referpage.'&flag=1');
            }
            exit;
        }
        if($action == 'delete'){
            $upload =& load_model('upload');
            foreach($pics as $v){
                $row = $this->mdl_picture->get_one_pic($v);
                if($row){
                    $upload->delpicfile($row['dir'],$row['pickey'],$row['ext']);
                    $this->mdl_album->remove_cover($v);
                    $this->mdl_picture->del_pic($v);
                }
            }
        }elseif($action == 'move'){
            $album = intval($this->getPost('albums'));
            if(!$album || $album == '-1'){
                 header('Location: admin.php?ctl=album&act=photos&album='.$album_id.'&page='.$referpage.'&flag=2');
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
            header('Location: admin.php?act=all&page='.$referpage.'&flag=3');
        }elseif($referfunc=='album'){
            header('Location: admin.php?ctl=album&act=photos&album='.$album_id.'&page='.$referpage.'&flag=3');
        }
        exit;
    }
    
    function gallery(){
        $album = intval($this->getGet('album'));
        if($album > 0){
            $title = $this->mdl_album->get_album_name($album);
        }else{
            $title = '所有图片';
        }
        
        @ob_clean();
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
        $pictures = $this->mdl_picture->get_all_pic(NULL,$album,'id asc',$this->setting['gallery_limit']);
        if(is_array($pictures)){
            foreach($pictures as $v){
                echo '    <image imageURL="'.mkImgLink($v['dir'],$v['pickey'],$v['ext'],'big').'" thumbURL="'.mkImgLink($v['dir'],$v['pickey'],$v['ext'],'square').'" linkURL="'.mkImgLink($v['dir'],$v['pickey'],$v['ext'],'orig').'" linkTarget="">
        <caption><![CDATA['.$v['name'].']]></caption>	
    </image>'."\n";
            }
        }

        echo '</simpleviewergallery>';
    }

    function resize(){
        $album = intval($this->getGet('album'));
        set_time_limit(0);
        ignore_user_abort(true);
        if(!$this->setting['demand_resize']){
            $this->mdl_upload = & load_model('upload');
            $tmppics = $this->mdl_picture->get_tmp_pic();
            if($tmppics){
                foreach($tmppics as $v){
                    $this->mdl_upload->generatepic($v['dir'],$v['pickey'],$v['ext']);
                    $this->mdl_picture->update_pic($v['id'],$v['name']);
                }
            }
        }
        echo 'alert("图片处理完成！");window.location.href="admin.php?ctl=album&act=photos&album='.$album.'";';
    }
}