<?php

class album_ctl extends pagecore{
    
    function index($par=array()){
        $page = isset($par['page'])?$par['page']:1;
        
        $pageurl = loader::lib('uri')->mk_uri('album','index',array('page'=>'[#page#]'));//'index.php?page=[#page#]';
        
        $mdl_album = & loader::model('album');
        $albums = $mdl_album->get_all_album($page,true);
        if(is_array($albums['ls']))
            foreach($albums['ls'] as $k=>$v){
                $albums['ls'][$k]['album_control_icons'] = loader::lib('plugin')->filter('album_control_icons','<a href="#">Edit</a>',$v['id']);
            }
        
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',loader::lib('page')->fetch($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->output->set('current_nav','index');
        
        loader::view('albums');
    }
}