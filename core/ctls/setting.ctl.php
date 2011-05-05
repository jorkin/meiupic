<?php

class setting_ctl extends pagecore{
    
    function _init(){
        //$this->plugin =& loader::lib('plugin');
        //$this->mdl_album = & loader::model('album');
    }
    
    function index(){
        need_login('page');
        
        $site = $this->setting->get_conf('site');
        $site['description'] = safe_invert($site['description']);
        $this->output->set('site',$site);
        $this->output->set('enable_comment',$this->setting->get_conf('system.enable_comment'));
        
        $page_title = '基本设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function save_basic(){
        need_login('ajax');
        
        $site = $this->getPost('site');
        $site['title'] = safe_convert($site['title']);
        $site['url'] = safe_convert($site['url']);
        $site['keywords'] = safe_convert($site['keywords']);
        $site['description'] = safe_convert($site['description']);
        
        if($site['title'] == ''){
            ajax_box_failed('站点名称不能为空！',true);
        }
        if($site['url'] == ''){
            ajax_box_failed('相册URL不能为空！',true);
        }
        $this->setting->set_conf('site.title',$site['title']);
        $this->setting->set_conf('site.url',$site['url']);
        $this->setting->set_conf('site.keywords',$site['keywords']);
        $this->setting->set_conf('site.description',$site['description']);
        if($this->getPost('enable_comment')){
            $this->setting->set_conf('system.enable_comment',true);
        }else{
            $this->setting->set_conf('system.enable_comment',false);
        }
        ajax_box_success('保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function upload(){
        need_login('page');
        
        $upload = $this->setting->get_conf('upload');
        $this->output->set('upload',$upload);
        
        $page_title = '上传设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function save_upload(){
        need_login('ajax');
        
        $upload = $this->getPost('upload');
        $enable_pre_resize = $this->getPost('enable_pre_resize');
        if($enable_pre_resize){
            if($upload['resize_width'] == '' || !is_numeric($upload['resize_width'])){
                ajax_box_failed('图片的最大宽度不能为空，并且必须为数字！',true);
            }
            if($upload['resize_height'] == '' || !is_numeric($upload['resize_height'])){
                ajax_box_failed('图片的最大高度不能为空，并且必须为数字！',true);
            }
            if($upload['resize_quality'] < 1 || $upload['resize_quality'] > 100){
                ajax_box_failed('图片质量必须介于1-100！',true);
            }
            $this->setting->set_conf('upload.resize_width',$upload['resize_width']);
            $this->setting->set_conf('upload.resize_height',$upload['resize_height']);
            $this->setting->set_conf('upload.resize_quality',$upload['resize_quality']);
            $this->setting->set_conf('upload.enable_pre_resize',true);
        }else{
            $this->setting->set_conf('upload.enable_pre_resize',false);
        }
        
        $this->setting->set_conf('upload.allow_size',$upload['allow_size']);
        ajax_box_success('保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function watermark(){
        need_login('page');
        
        $fonts = get_fonts();
        $this->output->set('fonts',$fonts);
        
        $watermark = $this->setting->get_conf('watermark');
        $this->output->set('watermark',$watermark);
        
        $page_title = '水印设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');
        
        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function save_watermark(){
        need_login('ajax');
        
        $watermark_type = $this->getPost('watermark_type','0');
        $watermark = $this->getPost('watermark');
        $this->setting->set_conf('watermark.type',$watermark_type);
        if(!isset($watermark['water_mark_pos'])){
            $watermark['water_mark_pos'] = 5;
        }
        
        if($watermark_type == 1){
            if($watermark['water_mark_image'] == ''){
                ajax_box_failed('图片水印地址不能为空！',true);
            }
            if($watermark['water_mark_opacity'] < 1 || $watermark['water_mark_opacity'] > 100){
                ajax_box_failed('水印透明度必须介于1-100！',true);
            }
            $this->setting->set_conf('watermark.water_mark_image',$watermark['water_mark_image']);
            $this->setting->set_conf('watermark.water_mark_opacity',$watermark['water_mark_opacity']);
            $this->setting->set_conf('watermark.water_mark_pos',$watermark['water_mark_pos']);
        }elseif($watermark_type == 2){
            if($watermark['water_mark_string'] == ''){
                ajax_box_failed('水印文字内容不能为空！',true);
            }
            if($watermark['water_mark_fontsize'] < 1){
                ajax_box_failed('水印文字大小必须大于1！',true);
            }
            if(!check_color($watermark['water_mark_color'])){
                ajax_box_failed('水印文字颜色不是有效的颜色！',true);
            }
            if(!isset($watermark['water_mark_font']) || !$watermark['water_mark_font']){
                ajax_box_failed('请选择水印文字字体！',true);
            }
            $this->setting->set_conf('watermark.water_mark_string',$watermark['water_mark_string']);
            $this->setting->set_conf('watermark.water_mark_fontsize',$watermark['water_mark_fontsize']);
            $this->setting->set_conf('watermark.water_mark_color',$watermark['water_mark_color']);
            $this->setting->set_conf('watermark.water_mark_font',$watermark['water_mark_font']);
            $this->setting->set_conf('watermark.water_mark_pos',$watermark['water_mark_pos']);
        }
        ajax_box_success('保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function fileupload(){
        $error = "";
        $msg = "";
        $fileElementName = 'fileToUpload';
        if(!empty($_FILES[$fileElementName]['error'])){
            $error = '上传失败！';
        }elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
            $error = '您没有选择文件上传！';
        }else{
            $path_dir = 'data/watermark';
            if(!file_exists(ROOTDIR.$path_dir)){
                @mkdir(ROOTDIR.$path_dir);
            }
            $filename = $_FILES[$fileElementName]['name'];
            $fileext = strtolower(end(explode('.',$filename)));
            $path = $path_dir.'/'.date('Ymd').'.'.$fileext;
            if(@move_uploaded_file($_FILES[$fileElementName]['tmp_name'],ROOTDIR.$path)){
                $msg = $path;
            }else{
                $error = '上传失败！';
            }
        }
        
        echo loader::lib('json')->encode(array('error'=>$error,'msg'=>$msg));
        exit;
    }
    
    function themes(){
        need_login('page');

        $mdl_template =& loader::model('template');
        $themes = $mdl_template->all_themes();
        $this->output->set('themes',$themes);

        $page_title = '基本设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');

        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function theme_set(){
        $theme = $this->getGet('theme');
        $this->setting->set_conf('system.current_theme',$theme);
        
        echo ajax_box('设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
}