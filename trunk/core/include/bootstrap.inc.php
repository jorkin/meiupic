<?php
/**
 * $Id:index.php 93 2011-1-07 15:35:32Z lingter $
 * 
 * @author : Lingter
 * @support : http://meiupic.meiu.cn
 * @copyright : (c)2011 meiu.cn lingter@gmail.com
 */
define('IN_MEIU',true);

if (PHP_VERSION >= "5.1.0") {
	date_default_timezone_set ( 'Asia/Shanghai' );
}
header("Content-type: text/html; charset=utf-8");

if (floor(PHP_VERSION) < 5){
    define('PHPVer',4);
}else{
    define('PHPVer',5);
}

/**
 * 开始计时器
 *
 * @param name
 *   计时器的名字
 */
function timer_start($name) {
  global $timers;

  list($usec, $sec) = explode(' ', microtime());
  $timers[$name]['start'] = (float)$usec + (float)$sec;
  $timers[$name]['count'] = isset($timers[$name]['count']) ? ++$timers[$name]['count'] : 1;
}

/**
 * 读取当前所用时间，但是并不停止计数器.
 *
 * @param name
 *   计时器的名字
 * @return
 *   当前消耗时间,单位微秒
 */
function timer_read($name) {
  global $timers;

  if (isset($timers[$name]['start'])) {
    list($usec, $sec) = explode(' ', microtime());
    $stop = (float)$usec + (float)$sec;
    $diff = round(($stop - $timers[$name]['start']) * 1000, 2);

    if (isset($timers[$name]['time'])) {
      $diff += $timers[$name]['time'];
    }
    return $diff;
  }
}

/**
 * 计时器停止.
 *
 * @param name
 *   计时器名字.
 * @return
 *   返回计时器数组.
 */
function timer_stop($name) {
  global $timers;

  $timers[$name]['time'] = timer_read($name);
  unset($timers[$name]['start']);

  return $timers[$name];
}

/**
 * 销毁所有不允许的全局变量
 */
function unset_globals() {
  if (ini_get('register_globals')) {
    $allowed = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
    foreach ($GLOBALS as $key => $value) {
      if (!isset($allowed[$key])) {
        unset($GLOBALS[$key]);
      }
    }
  }
}
/**
 * 启动初始化
 *
 */
function boot_init(){
    global $base_url, $base_path, $base_root,$timestamp,$tplrefresh;
    $timestamp = time();
    $tplrefresh = 1;
    
    if (isset($base_url)) {
        $parts = parse_url($base_url);
        if (!isset($parts['path'])) {
          $parts['path'] = '';
        }
        
        if($dir = trim($parts['path'],'\,/')){
            $base_path = '/'.$dir.'/';
        }else{
            $base_path = '/';
        }
        
        $base_root = substr($base_url, 0, strlen($base_url) - strlen($parts['path']));
        echo $base_path;
    }
    else {
        $base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $base_url = $base_root .= '://'. $_SERVER['HTTP_HOST'];
        if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
          $base_path = "/$dir";
          $base_url .= $base_path;
          $base_path .= '/';
        }
        else {
          $base_path = '/';
        }
    }
}


function meiu_bootstrap(){
    global $base_url, $base_path, $base_root, $language,$templatelangs;
    timer_start('page');
    require_once(COREDIR.'lang/'.LANGSET.'.lang.php');
    require_once(COREDIR.'loader.php');
    require_once(INCDIR.'modelfactory.php');
    unset_globals();
    boot_init();
    
    $templatelangs=array();
    
    $uri =& loader::lib('uri');
    $uriinfo = $uri->parse_uri();
    
    $plugin =& loader::lib('plugin');
    
    
    //loader::model('setting')->set_conf('system.current_theme','1');
    //loader::model('setting')->set_conf('system.current_theme_style','default');
    define('IN_CTL',$uriinfo['ctl']);
    define('IN_ACT',$uriinfo['act']);
    $custom_page = $plugin->trigger('custom_page',$uriinfo['pars']);
    if($custom_page === false){
        if(file_exists(CTLDIR.$uriinfo['ctl'].'.ctl.php')){
            require_once(INCDIR.'pagecore.php');
            require_once(CTLDIR.$uriinfo['ctl'].'.ctl.php');
           
            $controller_name = $uriinfo['ctl'].'_ctl';
            $controller = new $controller_name();
        
            if(method_exists($controller,$uriinfo['act'])){
                call_user_func_array(array($controller,$uriinfo['act']),array($uriinfo['pars']));
            }else{
                exit('404');
            }
        }else{
            exit('404');
        }
    }
    //echo $uri->mk_uri('photo','view',array('id'=>'33'));//'iphoto','index',array('id'=>'124','album'=>'100')
    //echo loader::model('setting')->get_conf('system.path_suffix');
    //var_dump( loader::model('setting')->get_conf('system.uri.adbc.path_suffix'));
    //echo timer_read('page');
}
?>