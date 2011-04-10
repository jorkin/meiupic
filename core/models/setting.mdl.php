<?php
/**
 * $Id: setting.mdl.php 56 2010-07-09 08:13:40Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010-2011 meiu.cn lingter@gmail.com
 */
class setting_mdl extends modelfactory {
    
    var $setting_pool=array();
    var $_regSave = false;
    
    function get_conf($key,$default=null){
        $key = preg_replace("/([^a-zA-Z0-9_\-\.]+)/e","", $key);
        $key_arr = explode('.',$key);
        $k = array_shift($key_arr);
        if(isset($this->setting_pool[$k])){
            $value = $this->setting_pool[$k];
        }else{
            $cache =& loader::lib('cache');
            $value = $cache->get('setting_'.$k);
            if(!$value){
                $this->db->select('#@setting','value','name="'.$k.'"');
                $data = $this->db->getOne();
                if($data){
                    $value = unserialize($data);
                }else{
                    return false;
                }
                $this->setting_pool[$k] = $value;
                $cache->set('setting_'.$k,$value);
            }
        }
        foreach($key_arr as $v){
            if(isset($value[$v])){
                $value = $value[$v];
            }else{
                return $default;
            }
        }
        return $value;
    }
    
    function set_conf($key,$value,$immediately=false){
        $key = preg_replace("/([^a-zA-Z0-9_\-\.]+)/e","", $key);

        $key_arr = explode('.',$key);
        $k = array_shift($key_arr);

        $eval_str = "\$this->setting_pool[\$k]";
        foreach($key_arr as $val){
            $eval_str .= '["'.$val.'"]';
        }
        $eval_str .= " = \$value;";
        eval($eval_str);

        if($immediately){
            $this->_save();
            return true;
        }else{
            if(!$this->_regSave){
                register_shutdown_function(array(&$this,'_save'));
                $this->_regSave = true;
            }
            return true;
        }
    }
    
    
    function _save(){
        $cache =& loader::lib('cache');
        foreach($this->setting_pool as $k=>$values){
            $this->db->select('#@setting','value','name="'.$k.'"');
            $data = $this->db->getOne();
            if($data){
                $keyvalue = unserialize($data);
                $keyvalue = array_merge($keyvalue,$values);
                $this->db->update('#@setting','name="'.$k.'"',array('value'=>serialize($keyvalue)));
                $this->db->query();
            }else{
                $this->db->insert('#@setting',array('name'=>$k,'value'=>serialize($values)));
                $this->db->query();
            }
            $cache->remove('setting_'.$k);
        }
    }
}