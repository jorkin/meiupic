<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends pagefactory{
    var $thumb_width = 110;
    var $thumb_height = 150;

    function controller(){
        parent::pagefactory();
        $this->mdl_album = & load_model('album');
        $this->mdl_upload = & load_model('upload');
        $this->mdl_picture = & load_model('picture');
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $this->output->set('current_nav','upload');
    }
    
    function index(){
        $this->output->set('albums_list',$this->mdl_album->get_all_album());
        $this->view->display('admin/upload_step1.php');
    }
    
    function step2(){
        $album_id = $_GET['album_id'];
        if($album_id){
            $this->output->set('album_id',$_GET['album_id']);
            $this->output->set('upload_runtimes',$this->setting['upload_runtimes']);
            $this->output->set('open_pre_resize',$this->setting['open_pre_resize']);
            $this->output->set('resize_img_width',$this->setting['resize_img_width']);
            $this->output->set('resize_img_height',$this->setting['resize_img_height']);
            $this->output->set('extension_allow',$this->setting['extension_allow']);
            $this->output->set('resize_quality',$this->setting['resize_quality']);
            $this->view->display('admin/upload_step2.php');
        }else{
            showInfo('非法参数:album_id不能为空！',false);
        }
    }
    
    function step2_1(){
        $album_id = $_GET['album_id'];
        if($album_id){
            $this->output->set('album_id',$_GET['album_id']);
            $this->view->display('admin/upload_step2_1.php');
        }else{
            showInfo('非法参数:album_id不能为空！',false);
        }
    }
    
    function process(){
        $this->mdl_upload->plupload();
    }
    
    function dopicupload(){
        $date = get_updir_name($this->setting['imgdir_type']);
        if(!is_dir(DATADIR.$date)){
            @mkdir(DATADIR.$date);
            @chmod(DATADIR.$date,0755);
        }
        $empty_num = 0;
        foreach($_FILES['imgs']['name'] as $k=>$upfile){
            $tmpfile = $_FILES['imgs']['tmp_name'][$k];
            if (!empty($upfile)) {
                $filename = $upfile;
                $fileext = strtolower(end(explode('.',$filename)));
                if(!in_array($fileext,explode(',',$this->setting['extension_allow']))){
                    showInfo('不支持的图片格式！',false);
                    exit;
                }
                if($_FILES['imgs']['size'][$k] > $this->setting['size_allow']){
                    showInfo('上传图片过大！不得大于'.$this->setting['size_allow'].'字节！',false);
                    exit;
                }
                $key = md5(str_replace('.','',microtime(true)));
                $imgpath = $date.'/'.$key.'.'.$fileext;
                $realpath = DATADIR.$imgpath;
                $thumbpath = $date.'/'.$name.'_thumb.jpg';
                $thumbrealpath = DATADIR.$thumbpath;
                
                if(@move_uploaded_file($tmpfile,$realpath)){
                    /*include_once(LIBDIR.'image.class.php');
                    $imgobj = new Image();
                    $imgobj->load($realpath);
                    $imgobj->setQuality(90);
                    $imgobj->resizeScale($this->thumb_width,$this->thumb_height);
                    $imgobj->save($thumbrealpath);*/
                    @chmod($realpath,0755);
                    $this->mdl_picture->insert_pic(array('album'=>$_GET['album'],
                                                    'name'=>$filename,
                                                    'dir'=>$date,
                                                    'key'=>$key,
                                                    'ext'=>$fileext));
                }else{
                    showInfo('文件上传失败！',false);
                    exit;
                }
            }else{
                $empty_num++;
            }
        }
        if($empty_num == count($_FILES['imgs']['name'])){
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<script> alert("您没有选择图片上传，请重新上传!");history.back();</script>';
            exit;
        }
        header('Location: index.php?ctl=upload&act=doupload&album='.$_GET['album']);
    }
    function doupload(){
        $this->_save_and_resize();
        
        $pics = $this->mdl_picture->get_tmp_pic();
        $this->output->set('uploaded_pics',$pics);
        $this->output->set('album',$_GET['album']);
        $this->view->display('admin/upload_step3.php');
    }
    
    function _save_and_resize(){
        $tmp_dir = where_is_tmp();
        $targetDir =  $tmp_dir. DIRECTORY_SEPARATOR . "plupload";
        
        $date = get_updir_name($this->setting['imgdir_type']);
        if(!is_dir(DATADIR.$date)){
            @mkdir(DATADIR.$date);
        }
        $files_count = intval($_POST['flash_uploader_count']);
        for($i=0;$i<$files_count;$i++){
            $tmpfile = $targetDir . DIRECTORY_SEPARATOR . $_POST["flash_uploader_{$i}_tmpname"];
            $filename = $_POST["flash_uploader_{$i}_name"];
            $status =  $_POST["flash_uploader_{$i}_status"];
            $fileext = strtolower(end(explode('.',$filename)));
            $key = md5(str_replace('.','',microtime(true)));
            $imgpath = $date.'/'.$key.'.'.$fileext;
            $realpath = DATADIR.$imgpath;
            $thumbpath = $date.'/'.$key.'_thumb.jpg';
            $thumbrealpath = DATADIR.$thumbpath;
            if($status == 'done' && file_exists($tmpfile)){
                if(@copy($tmpfile,$realpath)){
                    /*include_once(LIBDIR.'image.class.php');
                    $imgobj = new Image();
                    $imgobj->load($realpath);
                    $imgobj->setQuality(90);
                    $imgobj->resizeScale($this->thumb_width,$this->thumb_height);
                    $imgobj->save($thumbrealpath);*/
                    @chmod($realpath,0755);
                    $this->mdl_picture->insert_pic(array('album'=>$_GET['album'],
                                                    'name'=>$filename,
                                                    'dir'=>$date,
                                                    'key'=>$key,
                                                    'ext'=>$fileext));
                }
            }
        }
    }
    
    function saveimgname(){
        $imgname = $_POST['imgname'];
        $album = $_GET['album'];
        if($imgname){
            foreach($imgname as $k=>$v){
                $this->mdl_picture->update_pic(intval($k),$v);
            }
        }
        
        redirect('index.php?ctl=album&act=photos&album='.$album);
    }
    
    function reupload(){
        $id = intval($_GET['id']);
    
        $row = $this->mdl_picture->get_one_pic($id);
        if(!$row){
            echo '<script> top.reupload_alert("此照片不存在或已被删除!");</script>';
            exit;
        }
        if(empty($_FILES['imgs']['name'])){
            echo '<script> top.reupload_alert("请先选择要上传的图片!");</script>';
            exit;
        }
        
        $filename = $_FILES['imgs']['name'];
        $tmpfile = $_FILES['imgs']['tmp_name'];
        $fileext = strtolower(end(explode('.',$filename)));
        $oldext = $row['ext'];
        if($fileext != $oldext){
            echo '<script> top.reupload_alert("上传的文件的格式必须跟原图片一致!");</script>';
            exit;
        }
        if($_FILES['imgs']['size'] > $this->setting['size_allow']){
            echo '<script> top.reupload_alert("上传图片过大！不得大于'.$this->setting['size_allow'].'字节！");</script>';
            exit;
        }
        $realpath = ROOTDIR.mkImgLink($row['dir'],$row['key'],$row['ext'],'orig');
        $thumbrealpath = ROOTDIR.mkImgLink($row['dir'],$row['key'],$row['ext'],'thumb');
        
        $this->mdl_upload->delpicfile($row['dir'],$row['key'],$row['ext']);
        if(@move_uploaded_file($tmpfile,$realpath)){
            /*include_once(LIBDIR.'image.class.php');
            $imgobj = new Image();
            $imgobj->load($realpath);
            $imgobj->setQuality(90);
            $imgobj->resizeScale($this->thumb_width,$this->thumb_height);
            $imgobj->save($thumbrealpath);*/
            @chmod($realpath,0755);
            echo '<script> top.reupload_ok("'.$id.'","'.mkImgLink($row['dir'],$row['key'],$row['ext'],'orig').'","'.mkImgLink($row['dir'],$row['key'],$row['ext'],'thumb').'");</script>';
        }else{
            echo '<script> top.reupload_alert("文件上传失败!");</script>';
        }
    }
}