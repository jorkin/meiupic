<?php

class controller extends pagefactory{
    var $thumb_width = 110;
    var $thumb_height = 150;

    function controller(){
        parent::pagefactory();
        
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $this->output->set('current_nav','upload');
    }
    
    function index(){
        $this->db->select('#albums','*');
        $this->output->set('albums_list',$this->db->getAll());
        $this->view->display('upload_step1.php');
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
            $this->view->display('upload_step2.php');
        }else{
            showInfo('非法参数:album_id不能为空！',false);
        }
    }
    
    function step2_1(){
        $album_id = $_GET['album_id'];
        if($album_id){
            $this->output->set('album_id',$_GET['album_id']);
            $this->view->display('upload_step2_1.php');
        }else{
            showInfo('非法参数:album_id不能为空！',false);
        }
    }
    
    function process(){
        header('Content-type: text/plain; charset=UTF-8');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $tmp_dir = where_is_tmp();
        $targetDir =  $tmp_dir. DIRECTORY_SEPARATOR . "plupload";

        $cleanupTargetDir = false; //移除旧的临时文件
        $maxFileAge = 60 * 60; //临时文件超时时间

        // 5 分钟的执行时间
        @set_time_limit(5 * 60);


        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);

        if (!file_exists($targetDir))
            @mkdir($targetDir);

        if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
                    @unlink($filePath);
            }
            closedir($dir);
        } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');

        // 查看 header信息: content type
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                    fclose($out);
                    unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
    function dopicupload(){
        $date = get_updir_name($this->setting['imgdir_type']);
        if(!is_dir(DATADIR.$date)){
            @mkdir(DATADIR.$date);
        }
        
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
                $name = md5(str_replace('.','',microtime(true)));
                $imgpath = $date.'/'.$name.'.'.$fileext;
                $realpath = DATADIR.$imgpath;
                $thumbpath = $date.'/'.$name.'_thumb.jpg';
                $thumbrealpath = DATADIR.$thumbpath;
                
                if(@move_uploaded_file($tmpfile,$realpath)){
                    ResizeImage($realpath,$this->thumb_width,$this->thumb_height,$thumbrealpath);
                    $this->db->insert('#imgs',array('album'=>$_GET['album'],
                                                    'name'=>$filename,
                                                    'path'=>$imgpath,
                                                    'thumb'=>$thumbpath));
                    $this->db->query();
                }else{
                    showInfo('文件上传失败！',false);
                    exit;
                }
            }
        }
        header('Location: index.php?ctl=upload&act=doupload&album='.$_GET['album']);
    }
    function doupload(){
        $this->_save_and_resize();
        
        $this->db->select('#imgs','*','status=0','id asc');
        $pics = $this->db->getAll();
        $this->output->set('uploaded_pics',$pics);
        $this->output->set('album',$_GET['album']);
        $this->view->display('upload_step3.php');
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
            $name = md5(str_replace('.','',microtime(true)));
            $imgpath = $date.'/'.$name.'.'.$fileext;
            $realpath = DATADIR.$imgpath;
            $thumbpath = $date.'/'.$name.'_thumb.jpg';
            $thumbrealpath = DATADIR.$thumbpath;
            if($status == 'done' && file_exists($tmpfile)){
                if(@copy($tmpfile,$realpath)){
                    ResizeImage($realpath,$this->thumb_width,$this->thumb_height,$thumbrealpath);
                    $this->db->insert('#imgs',array('album'=>$_GET['album'],
                                                    'name'=>$filename,
                                                    'path'=>$imgpath,
                                                    'thumb'=>$thumbpath));
                    $this->db->query();
                }
            }
        }
    }
    
    function saveimgname(){
        $imgname = $_POST['imgname'];
        $album = $_GET['album'];
        if($imgname){
            foreach($imgname as $k=>$v){
                $this->db->update('#imgs','id='.intval($k),array('name'=>$v,'status'=>'1'));
                $this->db->query();
            }
        }
        
        redirect('index.php?ctl=album&act=photos&album='.$album);
    }
    
    function ajax_create_album(){
        $album_name = $_POST['album_name'];
        $this->db->insert('#albums',array('name'=>$album_name));
        if($this->db->query()){
            $this->db->select('#albums','*');
            $list = $this->db->getAssoc();
            echo json_encode(array('ret'=>true,'list'=>$list));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'创建相册失败！'));
        }
    }
    
    function reupload(){
        $id = intval($_GET['id']);
    
        $this->db->select('#imgs','*','id='.$id);
        $row = $this->db->getRow();
        if(!$row){
            echo '<script> top.reupload_alert("此照片不存在或已被删除!");</script>';
            exit;
        }
        if(empty($_FILES['imgs']['name'])){
            echo '<script> top.reupload_alert("请先选择要上传的图片!");</script>';
            exit;
        }
        $date = get_updir_name($this->setting['imgdir_type']);
        if(!is_dir(DATADIR.$date)){
            @mkdir(DATADIR.$date);
        }
        $filename = $_FILES['imgs']['name'];
        $tmpfile = $_FILES['imgs']['tmp_name'];
        $fileext = strtolower(end(explode('.',$filename)));
        $oldext = strtolower(end(explode('.',$row['path'])));
        if($fileext != $oldext){
            echo '<script> top.reupload_alert("上传的文件的格式必须跟原图片一致!");</script>';
            exit;
        }
        if($_FILES['imgs']['size'] > $this->setting['size_allow']){
            echo '<script> top.reupload_alert("上传图片过大！不得大于'.$this->setting['size_allow'].'字节！");</script>';
            exit;
        }
        $realpath = DATADIR.$row['path'];
        $thumbrealpath = DATADIR.$row['thumb'];
        
        if(@move_uploaded_file($tmpfile,$realpath)){
            ResizeImage($realpath,$this->thumb_width,$this->thumb_height,$thumbrealpath);
            echo '<script> top.reupload_ok("'.$id.'","'.$this->setting['imgdir'].'/'.$row['path'].'","'.$this->setting['imgdir'].'/'.$row['thumb'].'");</script>';
        }else{
            echo '<script> top.reupload_alert("文件上传失败!");</script>';
        }
    }
}