<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>美优相册管理系统</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<link rel="stylesheet" href="img/main.css" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" src="js/swfobject.js"></script>
</head>

<body>
<div id="info_msg" style="display:none"></div>
<div id="copyedok" style="display:none">已经拷贝到剪贴板！</div>
<div id="copytocpbord" style="display:none;">
    <input type="text" class="ipt_3" value="" /> <input id="cp_click" type="button" class="btn" value="复制到剪切板" /> <input type="button" class="btn" name="close" value="关闭" />
</div>
<div id="movetoalbum" style="display:none;">
    <span class="album"><select name="albums"></select></span>&nbsp;&nbsp; <input type="button" name="move" class="btn" value="移动" /> <input type="button" class="btn" name="close" value="关闭" />
</div>
<div id="reuploadpic" style="display:none;">
    <div class="uploading" style="display:none"><img src="img/loading.gif"></div>
    <form action="" target="_uploadpic" method="post" enctype="multipart/form-data"> <span class="upfield"><input type="file" name="imgs"></span>&nbsp;&nbsp; <input type="submit" name="upload" class="btn" value="上传" /> <input type="button" class="btn" name="close" value="关闭" />
    </form>
    <iframe name="_uploadpic" style="height:0px;width:0px;" border="0" frameborder="0"></iframe>
</div>
<div id="floatwin" class="dragable" style="display:none;">
    <h2 class="draghandle"><a href="javascript:void(0);" onclick="mydiv.Close()">关闭</a><span>浮动窗口</span></h2>
    <div id="floatContent">
    </div>
    <div id="floatFoot" style="display:none;">
    </div>
</div>

<div id="mainbox">
    <div class="headnav">
    <ul>
        <li <?php if ($res->get('current_nav')=='all'): ?>class="current"<?php endif; ?>><a href="admin.php?act=all">所有图片</a></li>
        <li <?php if ($res->get('current_nav')=='album'): ?>class="current"<?php endif; ?>><a href="admin.php?ctl=album">相册</a></li>
        <li <?php if ($res->get('current_nav')=='upload'): ?>class="current"<?php endif; ?>><a href="admin.php?ctl=upload">上传图片</a></li>
        
    </ul>
    <div class="setting"><a href="admin.php?ctl=setting">系统设置</a> <?php if($res->get('open_photo_setting')){?>  <a href="index.php">查看前台</a> <?php } ?> <a href="admin.php?ctl=default&act=logout">退出登录</a></div>
    </div>
    <div class="mainbody">
    