<?php

class default_ctl extends pagecore{
    
    function index($par=array()){
        $url = loader::lib('uri')->mk_uri('album');
        echo $url;
        header('Location: '.$url);
        exit;
    }
}