<?php

class default_ctl extends pagecore{
    
    function index($par){
        $url = loader::lib('uri')->mk_uri('albums');
        header('Location: '.$url);
        exit;
    }
}