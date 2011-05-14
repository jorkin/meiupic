<?php

class upload_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo =& loader::model('photo');
    }
    
    function index(){
        need_login('page');
        
        $album_id = $this->getGet('aid');
        
        $this->output->set('album_id',$album_id);
        
        $this->output->set('album_menu',$this->plugin->filter('album_menu',''));
        $this->output->set('albums_list',$this->mdl_album->get_kv());
        $this->output->set('upload_setting',$this->setting->get_conf('upload'));
        
        $supportType =  loader::lib('image')->supportType();
        $this->output->set('support_type',implode(',',$supportType));
        
        $page_title = '上传照片 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function normal(){
        need_login('page');
        
        $album_id = $this->getGet('aid');
        $this->output->set('album_id',$album_id);
        
        $this->output->set('album_menu',$this->plugin->filter('album_menu',''));
        $this->output->set('albums_list',$this->mdl_album->get_kv());
        
        $page_title = '上传照片 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function process(){
        set_time_limit(0);
        $type = $this->getGet('t');
        if($type == 'save_tmp'){
            if(!loader::model('user')->loggedin()){
                $return = array(
                    'jsonrpc'=>'2.0',
                    'error'=> array( 
                        'code'=>100,
                        'message'=>'请先登录后上传'
                     ),
                     'id'=>'id');
                 echo loader::lib('json')->encode($return);
                 exit;
            }
            
            $chunk = $this->getRequest('chunk',0);
            $chunks = $this->getRequest('chunks',0);
            $filename = $this->getRequest('name','');
            $target_dir = ROOTDIR.'cache'.DIRECTORY_SEPARATOR.'tmp';
        
            $status = loader::model('storage')->upload_multi($target_dir,
                                            $chunk,$chunks,$filename,true);
            switch($status){
                case 100:
                $return = array(
                    'jsonrpc'=>'2.0',
                    'error'=> array( 
                        'code'=>$status,
                        'message'=>lang('Failed to open temp directory.')
                     ),
                     'id'=>'id');
                break;
                case 101:
                $return = array(
                    'jsonrpc'=>'2.0',
                    'error'=>array(
                         'code'=>$status,
                         'message'=>lang('Failed to open input stream.')
                     ),
                     'id'=>'id');
                break;
                case 102:
                $return = array(
                    'jsonrpc'=>'2.0',
                    'error'=>array(
                        'code'=>$status,
                        'message'=>lang('Failed to open output stream.')
                     ),
                     'id'=>'id');
                break;
                case 0:
                $return = array('jsonrpc'=>'2.0','result'=>null,'id'=>'id');
            }
            echo loader::lib('json')->encode($return);
            exit;
        }elseif($type == 'normal'){
            //todo: normally save file
        }
    }
    
    function save(){
        set_time_limit(0);
        ignore_user_abort(true);
        $type = $this->getGet('t');
        $album_id = $this->getPost('album_id');
        
        if($type == 'multi'){
            need_login('ajax');
            
            if(!$album_id){
                form_ajax_failed('box','请先选择相册！');
            }
            
            $target_dir = ROOTDIR.'cache'.DIRECTORY_SEPARATOR.'tmp';
            $imglib =& loader::lib('image');
            $exiflib =& loader::lib('exif');
            $media_dirname = 'data/'.date('Ymd');
            $thumb_dirname = 'data/thumb/'.date('Ymd');
            if(!file_exists(ROOTDIR.$media_dirname)){
                @mkdir(ROOTDIR.$media_dirname);
            }
            if(!file_exists(ROOTDIR.'data/thumb')){
                @mkdir(ROOTDIR.'data/thumb');
            }
            if(!file_exists(ROOTDIR.$thumb_dirname)){
                @mkdir(ROOTDIR.$thumb_dirname);
            }
            $files_count = intval($this->getPost('muilti_uploader_count'));
            for($i=0;$i<$files_count;$i++){
                $tmpfile = $target_dir . DIRECTORY_SEPARATOR . $this->getPost("muilti_uploader_{$i}_tmpname");
                $filename = $this->getPost("muilti_uploader_{$i}_name");
                $status =  $this->getPost("muilti_uploader_{$i}_status");
                $fileext = file_ext($filename);
                $key = str_replace('.','',microtime(true));
                
                $realpath = ROOTDIR.$media_dirname.'/'.$key.'.'.$fileext;
                if(file_exists($realpath)){
                    $key = $key.'_1';
                    $realpath = ROOTDIR.$media_dirname.'/'.$key.'.'.$fileext;
                }
                if($status == 'done' && file_exists($tmpfile)){
                    if(@copy($tmpfile,$realpath)){
                        $arr['album_id'] = $album_id;
                        $arr['path'] = $media_dirname.'/'.$key.'.'.$fileext;
                        $arr['thumb'] = $thumb_dirname.'/'.$key.'.'.$fileext;
                        $arr['name'] = $filename;
                        $arr['create_time'] = time();
                        $arr['create_y'] = date('Y');
                        $arr['create_m'] = date('n');
                        $arr['create_d'] = date('j');
                        $imglib->load($realpath);
                        //resize image to thumb: 180*180 
                        $arr['width'] = $imglib->getWidth();
                        $arr['height'] = $imglib->getHeight();
                        if( $imglib->getExtension() == 'jpg'){
                            $exif = $exiflib->get_exif($realpath);
                            if($exif){
                                $arr['exif'] = serialize($exif);
                                $taken_time = strtotime($exif['DateTimeOriginal']);
                                $arr['taken_time'] = $taken_time;
                                $arr['taken_y'] = date('Y',$taken_time);
                                $arr['taken_m'] = date('n',$taken_time);
                                $arr['taken_d'] = date('j',$taken_time);
                            }
                        }
                        $water_setting = $this->setting->get_conf('watermark');
                        if($water_setting['type'] == 1){
                            $water_setting['water_mark_type'] = 'image';
                            $imglib->waterMarkSetting($water_setting);
                            $imglib->waterMark();
                            $imglib->save($realpath);
                        }elseif($water_setting['type'] == 2){
                            $water_setting['water_mark_type'] = 'font';
                            $water_setting['water_mark_font'] = $water_setting['water_mark_font']?ROOTDIR.'statics/font/'.$water_setting['water_mark_font']:'';
                            $imglib->waterMarkSetting($water_setting);
                            $imglib->waterMark();
                            $imglib->save($realpath);
                        }
                        $imglib->resizeScale(180,180);
                        
                        $imglib->save(ROOTDIR.$arr['thumb']);
                        if(!$this->mdl_photo->save($arr)){
                            @unlink($realpath);
                            @unlink(ROOTDIR.$arr['thumb']);
                        }
                        @unlink($tmpfile);
                    }
                }
            }
            
            $this->mdl_album->update_photos_num($album_id);
            $this->mdl_album->check_repare_cover($album_id);
            
            $gourl = site_link('photos','index',array('aid'=>$album_id));
            form_ajax_success('box','上传照片成功！',null,1,$gourl);
        }else{
            need_login('page');
            
            $this->output->set('album_id',$album_id);

            $this->output->set('album_menu',$this->plugin->filter('album_menu',''));
            $this->output->set('albums_list',$this->mdl_album->get_kv());

            $page_title = '上传照片 - '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');

            $this->page_init($page_title,$page_keywords,$page_description);
            
            if(!$album_id){
                $this->output->set('msginfo','请先选择相册！');
                loader::view('upload/normal');
                return;
            }
            $imglib =& loader::lib('image');
            $exiflib =& loader::lib('exif');
            $media_dirname = 'data/'.date('Ymd');
            $thumb_dirname = 'data/thumb/'.date('Ymd');
            $supportType = $imglib->supportType();
            if(!file_exists(ROOTDIR.$media_dirname)){
                @mkdir(ROOTDIR.$media_dirname);
            }
            if(!file_exists(ROOTDIR.'data/thumb')){
                @mkdir(ROOTDIR.'data/thumb');
            }
            if(!file_exists(ROOTDIR.$thumb_dirname)){
                @mkdir(ROOTDIR.$thumb_dirname);
            }
            $empty_num = 0;
            $error = '';
            $allowsize = allowsize($this->setting->get_conf('upload.allow_size'));
            foreach($_FILES['imgs']['name'] as $k=>$upfile){
                
                if (!empty($upfile)) {
                    $filesize = $_FILES['imgs']['size'][$k];
                    $tmpfile = $_FILES['imgs']['tmp_name'][$k];
                    $filename = $upfile;
                    $fileext = file_ext($filename);
                    
                    if($_FILES['imgs']['error'][$k] == 1){
                        $error .= '文件'.$filename.'上传失败:文件大小超过服务器限制！<br />';
                        continue;
                    }
                    
                    if($allowsize && $filesize>$allowsize){
                        $error .= '文件'.$filename.'上传失败:大小超过用户限制！<br />';
                        continue;
                    }
                    
                    if($filesize == 0){
                        $error .= '文件'.$filename.'上传失败:请确认上传的是否为文件！<br />';
                        continue;
                    }
                    if(!in_array($fileext,$supportType)){
                        $error .= '文件'.$filename.'上传失败:不支持此格式！<br />';
                        continue;
                    }
                    
                    $key = str_replace('.','',microtime(true));
                    $realpath = ROOTDIR.$media_dirname.'/'.$key.'.'.$fileext;
                    if(file_exists($realpath)){
                        $key = $key.'_1';
                        $realpath = ROOTDIR.$media_dirname.'/'.$key.'.'.$fileext;
                    }

                    if($tmpfile && @move_uploaded_file($tmpfile,$realpath)){
                        $arr['album_id'] = $album_id;
                        $arr['path'] = $media_dirname.'/'.$key.'.'.$fileext;
                        $arr['thumb'] = $thumb_dirname.'/'.$key.'.'.$fileext;
                        $arr['name'] = $filename;
                        $arr['create_time'] = time();
                        $arr['create_y'] = date('Y');
                        $arr['create_m'] = date('n');
                        $arr['create_d'] = date('j');
                        $imglib->load($realpath);
                        $arr['width'] = $imglib->getWidth();
                        $arr['height'] = $imglib->getHeight();
                        if( $imglib->getExtension() == 'jpg'){
                            $exif = $exiflib->get_exif($realpath);
                            if($exif){
                                $arr['exif'] = serialize($exif);
                                $taken_time = strtotime($exif['DateTimeOriginal']);
                                $arr['taken_time'] = $taken_time;
                                $arr['taken_y'] = date('Y',$taken_time);
                                $arr['taken_m'] = date('n',$taken_time);
                                $arr['taken_d'] = date('j',$taken_time);
                            }
                        }
                        
                        $water_setting = $this->setting->get_conf('watermark');
                        if($water_setting['type'] == 1){
                            $water_setting['water_mark_type'] = 'image';
                            $imglib->waterMarkSetting($water_setting);
                            $imglib->waterMark();
                            $imglib->save($realpath);
                        }elseif($water_setting['type'] == 2){
                            $water_setting['water_mark_type'] = 'font';
                            $water_setting['water_mark_font'] = $water_setting['water_mark_font']?ROOTDIR.'statics/font/'.$water_setting['water_mark_font']:'';
                            $imglib->waterMarkSetting($water_setting);
                            $imglib->waterMark();
                            $imglib->save($realpath);
                        }
                        //resize image to thumb: 180*180 
                        $imglib->resizeScale(180,180);
                        
                        $imglib->save(ROOTDIR.$arr['thumb']);
                        
                        $this->mdl_photo->save($arr);
                    }else{
                        $error .= '文件'.$filename.'上传失败！'.'<br />';
                    }
                }else{
                    $empty_num++;
                }
            }
            if($empty_num == count($_FILES['imgs']['name'])){
                $this->output->set('msginfo','<div class="failed">您没有选择图片上传，请重新上传！</div>');
            }else{
                $this->mdl_album->update_photos_num($album_id);
                $this->mdl_album->check_repare_cover($album_id);
                
                if($error){
                    $this->output->set('msginfo','<div class="failed">'.$error.'</div>');
                }else{
                    $this->output->set('msginfo','<div class="success">上传成功！'.'<a href="'.site_link('photos','index',array('aid'=>$album_id)).'">查看相册</a></div>');
                }
            }
            
            loader::view('upload/normal');
        }
        
    }
}