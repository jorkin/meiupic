<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */

class utility_mdl extends modelfactory{
    
    function sys_info(){
        $env_items = array(
                    'meiupic_version' => array('c'=>'MPIC_VERSION'),
                    'operate_system' => array('c' => 'PHP_OS'),
                    'server_software' => array('s' => 'SERVER_SOFTWARE'),
                    'php_runmode'=>'php_runmode',
                    'php_version' => array('c'=>'PHP_VERSION'),
                    'memory_limit' => array('i'=>'memory_limit'),
                    'post_max_size' => array('i'=>'post_max_size'),
                    'upload_max_filesize' => array('i'=>'upload_max_filesize'),
                    'mysql_support' => array('f'=>'mysql_connect'),
                    'mysqli_support' => array('f'=>'mysqli_connect'),
                    'sqlite_support' => array('f' => 'sqlite_open'),
                    'database_version' => 'database_version',
                    'gd_info' => 'gd_info',
                    'imagick_support' => array('cla' => 'imagick'),
                    'zlib_support' => array('f' => 'gzopen')
                    );
        $info = array();
        foreach($env_items as $k=>$v){
            if($k == 'php_runmode'){
               $info[] = array('title'=>lang($k),'value'=>php_sapi_name());
            }elseif($k == 'database_version'){
               $adapater = ($this->db->adapter=='mysql' || $this->db->adapter=='mysqli')?'Mysql':$this->db->adapter;
               $info[] = array('title'=>lang($k),'value'=>$adapater.' '.$this->db->version());
            }elseif($k == 'gd_info'){
                $tmp = function_exists('gd_info') ? gd_info() :false;
                $gd_ver = empty($tmp['GD Version']) ? lang('notsupport') : $tmp['GD Version'];
                $gd_rst = array();
                if(isset($tmp['FreeType Support'])){
                    $gd_rst[] = 'freetype';
                }
                if(isset($tmp['GIF Read Support'])){
                    $gd_rst[] = 'gif';
                }
                if(isset($tmp['JPEG Support'])){
                    $gd_rst[] = 'jpg';
                }
                if(isset($tmp['PNG Support'])){
                    $gd_rst[] = 'png';
                }
                $info[] = array('title'=>lang($k),'value'=>$gd_ver.' '.implode(',',$gd_rst));
            }elseif($k == 'meiupic_version'){
                if(file_exists(ROOTDIR.'conf/revision.txt')){
                    $rversion = '('.file_get_contents(ROOTDIR.'conf/revision.txt').')';
                }else{
                    $rversion = '';
                }
                $info[] = array('title'=>lang($k),'value'=>MPIC_VERSION.' '.$rversion);
            }elseif(isset($v['f'])){
                $info[] = array('title'=>lang($k),'value'=>function_exists($v['f'])?lang('support'):lang('notsupport'));
            }elseif(isset($v['c'])){
                $info[] = array('title'=>lang($k),'value'=>constant($v['c']));
            }elseif(isset($v['s'])){
                $info[] = array('title'=>lang($k),'value'=>$_SERVER[$v['s']]);
            }elseif(isset($v['cla'])){
                $info[] = array('title'=>lang($k),'value'=>class_exists($v['cla'])?lang('support'):lang('notsupport'));
            }
        }
        return $info;
      
    }
}