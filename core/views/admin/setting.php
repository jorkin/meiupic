<?php include('head.php');?>
<?php $setting = $res->get('setting');?>

<div id="setting_box">
    <div id="setting_nav">
        <?php include('setting_nav.php'); ?>
    </div>
    <div id="setting_body">
        <form method="post" action="admin.php?ctl=setting">
        <div id="base_setting" class="stab">
        <h1 class="album_title1">基本设置</h1>
        <table>
            <tbody>
                <tr>
                    <td class="tt">站点名称：</td><td class="tc"><input name="setting[site_title]" class="txtinput" type="text" value="<?php echo $setting['site_title'];?>" style="width:250px" /></td><td class="ti">前台显示的TITLE</td>
                </tr>
                <tr>
                    <td class="tt">站点关键字：</td><td class="tc"><input name="setting[site_keyword]" class="txtinput" type="text" value="<?php echo $setting['site_keyword'];?>" style="width:250px" /></td><td class="ti">前台META KEYWORD，关键字使用空格或,分割</td>
                </tr>
                <tr>
                    <td class="tt">站点描述：</td><td class="tc"><input name="setting[site_description]" class="txtinput" type="text" value="<?php echo $setting['site_description'];?>" style="width:250px" /></td><td class="ti">前台META DESCRIPTION</td>
                </tr>
            <tr>
                <td class="tt">相册URL：</td><td class="tc"><input name="setting[url]" class="txtinput" type="text" value="<?php echo $setting['url'];?>" style="width:250px" /></td><td class="ti">设置复制图片地址的URL前缀, 需要带上末尾的"/"</td>
            </tr>
            <tr>
                <td class="tt">按需生成各种尺寸图片：</td>
                <td class="tc">
                    <input name="setting[demand_resize]" type="checkbox" value="1" <?php if($setting['demand_resize']){ echo 'checked="checked"';} ?> /> 
                    <?php 
                    if(function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules())){
                        echo '<span class="green">您的服务器支持此功能！</span>';
                    }else{
                        echo '<span class="red">您的服务器不支持此功能！</span>';
                    }?>
                </td>
                <td class="ti">开启此项需要支持Rewrite,关闭此项则会在上传时生成各种尺寸图片</td>
            </tr>
            </tbody>
        </table>
        </div>
        <div id="upload_setting" class="stab">
        <h1 class="album_title1">上传设置</h1>
        <table>
            <tbody>
            <tr>
                <td class="tt">是否开启客户端预处理：</td><td class="tc"><input id="setting_open_pre_resize" name="setting[open_pre_resize]" type="checkbox" value="1" <?php if($setting['open_pre_resize']){ echo 'checked="checked"';} ?> onclick="switch_div(this,'imgsetting_div');" /></td><td class="ti">在客户端预处理可以大大减少网络传输，缩短上传时间。开启后无法获取照片EXIF信息</td>
            </tr>
            </tbody>
            <tbody id="imgsetting_div">
            <tr>
                <td class="tt">图片宽：</td><td class="tc"><input name="setting[resize_img_width]" class="txtinput" type="text" value="<?php echo $setting['resize_img_width'];?>" style="width:50px" /></td><td class="ti">客户端预处理图片的最大宽度</td>
            </tr>
            <tr>
                <td class="tt">图片高：</td><td class="tc"><input name="setting[resize_img_height]" class="txtinput" type="text" value="<?php echo $setting['resize_img_height'];?>" style="width:50px" /></td><td class="ti">客户端预处理图片的最大高度</td>
            </tr>
            <tr>
                <td class="tt">图片质量：</td><td class="tc"><input name="setting[resize_quality]" class="txtinput" type="text" value="<?php echo $setting['resize_quality'];?>" style="width:50px" /></td><td class="ti">预处理图片的质量 1-100</td>
            </tr>
            </tbody>
            <script>
            if($('#setting_open_pre_resize').get(0).checked){
                $("#imgsetting_div").show();
            }else{
                $("#imgsetting_div").hide();
            }
            </script>
            <tbody>
            <tr>
                <td class="tt">上传子目录形式：</td><td class="tc">
                    <select name="setting[imgdir_type]">
                        <option value="1" <?php if($setting['imgdir_type']=='1') echo 'selected="selected"';?>>YYYYMMDD</option>
                        <option value="2" <?php if($setting['imgdir_type']=='2') echo 'selected="selected"';?>>YYYYMM</option>
                    </select></td><td class="ti">如：data/20100520/xxxx.jpg</td>
            </tr>
            <tr>
                <td class="tt">普通上传允许的图片大小：</td><td class="tc"><input name="setting[size_allow]" class="txtinput" type="text" value="<?php echo $setting['size_allow'];?>" style="width:80px" /></td><td class="ti">单位：字节</td>
            </tr>
            </tbody>
        </table>
        </div>
        <div id="display_setting" class="stab">
        <h1 class="album_title1">显示设置</h1>
        <table>
            <tbody>
            <tr>
                <td class="tt">每页显示图片数：</td><td class="tc"><input name="setting[pageset]" class="txtinput" type="text" value="<?php echo $setting['pageset'];?>" style="width:50px" /></td><td class="ti"></td>
            </tr>
            <tr>
                <td class="tt">幻灯片图片显示限制：</td><td class="tc"><input name="setting[gallery_limit]" class="txtinput" type="text" value="<?php echo $setting['gallery_limit'];?>" style="width:50px" /></td><td class="ti"></td>
            </tr>
            </tbody>
        </table>
        </div>
        <div id="priv_setting" class="stab">
        <h1 class="album_title1">权限设置</h1>
        <table>
            <tbody>
                <tr>
                    <td class="tt">开放相册：</td><td class="tc"><input id="setting_open_photo" name="setting[open_photo]" type="checkbox" value="1" <?php if($setting['open_photo']){ echo 'checked="checked"';} ?>   /></td><td class="ti"></td>
                </tr>
                <tr>
                    <td class="tt">开启防盗链：</td><td class="tc"><input id="setting_access_ctl" name="setting[access_ctl]" type="checkbox" value="1" <?php if($setting['access_ctl']){ echo 'checked="checked"';} ?>   /></td><td class="ti"></td>
                </tr>
                <tr>
                    <td class="tt">允许的域名列表：</td><td class="tc"><textarea name="setting[access_domain]" style="margin-left: 4px; width:300px; height: 100px;"><?php echo $setting['access_domain'];?></textarea></td><td class="ti">一行一条记录</td>
                </tr>
                <tr>
                    <td></td><td><input type="submit" class="btn" value="保存设置" /></td><td></td>
                </tr>
            </tbody>
        </table>
        </div>
        </form>
    </div>
</div>

<?php include('foot.php');?>