<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $res->get('site_title');?></title>
<meta name="description" content="<?php echo $res->get('site_description');?>" />
<meta name="keywords" content="<?php echo $res->get('site_keyword');?>" />
<link rel="stylesheet" href="img/home.css" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
</head>

<body>
<div class="header">
    <div class="header_box">
        <h1 class="logo"><img src="img/logo.png" width="170" height="57" /></h1>
        
        <ul class="nav">
            <li class="first<?php if ($res->get('current_nav')=='index'): ?> now<?php endif; ?>"><a href="index.php"><span>首页</span></a> <sub></sub></li>
            <li <?php if ($res->get('current_nav')=='album'): ?>class="now"<?php endif; ?>><a href="index.php?ctl=album"><span>相册</span></a> <sub></sub></li>
            <li <?php if ($res->get('current_nav')=='newphotos'): ?>class="now"<?php endif; ?>><a href="index.php?ctl=default&act=newphotos"><span>最新上传</span></a> <sub></sub></li>
            <li class="last"><a href="admin.php"><span>管理相册</span></a> <sub></sub></li>
        </ul>
    </div>
</div>