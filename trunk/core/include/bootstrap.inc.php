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
 * 载入语言
 */
function lang() {
    global $templatelangs;
    $varr = func_get_args();
    $var = array_shift($varr);
    if(isset($GLOBALS['language'][$var])) {
        return vsprintf($GLOBALS['language'][$var],$varr);
    } else {
        $vars = explode(':', $var);
        if(count($vars) != 2) {
            return "!$var!";
        }
        if(!in_array($vars[0], $GLOBALS['templatelangs']) && empty($templatelang[$vars[0]])) {
            @include_once ROOTDIR.'plugins/'.$vars[0].'/lang/'.LANGSET.'.lang.php';
        }
        if(!isset($GLOBALS['templatelangs'][$vars[0]][$vars[1]])) {
            return "!$var!";
        } else {
            return vsprintf($GLOBALS['templatelangs'][$vars[0]][$vars[1]],$varr);
        }
    }
    return $var;
}

/*function call_plugin(){
    $varr = func_get_args();
    //$plugin_name = array_shift($varr);
    $plugin =& loader::lib('plugin');
    //$plugin->trigger($plugin_name,$varr);
    return call_user_func_array(array($plugin,'trigger'),$varr);
}*/
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
    
    $plugin =& loader::lib('plugin');
    $plugin->init_plugins();
    
    $uri =& loader::lib('uri');
    $uriinfo = $uri->parse_uri();
    
    
    
    $output =& loader::lib('output');
    $output->set('base_path',$base_path);
    $output->set('js_path',$base_path.'statics/js/');
    
    $output->set('site_name','我的相册');
    
    $head_str = "<title>美优相册系统2.0</title>\n";
    $head_str .= "<meta name=\"description\" content=\"美优相册系统是一个单用户的在线相册管理工具。\" />\n";
    $head_str .= "<meta name=\"keywords\" content=\"相册,php\" />\n";
    $output->set('meu_head',loader::lib('plugin')->filter('meu_head',$head_str));
    
    
    
    //loader::model('setting')->set_conf('system.current_theme','1');
    //loader::model('setting')->set_conf('system.current_theme_style','default');
    define('IN_CTL',$uriinfo['ctl']);
    define('IN_ACT',$uriinfo['act']);
    $custom_page = $plugin->trigger('custom_page.'.IN_CTL.'.'.IN_ACT,$uriinfo['pars']);
    //var_dump($custom_page);
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
}
?>