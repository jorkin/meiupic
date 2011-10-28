<?php

class upload_ctl extends pagecore{
    
    function _init(){
        $this->mdl_album = & loader::model('album');
        $this->mdl_photo =& loader::model('photo');
    }
    
    function index(){

        $album_id = $this->getRequest('aid');
        $act = $this->getGet('t');
        if($act=='normal'){
            $act = 'normal';
        }else{
            $act = 'multi';
        }
        $this->output->set('act',$act);
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
        
        $album_id = $this->getRequest('aid');

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
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

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
        
        $tmpfs_lib =& loader::lib('tmpfs');
        $status = $tmpfs_lib->upload($filename,$chunk!=0);
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

        if($status ==0 && ($chunks == 0||$chunk+1==$chunks)){
            if(! $this->mdl_photo->save_upload($album_id,$tmpfs_lib->get_path($filename),$filename)){
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

    function reupload(){
        need_login('page');

        $id = $this->getGet('id');
        $photo_info = $this->mdl_photo->get_info($id);
        
        $imglib =& loader::lib('image');
        $supportType = $imglib->supportType();
        $filesize = $_FILES['upfile']['size'];
        $filename = $_FILES['upfile']['name'];
        $fileext = file_ext($filename);
        $allowsize = allowsize($this->setting->get_conf('upload.allow_size'));
        $error = '';

        if($_FILES['upfile']['error'] == 1){
            $error = lang('failed_larger_than_server',$filename);
            echo '<script>
                alert("'.$error.'");
                parent.hide_loading_bar();
            </script>';
            exit;
        }
        
        if($allowsize && $filesize>$allowsize){
            $error = lang('failed_larger_than_usetting',$filename);
            echo '<script>
                alert("'.$error.'");
                parent.hide_loading_bar();
            </script>';
            exit;
        }
        
        if($filesize == 0){
            $error = lang('failed_if_file',$filename);
            echo '<script>
                alert("'.$error.'");
                parent.hide_loading_bar();
            </script>';
            exit;
        }
        if(!in_array($fileext,$supportType)){
            $error = lang('failed_not_support',$filename);
            echo '<script>
                alert("'.$error.'");
                parent.hide_loading_bar();
            </script>';
            exit;
        }

        if($this->mdl_photo->save_upload($photo_info['album_id'],$_FILES['upfile']['tmp_name'],$filename,false,$photo_info)){
            if($photo_info['is_cover']){
                $this->mdl_album->set_cover($id);
            }

            echo '<script>
                parent.location.reload();
            </script>';

            exit;
        }else{
            echo '<script>
                alert("'.lang('Failed to save file.').$error.'");
                parent.$("#meiu_float_box").find("loading").hide();
                parent.$("#meiu_float_box").find("box_container").show();
            </script>';
            exit;
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
                    

                    if(! $this->mdl_photo->save_upload($album_id,$tmpfile,$filename)){
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