<?php
class template_mdl extends modelfactory {
    
    function info($template_id){
        $this->db->select('#themes','*','id='.intval($template_id));
        return $this->db->getRow();
    }
}