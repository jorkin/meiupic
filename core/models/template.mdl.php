<?php
class template_mdl extends modelfactory {
    
    function info($template_id){
        $cache =& loader::lib('cache');
        $info = $cache->get('theme_info_'.$template_id);
        if($info === false){
            $this->db->select('#@themes','*','id='.intval($template_id));
            $info = $this->db->getRow();
            $cache->set('theme_info_'.$template_id,$info);
        }
        return $info;
    }
}