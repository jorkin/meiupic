<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
 
function &db($name = 'default',$config = ''){
    global $db_config;
    static $database = array();
    
    if(!isset($database[$name])){
        
        if($name == 'default'){
            $config = $db_config;
        }
        
        require_once (LIBDIR.'db.class.php');
        $database[$name] =& new db($config);
    }
    return $database[$name];
}

function &load_model($model){
    static $models = array();
    
    if(!isset($models[$model])){
        $modelPath = MODELDIR.$model.'.php';
        if(file_exists($modelPath)) {
            require_once(INCDIR.'modelfactory.php');
            require_once($modelPath);
        }else{ 
            exit('Can not load model:'.$model);
        }

        $models[$model] =& new $model;
    }
    return $models[$model];
}

function &get_output(){
    static $output = array();
    
    if(!isset($output[0])){
        require_once (LIBDIR.'output.class.php');
        $output[0] =& new output(); 
    }
    return $output[0];
}

function run(){
    global $setting;
    $ctl = isset($_GET['ctl'])?$_GET['ctl']:'default';
    $act = isset($_GET['act'])?$_GET['act']:'index';
    
    if(!$setting['open_photo'] && $ctl != 'photo' && $act != 'resize'){
        header('Location: admin.php');
        exit;
    }
    
    if(file_exists(CTLDIR.'front/'.$ctl.'.php')){
        require_once(INCDIR.'frontpage.php');
        require_once(CTLDIR.'front/'.$ctl.'.php');
        define('IN_CTL',$ctl);
        define('IN_ACT',$act);
        $controller = new controller();
        if(is_callable(array(&$controller,$act))){
            call_user_func(array(&$controller,$act));
        }else{
            showInfo('404 not found！',false);
        }
    }else{
        showInfo('404 not found！',false);
    }
}

function run_admin(){
    $ctl = isset($_GET['ctl'])?$_GET['ctl']:'default';
    $act = isset($_GET['act'])?$_GET['act']:'index';
    
    if(file_exists(CTLDIR.'admin/'.$ctl.'.php')){
        require_once(INCDIR.'adminpage.php');
        require_once(CTLDIR.'admin/'.$ctl.'.php');
        define('IN_CTL',$ctl);
        define('IN_ACT',$act);
        $controller = new controller();
        if(is_callable(array(&$controller,$act))){
            call_user_func(array(&$controller,$act));
        }else{
            showInfo('404 not found！',false);
        }
    }else{
        showInfo('404 not found！',false);
    }
}