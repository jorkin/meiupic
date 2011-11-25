<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010-2011 meiu.cn lingter@gmail.com
 */
 
class user_mdl extends modelfactory{
    
    var $cookie_name = 'MPIC_AUTH';
    var $cookie_auth_key = 'key1234';
    var $cookie_domain = '';
    var $uinfo = array();
    var $LOGIN_FLAG = false;
    var $table_name = '#@users';
    
    function user_mdl(){
        parent::modelfactory();
        $config =& loader::config();
        
        $this->cookie_name = $config['cookie_name'];
        $this->cookie_auth_key = $config['cookie_auth_key'];
        $this->cookie_domain = $config['cookie_domain'];
        
        if(isset($_COOKIE[$this->cookie_name])){

            $auth = $this->authcode(
                $_COOKIE[$this->cookie_name],
                'DECODE', 
                md5($this->cookie_auth_key)
            );

            $auth = explode("\t",$auth);
            $uid = isset($auth[1])?$auth[1]:0;
            $upass = isset($auth[0])?$auth[0]:'';
            $this->db->select('#@users','*',"id=".intval($uid));
            $uinfo =  $this->db->getRow();
            if(!$uinfo){
                $this->LOGIN_FLAG = false;
            }else{
                if($uinfo['user_pass'] == $upass){    
                    $this->LOGIN_FLAG = true;
                    $this->uinfo = $uinfo;
                }else{
                    $this->LOGIN_FLAG = false;
                }
            }
        }else{
            $this->LOGIN_FLAG = false;
        }
    }
    
    function get_info_by_name($name){
        $this->db->select('#@users','*',"user_name=".$this->db->q_str($name));
        return $this->db->getRow();
    }
    
    function check_pass($uid,$pass){
        $info = $this->get_info($uid);
        if(!$info){
            return false;
        }
        if($info['user_pass'] != $pass){
            return false;
        }
        return true;
    }
    
    /**
     * 判断用户是否登陆
     *
     * @return Bool
     */
    function loggedin(){
        return $this->LOGIN_FLAG;
    }
    
    /**
     * 获取用户信息
     *
     * @param String $key
     * @param String $default
     * @return String
     */
    function get_field($key,$default = '') {
        return isset ($this->uinfo[$key]) ? $this->uinfo[$key] : $default;
    }
    
    function get_all_field(){
        return $this->uinfo;
    }
    
    /**
     * 设置用户登陆
     *
     * @param String $loginname
     * @param String $password
     * @param String $expire_time
     * @return Bool
     */
    function set_login($login_name,$password,$expire_time = 0){
        
        $uinfo = $this->get_info_by_name($login_name);

        if($uinfo && $uinfo['user_pass'] == $password){

            $this->LOGIN_FLAG = true;

            $this->uinfo = $uinfo;
            
            $my_auth = $this->authcode(
                $password."\t".$uinfo[$this->id_col],
                'ENCODE',
                md5($this->cookie_auth_key)
            );
            @ob_clean();
            setcookie($this->cookie_name,$my_auth,$expire_time,'/',$this->cookie_domain);
            return true;
        }else{
            return false;
        }
    }

    function clear_login(){
        @ob_clean();
        setcookie($this->cookie_name,'',- 86400 * 365,'/',$this->cookie_domain);
    }
    
    function save_extra($id,$extra){
        if(is_array($extra)){
            foreach($extra as $k => $v){
                $this->db->select('#@usermeta','meta_value','userid='.intval($id).' and meta_key='.$this->db->q_str($k));
                $row = $this->db->getRow();
                if($row){
                    $this->db->update('#@usermeta','userid='.intval($id).' and meta_key='.$this->db->q_str($k),array('meta_value'=>$v));
                }else{
                    $this->db->insert('#@usermeta',array('userid'=>intval($id),'meta_key'=>$k,'meta_value'=>$v));
                }
                $this->db->query();
            }
            $cache =& loader::lib('cache');
            $cache->remove('user_extra_'.$id);
        }
    }
    
    function get_extra($id){
        $cache =& loader::lib('cache');
        $value = $cache->get('user_extra_'.$id);
        if($value){
            return $value;
        }
        $this->db->select('#@usermeta','meta_key,meta_value','userid='.intval($id));
        $value = $this->db->getAssoc();
        $cache->set('user_extra_'.$id,$value);
        return $value;
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