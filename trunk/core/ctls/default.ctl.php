<?php

class default_ctl extends pagecore{
    
    function index(){
        $this->setting->set_conf('system.gravatar_url','http://www.gravatar.com/avatar.php?rating=G&size=48&default=http://localhost/meiupic/statics/img/no_avatar.jpg&gravatar_id={idstring}');

        $url = site_link('albums');
        redirect($url);
    }
}