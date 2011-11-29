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
        $func_name = '__'.str_replace('.','_',$prev_version).'to'.str_replace('.','_',$current_version);
        
        if(method_exists($this,$func_name)){
            call_user_func_array(array($this,$func_name),array());
        }
        $this->setting->set_conf('system.version',MPIC_VERSION);
        echo '升级成功，跳转至首页!<br />';
        redirect(site_link('default','index'),3);
    }

    function __2_0to2_1_0(){
        if($this->db->adapter == 'sqlite'){
            $this->db->query('CREATE TABLE #@cate (
                id integer NOT NULL primary key,
                par_id int(4) NOT NULL DEFAULT 0,
                name varchar(100) NOT NULL,
                cate_path varchar(255) DEFAULT NULL,
                sort int(4) NOT NULL DEFAULT 0)');
            $this->db->query('CREATE TABLE #@nav (
                id integer NOT NULL primary key ,
                type tinyint(1) NOT NULL DEFAULT 1,
                name varchar(50) NOT NULL ,
                url varchar(200) NOT NULL ,
                sort smallint(4) NOT NULL  DEFAULT 100,
                enable tinyint(1) NOT NULL DEFAULT 1)');
            $this->db->query('ALTER TABLE #@albums ADD cate_id int(4) NOT NULL DEFAULT 0');
            $this->db->query('CREATE INDEX cg_par_id on #@cate (par_id)');
            $this->db->query('CREATE INDEX a_cate_id on #@albums (cate_id)');
            $this->db->query('CREATE INDEX p_album_id on #@photos (album_id)');
        }else{
            $this->db->query($this->_createtable("CREATE TABLE `#@cate` (
                  `id` int(4) NOT NULL AUTO_INCREMENT,
                  `par_id` int(4) NOT NULL DEFAULT '0',
                  `name` varchar(100) NOT NULL,
                  `cate_path` varchar(255) DEFAULT NULL,
                  `sort` int(4) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `par_id` (`par_id`)
                ) TYPE=MyISAM ;"));
            $this->db->query($this->_createtable("CREATE TABLE `#@nav` (
                `id` smallint(4) NOT NULL AUTO_INCREMENT ,
                `type` tinyint(1) NOT NULL DEFAULT '1',
                `name` varchar(50) NOT NULL ,
                `url` varchar(200) NOT NULL ,
                `sort` smallint(4) NOT NULL DEFAULT '100',
                `enable` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY ( `id` )
                ) TYPE=MyISAM ;"));
            $this->db->query('ALTER TABLE `#@albums` ADD `cate_id` int(4) NOT NULL DEFAULT 0 , ADD INDEX `cate_id` (`cate_id`)');

        }
        $this->setting->set_conf('site.share_title','分享张很赞的照片:{name}');
        $this->db->insert('#@nav',array(
            'type'=>0,
            'name'=>'首页',
            'url' =>'default',
            'sort'=>'100'
        ));
        $this->db->query();
        $this->db->insert('#@nav',array(
            'type'=>0,
            'name'=>'标签',
            'url' =>'tags',
            'sort'=>'100'
        ));
        $this->db->query();
        $this->db->insert('#@nav',array(
            'type'=>0,
            'name'=>'分类',
            'url' =>'category',
            'sort'=>'100'
        ));
        $this->db->query();
    }

    function _createtable($sql) {
        $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
        $type = in_array($type, array('MYISAM', 'HEAP', 'MEMORY')) ? $type : 'MYISAM';
        return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
        ($this->db->version() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=utf8" : " TYPE=$type");
    }
}