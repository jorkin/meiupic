<?php
class update_ctl extends pagecore{
    
    function _init(){
        
    }
    
    function core(){
        need_login('page');
        $this->setting->set_conf('update',array());

        $newversion = $this->getRequest('version');
        
        $software = 'meiupic';
        $version = MPIC_VERSION;
        if($newversion == $version){
            exit(lang('no_need_to_update'));
        }
        if(!$newversion){
            exit(lang('version_can_not_be_empty'));
        }
        //检查目录是否可以读写
        $directory = @dir(ROOTDIR);
        while($entry = $directory->read()) {
            if($entry == '..' || $entry == '.'){
                continue;
            }
            $filename = ROOTDIR.$entry;
            if(is_dir($filename) && !dir_writeable($filename)){
                exit(lang('dir_not_writable',$filename));
            }elseif(is_file($filename) && !is_writable($filename)){
                exit(lang('file_not_writable',$filename));
            }
        }
        $directory->close();


        $langset = LANGSET;
        $time = time();
        $hash = md5("{$newversion}{$software}{$version}{$langset}{$time}");
        $q = base64_encode("newversion=$newversion&software=$software&version=$version&langset=$langset&time=$time&hash=$hash");

        $url = CHECK_UPDATE_URL.'?act=update&q='.$q;
        
        $response = get_remote($url,2);
        if(!$response){
            exit(lang('connect_to_server_failed'));
        }

        $json =& loader::lib('json');
        $result = $json->decode($response);
        
        if($result['return']){
            $tmpfile = ROOTDIR.'cache/tmp/update.zip';

            if(file_exists($tmpfile) && md5_file($tmpfile)==$result['md5']){
                echo lang('file_has_been_downloaded').'<br />';
            }else{
                $content = get_remote($result['package']);
                file_put_contents($tmpfile,$content);

                $file_md5 = md5_file($tmpfile);
                if($file_md5 != $result['md5']){
                    echo lang('download_package_failed').'<br />';
                    exit;
                }
                echo lang('download_package_succ').'<br />';
            }
            $zip =& loader::lib('zip');
            $zip->load_file($tmpfile);
            $zip->extract(PCLZIP_OPT_PATH, './', PCLZIP_OPT_REPLACE_NEWER);
            echo lang('unzip_package_succ').'<br />';
            echo lang('delete_tmp_download_file').'<br />';
            unlink($tmpfile);
            echo lang('upgrade_after_jump').'<br />';

            redirect(site_link('update','script'),1);
        }else{
            exit(lang('get_update_fail'));
        }
    }

    function script(){
        $prev_version = $this->setting->get_conf('system.version');
        $current_version = MPIC_VERSION;
        if($current_version == $prev_version){
            echo lang('have_been_updated').'<br />';
            exit;
        }

        if(version_compare($current_version,$prev_version,'<')){
            echo lang('could_not_degrade').'<br />';
            exit;
        }

        if($prev_version == '' || version_compare($prev_version,'2.0','<') ){
            echo lang('too_old_to_update').'<br />';
            exit;
        }
        
        $script_file = ROOTDIR.'install/upgrade_'.$prev_version.'.php';
        if(file_exists($script_file)){
            require_once($script_file);
        }

        $this->setting->set_conf('system.version',MPIC_VERSION);
        $this->setting->set_conf('update',array());
        //清除缓存
        //Todo 需要统一清除缓存的功能，使其兼容memcache等
        dir_clear(ROOTDIR.'cache/data');
        dir_clear(ROOTDIR.'cache/templates');
        dir_clear(ROOTDIR.'cache/tmp');

        echo lang('upgrade_success').'<a href="'.site_link('default','index').'">'.lang('click_to_jump').'</a>';
    }

    function _createtable($sql) {
        $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
        $type = in_array($type, array('MYISAM', 'HEAP', 'MEMORY')) ? $type : 'MYISAM';
        return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
        ($this->db->version() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=utf8" : " TYPE=$type");
    }
}