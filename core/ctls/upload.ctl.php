<?php

class upload_ctl extends pagecore{
    
    function _init(){
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo =& loader::model('photo');
    }
    
    function index(){

        $album_id = $this->getRequest('aid');
        $this->output->set('album_id',$album_id);

        $this->output->set('albums_list',$this->mdl_album->get_kv());

        $page_title = lang('upload_photo').' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }

    function multi(){
        need_login('page');
        
        $album_id = $this->getRequest('aid');

        if(!$album_id){
            showError(lang('pls_sel_album'));
        }
        
        $this->output->set('album_id',$album_id);
        $this->output->set('upload_setting',$this->setting->get_conf('upload'));
        $album_info = $this->mdl_album->get_info($album_id);
        $this->output->set('album_info',$album_info);

        $img_lib = & loader::lib('image');
        $supportType =  $img_lib->supportType();
        $this->output->set('support_type',implode(',',$supportType));
        
        $page_title = lang('upload_photo').' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function normal(){
        need_login('page');
        
        $album_id = $this->getGet('aid');

        if(!$album_id){
            showError(lang('pls_sel_album'));
        }

        $this->output->set('album_id',$album_id);
        $album_info = $this->mdl_album->get_info($album_id);
        $this->output->set('album_info',$album_info);
        
        $page_title = lang('upload_photo').' - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function process(){
        set_time_limit(0);
        $type = $this->getGet('t');

        $json =& loader::lib('json');
        if(!$this->user->loggedin()){
            $return = array(
                'jsonrpc'=>'2.0',
                'error'=> array( 
                    'code'=>100,
                    'message'=>lang('pls_login_before_upload')
                 ),
                 'id'=>'id');
             echo $json->encode($return);
             exit;
        }
        
        $album_id = $this->getRequest('aid');
        $chunk = $this->getRequest('chunk',0);
        $chunks = $this->getRequest('chunks',0);
        $filename = $this->getRequest('name','');
        $target_dir = ROOTDIR.'cache'.DIRECTORY_SEPARATOR.'tmp';
        
        $storage_mdl =& loader::model('storage');
        $status = $storage_mdl->upload_multi($target_dir,
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

        if($status ==0 && ($chunk+1==$chunks)){
            if(! $this->_save($album_id,$target_dir.'/'.$filename,$filename)){
                $return = array(
                'jsonrpc'=>'2.0',
                'error'=>array(
                    'code'=>$status,
                    'message'=> lang('Failed to save file.')
                 ),
                 'id'=>'id');
            }
        } 
        echo $json->encode($return);
        exit;
    }

    function _save($album_id,$tmpfile,$filename){
        $media_dirname = 'data/'.$album_id;//.date('Ymd');
        $thumb_dirname = 'data/thumb/'.$album_id;//.date('Ymd');
        
        $storlib =& loader::lib('storage');
        $imglib =& loader::lib('image');
        $exiflib =& loader::lib('exif');
        $fileext = file_ext($filename);
        $key = str_replace('.','',microtime(true));
        
        $tmpfile_thumb = $tmpfile.'_thumb.'.$fileext;

        $filepath = $media_dirname.'/'.$key.'.'.$fileext;
        $thumbpath = $thumb_dirname.'/'.$key.'.'.$fileext;

        if(file_exists($tmpfile)){
            $imglib->load($tmpfile);
            
            $arr['width'] = $imglib->getWidth();
            $arr['height'] = $imglib->getHeight();
            if( $imglib->getExtension() == 'jpg'){
                $exif = $exiflib->get_exif($tmpfile);
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
                $imglib->save($tmpfile);
            }elseif($water_setting['type'] == 2){
                $water_setting['water_mark_type'] = 'font';
                $water_setting['water_mark_font'] = $water_setting['water_mark_font']?ROOTDIR.'statics/font/'.$water_setting['water_mark_font']:'';
                $imglib->waterMarkSetting($water_setting);
                $imglib->waterMark();
                $imglib->save($tmpfile);
            }
            //resize image to thumb: 180*180 
            $imglib->resizeScale(180,180);
            $imglib->save($tmpfile_thumb);
            
            if( $storlib->upload($filepath,$tmpfile)){
                $arr['album_id'] = $album_id;
                $arr['path'] = $media_dirname.'/'.$key.'.'.$fileext;
                $arr['thumb'] = $thumb_dirname.'/'.$key.'.'.$fileext;
                $arr['name'] = file_pure_name($filename);
                $arr['create_time'] = time();
                $arr['create_y'] = date('Y');
                $arr['create_m'] = date('n');
                $arr['create_d'] = date('j');
                
                $storlib->upload($thumbpath,$tmpfile_thumb);

                if(!($photo_id = $this->mdl_photo->save($arr))){
                    $storlib->delete($filepath);
                    $storlib->delete($thumbpath);
                }
                @unlink($tmpfile);

                $this->plugin->trigger('uploaded_photo',$photo_id);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function save(){
        set_time_limit(0);
        ignore_user_abort(true);
        $type = $this->getGet('t');
        $album_id = $this->getRequest('aid');

        if(!$album_id){
            showError(lang('pls_sel_album'));
        }

        if($type == 'multi'){
            need_login('ajax');

            $files_count = intval($this->getPost('muilti_uploader_count'));
            for($i=0;$i<$files_count;$i++){
                $filename = $this->getPost("muilti_uploader_{$i}_tmpname");
                $realname = $this->getPost("muilti_uploader_{$i}_name");
                $purename = file_pure_name($filename);
                $purerealname = file_pure_name($realname);
                $photorow = $this->mdl_photo->get_photo_by_name_aid($album_id,$purename);
                if($photorow){
                    $this->mdl_photo->update($photorow['id'],array('name'=>$purerealname));
                }
            }
            
            $this->mdl_album->update_photos_num($album_id);
            $this->mdl_album->check_repare_cover($album_id);
            
            $gourl = site_link('photos','index',array('aid'=>$album_id));
            form_ajax_success('box',lang('upload_photo_success'),null,1,$gourl);
        }else{
            need_login('page');
            
            $this->output->set('album_id',$album_id);
            $album_info = $this->mdl_album->get_info($album_id);
            $this->output->set('album_info',$album_info);

            $page_title = lang('upload_photo').' - '.$this->setting->get_conf('site.title');
            $page_keywords = $this->setting->get_conf('site.keywords');
            $page_description = $this->setting->get_conf('site.description');

            $this->page_init($page_title,$page_keywords,$page_description);
            
            $imglib =& loader::lib('image');
            $supportType = $imglib->supportType();

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
                        $error .= lang('failed_larger_than_server',$filename).'<br />';
                        continue;
                    }
                    
                    if($allowsize && $filesize>$allowsize){
                        $error .= lang('failed_larger_than_usetting',$filename).'<br />';
                        continue;
                    }
                    
                    if($filesize == 0){
                        $error .= lang('failed_if_file',$filename).'<br />';
                        continue;
                    }
                    if(!in_array($fileext,$supportType)){
                        $error .= lang('failed_not_support',$filename).'<br />';
                        continue;
                    }
                    

                    if(! $this->_save($album_id,$tmpfile,$filename)){
                        $error .= lang('file_upload_failed',$filename).'<br />';
                    }
                }else{
                    $empty_num++;
                }
            }
            if($empty_num == count($_FILES['imgs']['name'])){
                $this->output->set('msginfo','<div class="failed">'.lang('need_sel_upload_file').'</div>');
            }else{
                $this->mdl_album->update_photos_num($album_id);
                $this->mdl_album->check_repare_cover($album_id);
                
                if($error){
                    $this->output->set('msginfo','<div class="failed">'.$error.'</div>');
                }else{
                    $this->output->set('msginfo','<div class="success">'.lang('upload_photo_success').'<a href="'.site_link('photos','index',array('aid'=>$album_id)).'">'.lang('view_album').'</a></div>');
                }
            }
            
            loader::view('upload/normal');
        }
        
    }
}