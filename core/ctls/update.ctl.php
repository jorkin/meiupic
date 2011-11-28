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

        $langset = LANGSET;
        $time = time();
        $hash = md5("{$newversion}{$software}{$version}{$langset}{$time}");
        $q = base64_encode("newversion=$newversion&software=$software&version=$version&langset=$langset&time=$time&hash=$hash");

        $url = CHECK_UPDATE_URL.'?act=update&q='.$q;

        $json =& loader::lib('json');
        $result = $json->decode(get_remote($url,2));
        
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
        }
    }

    function script(){
        $prev_version = $this->setting->get_conf('system.version');
        $current_version = MPIC_VERSION;

        if(version_compare($current_version,$prev_version,'<')){
            echo '脚本无法执行降级操作！';
            exit;
        }

        if($prev_version == '' || version_compare($prev_version,'2.0','<=') ){
            echo '对不起,2.0以前版本无法自动升级！';
            exit;
        }

        
    }
}