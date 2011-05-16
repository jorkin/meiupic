<?php

class utils_cct extends pagecore{
    
    function copyurl(){
        need_login('ajax_bubble');
        
        $url = $this->setting->get_conf('site.url');
        
        $id = $this->getGet('id');
        $photo_info = loader::model('photo')->get_info($id);
        $photo_info['path'] = $url.$photo_info['path'];
        
        $this->output->set('photo_info',$photo_info);
        
        loader::view('copyimg:copyurl');
    }
}