<?php
class setting extends modelfactory{
    
    function save_setting($new_setting){
        $setting_content = "<?php \n";

        $setting_content .= "\$setting['url'] = '".$new_setting['url']."';\n";
        $setting_content .= "\$setting['imgdir'] = '".$new_setting['imgdir']."';\n";
        $setting_content .= "\$setting['upload_runtimes'] = '".$new_setting['upload_runtimes']."';\n";
        $setting_content .= "\$setting['open_pre_resize'] = ".$new_setting['open_pre_resize'].";\n";
        $setting_content .= "\$setting['resize_img_width'] = '".$new_setting['resize_img_width']."';\n";
        $setting_content .= "\$setting['resize_img_height'] = '".$new_setting['resize_img_height']."';\n";
        $setting_content .= "\$setting['resize_quality'] = '".$new_setting['resize_quality']."';\n";
        $setting_content .= "\$setting['imgdir_type'] = '".$new_setting['imgdir_type']."';\n";
        $setting_content .= "\$setting['extension_allow'] = '".$new_setting['extension_allow']."';\n";
        $setting_content .= "\$setting['size_allow'] = '".$new_setting['size_allow']."';\n";
        $setting_content .= "\$setting['pageset'] = '".$new_setting['pageset']."';\n";
        $setting_content .= "?>";
        
        return @file_put_contents(ROOTDIR.'conf/setting.php',$setting_content);
    }
    
    function get_setting(){
        global $setting;
        return $setting;
    }
    
    function change_admin_pass($id,$newpass){
        $query_arr['table'] = 'admin';
        $query_arr['values'] = array(
                'userpass'=>$newpass
            );
        $query_arr['where'] = array('id='.intval($id));
        return $this->db->update($query_arr);
    }
}
