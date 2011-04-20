<?php
/**
 * $Id:index.php 93 2011-1-07 15:35:32Z lingter $
 * 
 * @author : Lingter
 * @support : http://meiupic.meiu.cn
 * @copyright : (c)2011 meiu.cn lingter@gmail.com
 */
define('IN_MEIU',true);

define('VERSION','2.0');

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
            @include ROOTDIR.'plugins'.DIRECTORY_SEPARATOR.$vars[0].DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.LANGSET.'.lang.php';
            if(isset($language)){
                $GLOBALS['templatelangs'][$vars[0]] = $language;
            }
        }
        if(!isset($GLOBALS['templatelangs'][$vars[0]][$vars[1]])) {
            return "!$var!";
        } else {
            return vsprintf($GLOBALS['templatelangs'][$vars[0]][$vars[1]],$varr);
        }
    }
    return $var;
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
    }
    else {
        $base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $base_url = $base_root .= '://'. $_SERVER['HTTP_HOST'];
        if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
          $base_path = "/$dir";
          $base_path .= '/';
          $base_url .= $base_path;
        }
        else {
          $base_path = '/';
        }
    }
}

function timezone_set($timeoffset = 8) {
	if(function_exists('date_default_timezone_set')) {
		@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	}
}


function init_defines(){
    $Config =& loader::config();
    if(isset($Config['img_engine']) && in_array($Config['img_engine'],array('imagick','gd'))){
        define('IMG_ENGINE',$Config['img_engine']);
    }else{
        define('IMG_ENGINE','gd');
    }
    
    timezone_set($Config['timeoffset']);
}

function init_template(){
    $current_theme = loader::model('setting')->get_conf('system.current_theme','1');
    define('TEMPLATEID', $current_theme);
    $themeinfo = loader::model('template')->info($current_theme);
    if($themeinfo){
        $GLOBALS['THEME_CONFIG'] = unserialize($themeinfo['config']);
        define('TPLDIR','themes/'.$themeinfo['cname']);
    }else{
        define('TPLDIR','themes/default');
    }
}

function meiu_bootstrap(){
    global $base_url, $base_path, $base_root, $language,$templatelangs;
    timer_start('page');
    require_once(COREDIR.'lang'.DIRECTORY_SEPARATOR.LANGSET.'.lang.php');
    require_once(COREDIR.'loader.php');
    require_once(INCDIR.'functions.php');
    
    unset_globals();
    init_defines();
    boot_init();
    init_template();
    $templatelangs=array();
    
    $plugin =& loader::lib('plugin');
    $plugin->init_plugins();
    
    $uri =& loader::lib('uri');
    $uriinfo = $uri->parse_uri();
    
    $output =& loader::lib('output');
    $output->set('base_path',$base_path);
    $output->set('statics_path',$base_path.'statics/');
    $output->set('site_name',loader::model('setting')->get_conf('system.site_name','我的相册'));
    
    $user =& loader::model('user');
    $output->set('loggedin',$user->loggedin());
    define('IN_CTL',$uriinfo['ctl']);
    define('IN_ACT',$uriinfo['act']);
    $_GET = array_merge($_GET,$uriinfo['pars']);
    $_REQUEST = array_merge($_REQUEST,$uriinfo['pars']);
    
    require_once(INCDIR.'pagecore.php');
    $custom_page = $plugin->trigger('custom_page.'.IN_CTL.'.'.IN_ACT) || $plugin->trigger('custom_page.'.IN_CTL,IN_ACT);

    if($custom_page === false){
        if(file_exists(CTLDIR.$uriinfo['ctl'].'.ctl.php')){
            require_once(CTLDIR.$uriinfo['ctl'].'.ctl.php');

            $controller_name = $uriinfo['ctl'].'_ctl';
            $controller = new $controller_name();
            $controller->_init();
            if(method_exists($controller,$uriinfo['act'])){
                call_user_func(array($controller,$uriinfo['act']));
            }else{
                exit('404');
            }
            $controller->_called();
        }else{
            exit('404');
        }
    }
}
?>