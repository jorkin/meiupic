<?php

function get_remote($url,$timeout = 15, $limit = 0, $post = '', $cookie = '', $ip = '',  $block = TRUE){
    if(function_exists('fsockopen') || function_exists('pfsockopen')){
        return socket_get_content($url, $timeout , $limit , $post , $cookie , $ip,  $block);
    }else{
        $ctx = null;
        if($timeout>0){
            if(function_exists('stream_context_create')){
                if($post){
                    $data = http_build_query($post, '', '&');
                    $par = array(
                        'http' => array(
                            'method'=>'POST',
                            'timeout'=>$timeout,
                            'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
                            "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n".
                            "Content-Length: " . strlen($data) . "\r\n".
                            "Cookie: $cookie\r\n",
                            'content' => $data,
                        )
                    );
                }else{
                    $par = array(
                        'http' => array(
                            'method'=>'GET',
                            'timeout'=>$timeout,
                            'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
                            "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n".
                            "Cookie: $cookie\r\n",
                        )
                    );
                }
                $ctx = stream_context_create($par);
            }
        }
        $result = @file_get_contents($url,false,$ctx);
        return $result;
    }
}

function socket_get_content($url, $timeout = 15, $limit = 0, $post = '', $cookie = '', $ip = '',  $block = TRUE) {
    $return = '';
    $matches = parse_url($url);
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].(isset($matches['query']) && $matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;

    if($post) {
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: '.strlen($post)."\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }

    if(function_exists('fsockopen')) {
        $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    } elseif (function_exists('pfsockopen')) {
        $fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    } else {
        $fp = false;
    }

    if(!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if(!$status['timed_out']) {
            while (!feof($fp)) {
                if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }

            $stop = false;
            while(!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        @fclose($fp);
        return $return;
    }
}