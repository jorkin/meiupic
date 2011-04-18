<?php

class upload_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo =& loader::model('photo');
    }
    
    function index(){
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
        
        if($type == 'multi'){
            $album_id = $this->getPost('album_id');
            if(!$album_id){
                echo ajax_box('请先选择相册！',null);
                return;
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
                $fileext = strtolower(end(explode('.',$filename)));
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
        }else{
            //todo: normally save file
        }
        $gourl = site_link('photos','index',array('aid'=>$album_id));
        echo ajax_box('上传照片成功！',null,1,$gourl);
    }
}