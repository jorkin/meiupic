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
        <div id="display_setting" class="stab">
        <h1 class="album_title1">图片设置</h1>
        <table>
            <tbody>
            <tr>
                <td class="tt">使用水印：</td><td class="tc"><input id="setting_open_watermark" name="setting[open_watermark]" type="checkbox" value="1" <?php if($setting['open_watermark']){ echo 'checked="checked"';} ?> onclick="switch_div(this,'watermark_div');" /></td><td class="ti"></td>
            </tr>
            </tbody>
            <tbody id="watermark_div">
            <tr>
                <td class="tt">水印图片路径：</td><td class="tc"><input name="setting[watermark_path]" class="txtinput" type="text" value="<?php echo $setting['watermark_path'];?>" style="width:250px" /></td><td class="ti">如data/water.png，水印图片只支持png,gif,jpg</td>
            </tr>
            <tr>
                <td class="tt">水印位置：</td><td class="tc">
                <table>
                    <tr>
                        <td><input type="radio" name="setting[watermark_pos]" value="1" <?php if($setting['watermark_pos']==1){ echo 'checked="checked"';} ?> /> #1</td><td><input type="radio" name="setting[watermark_pos]" value="2" <?php if($setting['watermark_pos']==2){ echo 'checked="checked"';} ?> /> #2</td><td><input type="radio" name="setting[watermark_pos]" value="3" <?php if($setting['watermark_pos']==3){ echo 'checked="checked"';} ?> /> #3</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="setting[watermark_pos]" value="4" <?php if($setting['watermark_pos']==4){ echo 'checked="checked"';} ?> /> #4</td><td><input type="radio" name="setting[watermark_pos]" value="5" <?php if($setting['watermark_pos']==5){ echo 'checked="checked"';} ?> /> #5</td><td><input type="radio" name="setting[watermark_pos]" value="6" <?php if($setting['watermark_pos']==6){ echo 'checked="checked"';} ?> /> #6</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="setting[watermark_pos]" value="7" <?php if($setting['watermark_pos']==7){ echo 'checked="checked"';} ?> /> #7</td><td><input type="radio" name="setting[watermark_pos]" value="8" <?php if($setting['watermark_pos']==8){ echo 'checked="checked"';} ?> /> #8</td><td><input type="radio" name="setting[watermark_pos]" value="9" <?php if($setting['watermark_pos']==9){ echo 'checked="checked"';} ?> /> #9</td>
                    </tr>
                    <tr>
                        <td colspan="3"><input type="radio" name="setting[watermark_pos]" value="0" <?php if($setting['watermark_pos']==0){ echo 'checked="checked"';} ?> /> 随机</td>
                    </tr>
                </table>
                </td><td class="ti"></td>
            </tr>
            </tbody>
        </table>
        </div>
        <script>
            if($('#setting_open_watermark').get(0).checked){
                $("#watermark_div").show();
            }else{
                $("#watermark_div").hide();
            }
       </script>
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
                    <td class="tt">允许的域名列表：</td><td class="tc"><textarea name="setting[access_domain]" style="margin-left: 4px; width:300px; height: 100px;"><?php echo $setting['access_domain'];?></textarea></td><td class="ti">一行一条域名，请勿带上前面的http://</td>
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