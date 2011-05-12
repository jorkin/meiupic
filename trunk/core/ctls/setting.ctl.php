<?php

class setting_ctl extends pagecore{
    
    function _init(){
        $this->plugin =& loader::lib('plugin');
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
        need_login('ajax_box');
        
        $site = $this->getPost('site');
        $site['title'] = safe_convert($site['title']);
        $site['url'] = safe_convert($site['url']);
        $site['keywords'] = safe_convert($site['keywords']);
        $site['description'] = safe_convert($site['description']);
        
        if($site['title'] == ''){
            form_ajax_failed('box','站点名称不能为空！');
        }
        if($site['url'] == ''){
            form_ajax_failed('box','相册URL不能为空！');
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
        form_ajax_success('box','保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
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
        need_login('ajax_box');
        
        $upload = $this->getPost('upload');
        $enable_pre_resize = $this->getPost('enable_pre_resize');
        if($enable_pre_resize){
            if($upload['resize_width'] == '' || !is_numeric($upload['resize_width'])){
                form_ajax_failed('box','图片的最大宽度不能为空，并且必须为数字！');
            }
            if($upload['resize_height'] == '' || !is_numeric($upload['resize_height'])){
                form_ajax_failed('box','图片的最大高度不能为空，并且必须为数字！');
            }
            if($upload['resize_quality'] < 1 || $upload['resize_quality'] > 100){
                form_ajax_failed('box','图片质量必须介于1-100！');
            }
            $this->setting->set_conf('upload.resize_width',$upload['resize_width']);
            $this->setting->set_conf('upload.resize_height',$upload['resize_height']);
            $this->setting->set_conf('upload.resize_quality',$upload['resize_quality']);
            $this->setting->set_conf('upload.enable_pre_resize',true);
        }else{
            $this->setting->set_conf('upload.enable_pre_resize',false);
        }
        
        $this->setting->set_conf('upload.allow_size',$upload['allow_size']);
        form_ajax_success('box','保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
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
        need_login('ajax_box');
        
        $watermark_type = $this->getPost('watermark_type','0');
        $watermark = $this->getPost('watermark');
        $this->setting->set_conf('watermark.type',$watermark_type);
        if(!isset($watermark['water_mark_pos'])){
            $watermark['water_mark_pos'] = 5;
        }
        
        if($watermark_type == 1){
            if($watermark['water_mark_image'] == ''){
                form_ajax_failed('box','图片水印地址不能为空！');
            }
            if($watermark['water_mark_opacity'] < 1 || $watermark['water_mark_opacity'] > 100){
                form_ajax_failed('box','水印透明度必须介于1-100！');
            }
            $this->setting->set_conf('watermark.water_mark_image',$watermark['water_mark_image']);
            $this->setting->set_conf('watermark.water_mark_opacity',$watermark['water_mark_opacity']);
            $this->setting->set_conf('watermark.water_mark_pos',$watermark['water_mark_pos']);
        }elseif($watermark_type == 2){
            if($watermark['water_mark_string'] == ''){
                form_ajax_failed('box','水印文字内容不能为空！');
            }
            if($watermark['water_mark_fontsize'] < 1){
                form_ajax_failed('box','水印文字大小必须大于1！');
            }
            if(!check_color($watermark['water_mark_color'])){
                form_ajax_failed('box','水印文字颜色不是有效的颜色！');
            }
            if(!isset($watermark['water_mark_font']) || !$watermark['water_mark_font']){
                form_ajax_failed('box','请选择水印文字字体！');
            }
            $this->setting->set_conf('watermark.water_mark_string',$watermark['water_mark_string']);
            $this->setting->set_conf('watermark.water_mark_fontsize',$watermark['water_mark_fontsize']);
            $this->setting->set_conf('watermark.water_mark_color',$watermark['water_mark_color']);
            $this->setting->set_conf('watermark.water_mark_font',$watermark['water_mark_font']);
            $this->setting->set_conf('watermark.water_mark_pos',$watermark['water_mark_pos']);
        }
        form_ajax_success('box','保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
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

        $mdl_theme =& loader::model('theme');
        $themes = $mdl_theme->all_themes();
        $this->output->set('themes',$themes);

        $page_title = '风格设置 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');

        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function theme_set(){
        need_login('ajax_page');
        
        $theme = $this->getGet('theme');
        $this->setting->set_conf('system.current_theme',$theme);
        
        ajax_box('设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function theme_edit(){
        need_login('ajax_page');
        
        $theme = $this->getGet('theme');
        $this->output->set('theme',$theme);
        
        $_config =  $this->setting->get_conf('theme.'.$theme);
        
        ob_start();
        include template('_config',$theme,'themes/'.$theme);
        $setting_config = ob_get_clean();
        
        $this->output->set('setting_config',$setting_config);
        $this->render();
    }
    
    function theme_save_setting(){
        need_login('ajax');
        
        $theme = $this->getGet('theme');
        $config = $this->getPosts();
        
        $this->setting->set_conf('theme.'.$theme,$config);
        form_ajax_success('box','保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
    }
    
    function theme_confirm_remove(){
        need_login('ajax_page');
        $theme = $this->getGet('theme');
        $this->output->set('theme',$theme);
        
        $this->render();
    }
    
    function theme_remove(){
        need_login('ajax_page');
        
        $theme = $this->getGet('theme');
        if($theme == ''){
            ajax_box('请确认要删除的风格是否存在！');
        }
        if($theme == 'default'){
            ajax_box('默认风格不能删除！');
        }
        $current_theme = $this->setting->get_conf('system.current_theme');
        if($current_theme == $theme){
            ajax_box('当前风格正在使用中，无法删除！');
        }
        
        if(loader::model('theme')->remove($theme)){
            ajax_box('删除主题成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box('删除主题失败！');
        }
        
    }
    
    function plugins(){
        need_login('page');

        $plugins = $this->plugin->get_plugins();
        $this->output->set('plugins',$plugins);
        
        $page_title = '插件管理 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');

        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function plugin_install(){
        need_login('ajax_page');
        
        $plugin = $this->getGet('plugin');
        if($this->plugin->install_plugin($plugin)){
            ajax_box('安装插件成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box('安装插件失败！');
        }
    }
    
    function plugin_enable(){
        need_login('ajax_page');
        
        $plugin = $this->getGet('plugin');
        if($this->plugin->enable_plugin($plugin)){
            ajax_box('启用插件成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box('启用插件失败！');
        }
    }
    
    function plugin_disable(){
        need_login('ajax_page');
        
        $plugin = $this->getGet('plugin');
        if($this->plugin->disable_plugin($plugin)){
            ajax_box('停用插件成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box('停用插件失败！');
        }
    }
    
    function plugin_remove(){
        need_login('ajax_page');
        
        $plugin = $this->getGet('plugin');
        if($this->plugin->remove_plugin($plugin)){
            ajax_box('删除插件成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            ajax_box('删除插件失败！');
        }
    }
    
    function plugin_config(){
        need_login('ajax_page');
        
        $plugin = $this->getGet('plugin');
        $this->output->set('plugin',$plugin);

        $_config = $this->plugin->get_config($plugin);
        
        ob_start();
        include template('_config',$plugin,'plugins/'.$plugin);
        $plugin_config_fields = ob_get_clean();
        
        $this->output->set('plugin_config_fields',$plugin_config_fields);
        $this->render();
    }
    
    function plugin_save_config(){
        need_login('ajax');
        
        $plugin = $this->getGet('plugin');
        $config = $this->getPosts();
        if($this->plugin->save_config($plugin,$config)){
            form_ajax_success('box','保存设置成功！',null,0.5,$_SERVER['HTTP_REFERER']);
        }else{
            form_ajax_success('text','保存设置失败！',null,0.5,$_SERVER['HTTP_REFERER']);
        }
        
    }
    
    function info(){
        need_login('page');
        $info = loader::model('utility')->sys_info();
        $this->output->set('info',$info);
        
        $page_title = '系统信息 - 系统设置 - '.$this->setting->get_conf('site.title');
        $page_keywords = $this->setting->get_conf('site.keywords');
        $page_description = $this->setting->get_conf('site.description');

        $this->page_init($page_title,$page_keywords,$page_description);
        $this->render();
    }
    
    function phpinfo(){
        need_login('page');
        
        phpinfo();
    }
}