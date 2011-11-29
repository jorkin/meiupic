<?php
class update_ctl extends pagecore{
    
    function _init(){
        
    }
    
    function core(){
        need_login('page');
        $newversion = $this->getRequest('version');
        
        $software = 'meiupic';
        $version = MPIC_VERSION;
        if($newversion == $version){
            exit('No need to update!');
        }
        if(!$newversion){
            exit('新的版本号不能为空！');
        }
        //检查目录是否可以读写
        $directory = @dir(ROOTDIR);
        while($entry = $directory->read()) {
            if($entry == '..' || $entry == '.'){
                continue;
            }
            $filename = ROOTDIR.$entry;
            if(is_dir($filename) && !dir_writeable($filename)){
                exit('目录：'.$filename.' 不可写！');
            }elseif(is_file($filename) && !is_writable($filename)){
                exit('文件：'.$filename.' 不可写！');
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
            exit('远程服务器未相应！');
        }

        $json =& loader::lib('json');
        $result = $json->decode($response);
        
        if($result['return']){
            $tmpfile = ROOTDIR.'cache/tmp/update.zip';

            if(file_exists($tmpfile) && md5_file($tmpfile)==$result['md5']){
                echo '文件已下载！<br />';
            }else{
                $content = get_remote($result['package']);
                file_put_contents($tmpfile,$content);

                $file_md5 = md5_file($tmpfile);
                if($file_md5 != $result['md5']){
                    echo '下载更新文件失败！<br />';
                    exit;
                }
                echo '下载文件成功！<br />';
            }
            $zip =& loader::lib('zip');
            $zip->load_file($tmpfile);
            $zip->extract('./');
            echo '解压文件成功！<br />';
            echo '删除临时文件！<br />';
            unlink($tmpfile);
            echo '跳转后执行升级脚本！<br />';

            redirect(site_link('update','script'),1);
        }else{
            exit('获取更新失败！');
        }
    }

    function script(){
        need_login('page');

        $prev_version = $this->setting->get_conf('system.version');
        $current_version = MPIC_VERSION;
        if($current_version == $prev_version){
            echo '已经升级过了！<br />';
            exit;
        }

        if(version_compare($current_version,$prev_version,'<')){
            echo '脚本无法执行降级操作！<br />';
            exit;
        }

        if($prev_version == '' || version_compare($prev_version,'2.0','<') ){
            echo '对不起,2.0以前版本无法自动升级！<br />';
            exit;
        }
        
        $script_file = ROOTDIR.'install/upgrade_'.$prev_version.'.php';
        if(file_exists($script_file)){
            require_once($script_file);
        }

        $this->setting->set_conf('system.version',MPIC_VERSION);

        //清除缓存
        //Todo 需要统一清除缓存的功能，使其兼容memcache等
        dir_clear(ROOTDIR.'cache/data');
        dir_clear(ROOTDIR.'cache/templates');
        dir_clear(ROOTDIR.'cache/tmp');

        echo '升级成功，跳转至首页!<br />';

        echo '<a href="'.site_link('default','index').'">点击跳转至首页</a>';
    }

    function _createtable($sql) {
        $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
        $type = in_array($type, array('MYISAM', 'HEAP', 'MEMORY')) ? $type : 'MYISAM';
        return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
        ($this->db->version() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=utf8" : " TYPE=$type");
    }
}