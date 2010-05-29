<?php

class auth {

    var $LOGIN_FLAG = false;
    
    var $M_info = NULL;

    var $db;
    
    function auth() {
        $this->db =& db();
        $this->config = array(
                'cookie_name' => 'PIC_AU',
                'cookie_path' => '/',
                'cookie_domain' => '',
                'auth_key' => 's29(8af)$jsdf23',
                'db_table' => '#admin',
                'db_uid'   => 'id',
                'db_upass' => 'userpass',
                'db_loginname' => 'username'
        );
        if(isset($_COOKIE[$this->config['cookie_name']])){

            $auth = $this->authcode(
                $_COOKIE[$this->config['cookie_name']],
                'DECODE', 
                md5($this->config['auth_key'])
            );

            $auth = explode("\t",$auth);
            $uid = isset($auth[1])?$auth[1]:0;
            $upass = isset($auth[0])?$auth[0]:'';

            $member_info = $this->_get_minfo_by_uid($uid);

            if(!$member_info){
                $this->LOGIN_FLAG = false;
            }else{
                if($member_info[$this->config['db_upass']] == $upass){    
                    $this->LOGIN_FLAG = true;
                    $this->M_info = $member_info;
                }else{
                    $this->LOGIN_FLAG = false;
                }
            }
        }else{
            $this->LOGIN_FLAG = false;
        }
    }
    
    function _get_minfo_by_uid($uid){
        global $db_config;
        if($db_config['adapter'] == 'txtsql'){
            $data = $this->db->select( array(
                    'table' => str_replace('#','',$this->config['db_table']),
                    'where' => array($this->config['db_uid'].' = '.intval($uid))
                    )
            );
            if(count($data) > 0){
                $member_info = $data[0];
            }else{
                $member_info = false;
            }
        }else{
            $this->db->select($this->config['db_table'],'*',$this->config['db_uid']."='$uid'");
            $member_info = $this->db->getRow();
        }
        return $member_info;
    }
    
    function _get_minfo_by_loginname($loginname){
        global $db_config;
        if($db_config['adapter'] == 'txtsql'){
            $data = $this->db->select( array(
                    'table' => str_replace('#','',$this->config['db_table']),
                    'where' => array($this->config['db_loginname'].' = '.$loginname)
                    )
            );
            if(count($data) > 0){
                $member_info = $data[0];
            }else{
                $member_info = false;
            }
        }else{
            $this->db->select($this->config['db_table'],'*',$this->config['db_loginname']."='$loginname'");
            $member_info = $this->db->getRow();
        }
        return $member_info;
    }
    /**
     * 判断用户是否登陆
     *
     * @return Bool
     */
    function isLogedin() {
        return $this->LOGIN_FLAG;
    }
    
    /**
     * 获取用户信息
     *
     * @param String $key
     * @param String $default
     * @return String
     */
    function getInfo($key,$default = '') {
        return isset ($this->M_info["$key"]) ? $this->M_info["$key"] : $default;
    }
    
    /**
     * 设置用户登陆
     *
     * @param String $loginname
     * @param String $password
     * @param String $expire_time
     * @return Bool
     */
    function setLogin($loginname,$password,$expire_time = 0){

        $member_info = $this->_get_minfo_by_loginname($loginname);

        if($member_info && $member_info[$this->config['db_upass']] == $password){

            $this->LOGIN_FLAG = true;

            $this->M_info = $member_info;
            
            $my_auth = $this->authcode(
                $password."\t".$member_info[$this->config['db_uid']],
                'ENCODE',
                md5($this->config['auth_key'])
            );
            
            setcookie($this->config['cookie_name'],$my_auth,$expire_time,$this->config['cookie_path'],$this->config['cookie_domain']);
            return true;
        }else{
            return false;
        }
    }

    function clearLogin(){
        setcookie($this->config['cookie_name'],'',- 86400 * 365,$this->config['cookie_path'],$this->config['cookie_domain']);
    }

    /**
     * 认证加密
     *
     * @param String $string
     * @param String $operation
     * @param String $key
     * @param Int $expiry
     * @return String
     */
     function authcode($string, $operation = 'DECODE', $key, $expiry = 0) {
         $ckey_length = 4;
         $key = md5($key);
         $keya = md5(substr($key, 0, 16));
         $keyb = md5(substr($key, 16, 16));
         $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

         $cryptkey = $keya.md5($keya.$keyc);
         $key_length = strlen($cryptkey);

         $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
         $string_length = strlen($string);

         $result = '';
         $box = range(0, 255);

         $rndkey = array();
         for($i = 0; $i <= 255; $i++) {
             $rndkey[$i] = ord($cryptkey[$i % $key_length]);
         }

         for($j = $i = 0; $i < 256; $i++) {
             $j = ($j + $box[$i] + $rndkey[$i]) % 256;
             $tmp = $box[$i];
             $box[$i] = $box[$j];
             $box[$j] = $tmp;
         }

         for($a = $j = $i = 0; $i < $string_length; $i++) {
             $a = ($a + 1) % 256;
             $j = ($j + $box[$a]) % 256;
             $tmp = $box[$a];
             $box[$a] = $box[$j];
             $box[$j] = $tmp;
             $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
         }

         if($operation == 'DECODE') {
             if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                 return substr($result, 26);
             } else {
                 return '';
             }
         } else {
             return $keyc.str_replace('=', '', base64_encode($result));
         }

     }
}