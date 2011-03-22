<?php

class default_ctl extends pagecore{
    
    function index(){
        $url = site_link('albums');
        header('Location: '.$url);
        exit;
    }
}