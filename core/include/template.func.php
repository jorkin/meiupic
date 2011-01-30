<?php
function strexists($haystack, $needle) {
    return !(strpos($haystack, $needle) === FALSE);
}
function template($file, $templateid = 0, $tpldir = '') {
    if(strexists($file, ':')) {
        list($templateid, $file) = explode(':', $file);
        $tpldir = 'plugins/'.$templateid.'/templates';
    }
    $tpldir = $tpldir ? $tpldir : TPLDIR;
    $templateid = $templateid ? $templateid : TEMPLATEID;
    $tplfile = ROOTDIR.$tpldir.'/'.$file.'.htm';
    $filebak = $file;
    $objfile = ROOTDIR.'cache/templates/'.STYLEID.'_'.$templateid.'_'.$file.'.tpl.php';
    if($templateid != 1 && !file_exists($tplfile)) {
        $tplfile = ROOTDIR.'themes/default/'.$filebak.'.htm';
    }
    @checktplrefresh($tplfile, $tplfile, filemtime($objfile), $templateid, $tpldir);

    return $objfile;
}

function checktplrefresh($maintpl, $subtpl, $timecompare, $templateid, $tpldir) {
    global $tplrefresh;
    if(empty($timecompare) || $tplrefresh == 1 || ($tplrefresh > 1 && !($GLOBALS['timestamp'] % $tplrefresh))) {
        if(empty($timecompare) || @filemtime($subtpl) > $timecompare) {
            parse_template($maintpl, $templateid, $tpldir);
            return TRUE;
        }
    }
    return FALSE;
}


function parse_template($tplfile, $templateid, $tpldir) {
    global $subtemplates, $timestamp;

    $nest = 6;
    $basefile = $file = basename($tplfile, '.htm');
    $objfile = ROOTDIR.'cache/templates/'.STYLEID.'_'.$templateid.'_'.$file.'.tpl.php';

    if(!@$fp = fopen($tplfile, 'r')) {
        dexit("Current template file '$tpldir/$file.htm' not found or have no access!");
    }

    $template = @fread($fp, filesize($tplfile));
    fclose($fp);

    $var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
    $const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

    $subtemplates = array();
    for($i = 1; $i <= 3; $i++) {
        if(strexists($template, '{subtemplate')) {
            $template = preg_replace("/[\n\r\t]*\{subtemplate\s+([a-z0-9_:]+)\}[\n\r\t]*/ies", "stripvtemplate('\\1', 1)", $template);
        }
    }

    $template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
    $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
    $template = preg_replace("/\{lang\s+(.+?)\}/ies", "stripvtags('<?php echo languagevar(\"\\1\");?>')", $template);
    $template = str_replace("{LF}", "<?php echo \"\\n\"; ?>", $template);

    $headeradd = '';
    if(!empty($subtemplates)) {
        $headeradd .= "\n0\n";
        foreach ($subtemplates as $fname) {
            $headeradd .= "|| checktplrefresh('$tplfile', '$fname', $timestamp, '$templateid', '$tpldir')\n";
        }
        $headeradd .= ';';
    }

    $template = "<?php if(!defined('IN_MEIU')) exit('Access Denied'); {$headeradd}?>\n$template";

    $template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_:]+)\}[\n\r\t]*/ies", "stripvtemplate('\\1', 0)", $template);
    $template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/ies", "stripvtemplate('\\1', 0)", $template);
    $template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<?php \\1 ?>','')", $template);
    $template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<?php echo \\1; ?>','')", $template);
    $template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "stripvtags('\\1<?php } elseif(\\2) { ?>\\3','')", $template);
    $template = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<?php } else { ?>\\2", $template);
      
    for($i = 0; $i < $nest; $i++) {
        $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<?php } } ?>')", $template);
        $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<?php } } ?>')", $template);
        $template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/ies", "stripvtags('\\1<?php if(\\2) { ?>\\3','\\4\\5<?php } ?>\\6')", $template);
    }
    
    $template = preg_replace("/\{$const_regexp\}/s", "<?php echo \\1; ?>", $template);
    $template = preg_replace("/ \?\>[\n\r]*\<\?php /s", " ", $template);
    
    $template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?php echo \\1; ?>", $template);
    $template = preg_replace("/\<\?php echo \<\?php echo $var_regexp; \?\>; \?\>/es", "addquote('<?php echo \\1; ?>')", $template);
    
    $template = preg_replace("/[\n\r\t]*\{link\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<?php echo striplink(\"\\1\"); ?>','')", $template);
    
    if(!@$fp = fopen($objfile, 'w')) {
        dexit("Directory './cache/templates/' not found or have no access!");
    }

    $template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "transamp('\\0')", $template);
    $template = preg_replace("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/ise", "stripscriptamp('\\1', '\\2')", $template);

    $template = preg_replace("/[\n\r\t]*\{block\s+([a-zA-Z0-9_]+)\}(.+?)\{\/block\}/ies", "stripblock('\\1', '\\2')", $template);

    flock($fp, 2);
    fwrite($fp, $template);
    fclose($fp);
}

function stripvtemplate($tpl, $sub) {
    $vars = explode(':', $tpl);
    $templateid = 1;
    $tpldir = '';
    if(count($vars) == 2) {
        list($templateid, $tpl) = $vars;
        $tpldir = 'plugins/'.$templateid.'/templates';
    }
    if($sub) {
        return loadsubtemplate($tpl, $templateid, $tpldir);
    } else {
        return stripvtags("<?php include template('$tpl', '$templateid', '$tpldir'); ?>", '');
    }
}

function loadsubtemplate($file, $templateid = 0, $tpldir = '') {
    global $subtemplates;
    $tpldir = $tpldir ? $tpldir : TPLDIR;
    $templateid = $templateid ? $templateid : TEMPLATEID;

    $tplfile = ROOTDIR.$tpldir.'/'.$file.'.htm';
    if($templateid != 1 && !file_exists($tplfile)) {
        $tplfile = ROOTDIR.'themes/default/'.$file.'.htm';
    }
    $content = @implode('', file($tplfile));
    $subtemplates[] = $tplfile;
    return $content;
}

function languagevar($var) {
    global $templatelangs;
    $varr = explode('|',$var);
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

function transamp($str) {
    $str = str_replace('&', '&amp;', $str);
    $str = str_replace('&amp;amp;', '&amp;', $str);
    $str = str_replace('\"', '"', $str);
    return $str;
}

function addquote($var) {
    return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}

function stripvtags($expr, $statement) {
    $expr = str_replace("\\\"", "\"", preg_replace("/\<\?php echo (\\\$.+?); \?\>/s", "\\1", $expr));
    $expr = preg_replace("/`(.+)`/U","{\\1}",$expr);
    $statement = str_replace("\\\"", "\"", $statement);
    return $expr.$statement;
}

function stripscriptamp($s, $extra) {
    $extra = str_replace('\\"', '"', $extra);
    $s = str_replace('&amp;', '&', $s);
    return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
}

function stripblock($var, $s) {
    $s = str_replace('\\"', '"', $s);
    $s = preg_replace("/<\?php echo \\\$(.+?); \?>/", "{\$\\1}", $s);
    preg_match_all("/<\?php echo (.+?); \?>/e", $s, $constary);
    $constadd = '';
    $constary[1] = array_unique($constary[1]);
    foreach($constary[1] as $const) {
        $constadd .= '$__'.$const.' = '.$const.';';
    }
    $s = preg_replace("/<\?php echo (.+?); \?>/", "{\$__\\1}", $s);
    $s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
    $s = str_replace('<?php', "\nEOF;\n", $s);
    return "<?php\n$constadd\$$var = <<<EOF\n".$s."\nEOF;\n?>";
}

function striplink($var){
    parse_str($var,$url_arr);
    $uri =& loader::lib('uri');
    $ctl = isset($url_arr['ctl'])?$url_arr['ctl']:'default';
    $act = isset($url_arr['act'])?$url_arr['act']:'index';
    unset($url_arr['ctl']);
    unset($url_arr['act']);
    
    return $uri->mk_uri($ctl,$act,$url_arr);
}

function dexit($msg){
    exit($msg);
}

?>