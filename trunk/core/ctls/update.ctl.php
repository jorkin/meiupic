<?php
class update_ctl extends pagecore{
    
    function _init(){
        //$this->mdl_album = & loader::model('album');
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
                echo '文件已存在！<br />';
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
        }
        //get_remote
    }
}