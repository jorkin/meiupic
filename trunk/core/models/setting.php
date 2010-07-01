<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class setting extends modelfactory{
    
    function save_setting($new_setting){
        $htaccess_content = '<ifmodule mod_rewrite.c>
RewriteEngine On
Options +FollowSymLinks
# sqlite database path
RewriteRule database\.db / [F]'."\n";
        
        if($new_setting['access_ctl'] == 'true'){
            if('' != trim($new_setting['access_domain'])){
                $htaccess_content .= '#access'."\n";
                $htaccess_content .= 'RewriteCond %{HTTP_REFERER} !^$ [NC]'."\n";
                $access_arr = explode("\n",$new_setting['access_domain']);
                foreach($access_arr as $v){
                    if('' != trim($v)){
                        $htaccess_content .= 'RewriteCond %{HTTP_REFERER} !^(http|https)://'.trim($v).' [NC]'."\n";
                    }
                }
                $htaccess_content .= 'RewriteRule .*\.(jpg|jpeg|gif|png)$ ../img/noaccess.jpg [NC,L]'."\n";
            }
        }
        
        if($new_setting['demand_resize'] == 'true'){
            if(function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules())){
                $htaccess_content .= '#auto resize'."\n";
                $htaccess_content .= 'RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .*/(.*)_(.*)\.(jpg|gif|png)$ ../index.php?ctl=photo&act=resize&size=$2&key=$1 [NC,L]'."\n";
            }else{
                $new_setting['demand_resize'] = 'false';
            }
        }
        
        $htaccess_content .= '</ifmodule>';
        
        if($new_setting['demand_resize'] == 'true' || $new_setting['access_ctl'] == 'true'){
            @file_put_contents(DATADIR.'.htaccess',$htaccess_content);
            @chmod(DATADIR.'.htaccess',0755);
        }else{
            @unlink(DATADIR.'.htaccess');
        }
        
        $setting_content = "<?php \n";
        $setting_content .= "\$setting['site_title'] = '".html_replace($new_setting['site_title'])."';\n";
        $setting_content .= "\$setting['site_keyword'] = '".html_replace($new_setting['site_keyword'])."';\n";
        $setting_content .= "\$setting['site_description'] = '".html_replace($new_setting['site_description'])."';\n";
        $setting_content .= "\$setting['url'] = '".$new_setting['url']."';\n";
        $setting_content .= "\$setting['imgdir'] = '".$new_setting['imgdir']."';\n";
        $setting_content .= "\$setting['upload_runtimes'] = '".$new_setting['upload_runtimes']."';\n";
        $setting_content .= "\$setting['open_pre_resize'] = ".$new_setting['open_pre_resize'].";\n";
        $setting_content .= "\$setting['resize_img_width'] = '".$new_setting['resize_img_width']."';\n";
        $setting_content .= "\$setting['resize_img_height'] = '".$new_setting['resize_img_height']."';\n";
        $setting_content .= "\$setting['demand_resize'] = ".$new_setting['demand_resize'].";\n";
        $setting_content .= "\$setting['resize_quality'] = '".$new_setting['resize_quality']."';\n";
        $setting_content .= "\$setting['imgdir_type'] = '".$new_setting['imgdir_type']."';\n";
        $setting_content .= "\$setting['extension_allow'] = '".$new_setting['extension_allow']."';\n";
        $setting_content .= "\$setting['size_allow'] = '".$new_setting['size_allow']."';\n";
        $setting_content .= "\$setting['pageset'] = '".$new_setting['pageset']."';\n";
        $setting_content .= "\$setting['open_photo'] = ".$new_setting['open_photo'].";\n";
        $setting_content .= "\$setting['gallery_limit'] = '".$new_setting['gallery_limit']."';\n";
        $setting_content .= "\$setting['access_ctl'] = ".$new_setting['access_ctl'].";\n";
        $setting_content .= "\$setting['access_domain'] = '".$new_setting['access_domain']."';\n";
        $setting_content .= "?>";
        
        return @file_put_contents(ROOTDIR.'conf/setting.php',$setting_content);
    }
    
    function get_setting(){
        global $setting;
        return $setting;
    }
    
    function change_admin_pass($id,$newpass){
        $this->db->update('#admin','id='.intval($id),array('userpass'=>$newpass));
        return $this->db->query();
    }
}
