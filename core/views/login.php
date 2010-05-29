<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>美优相册管理系统</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<link rel="stylesheet" href="img/login.css" type="text/css" />
<script language="javascript">
function fEvent(sType,oInput){
		switch (sType){
			case "focus" :
				oInput.isfocus = true;
				oInput.style.backgroundColor='#FFFFD8';
			case "mouseover" :
				oInput.style.borderColor = '#298CBA';
				break;
			case "blur" :
				oInput.isfocus = false;
				oInput.style.backgroundColor="";
			case "mouseout" :
				if(!oInput.isfocus){
					oInput.style.borderColor='#CDDFE4';
				}
				break;
		}
}
</script>

</head>
<body>

<div class="login">
    <div class="login_center">
            <div id="messages">
            <?php switch($res->get('flag')){
               case '1':
                    echo '用户名密码错误！';
                    break;
               case '2':
                    echo '您已成功退出！';
                    break;
               default:
                    echo '您好，欢迎登录美优相册管理系统！';
            }?>
            
            <br/>
        </div>
                
        <div id="logo">
        </div>
        <form method="post" target="_top" action="index.php?ctl=default&act=login" name="login_soft">
            <p class="label">帐 号：<br /><input type="text" class="login_input" name="loginname" onMouseOver="fEvent('mouseover',this)" onFocus="fEvent('focus',this)" onBlur="fEvent('blur',this)" onMouseOut="fEvent('mouseout',this)"></p>

            <p class="label">密 码：<br /><input type="password" class="login_input" name="operatorpw" onMouseOver="fEvent('mouseover',this)" onFocus="fEvent('focus',this)" onBlur="fEvent('blur',this)" onMouseOut="fEvent('mouseout',this)"></p>
            <p class="label"> <input type="checkbox" name="remember" value="1" />记住密码 <input type="submit" class="login_botton" name="submit" value="登录"></p>
        </form>
        <script type="text/javascript">
        window.document.login_soft.loginname.focus();
        </script>
    </div>
</div>

</body>
</html>