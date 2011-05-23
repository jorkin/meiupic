<?php

class plugin_copyimg extends plugin{
    var $config = array();
    
    var $name = '拷贝图片地址';
    var $description = '一键拷贝图片地址！';
    var $local_ver = '1.0';
    var $author_name = 'Meiu Studio';
    var $author_url = 'http://www.meiu.cn';
    var $author_email = 'lingter@gmail.com';
    
    function init(){
        $this->plugin_mgr->add_filter('photo_control_icons',array('copyimg','photo_list_page_icon'));
        $this->plugin_mgr->add_filter('meu_head',array('copyimg','html_head'),10);
        $this->plugin_mgr->add_trigger('custom_page.utils.copyurl',array('copyimg','copyurl_act'));
        
        $this->loggedin = loader::model('user')->loggedin();
    }
    
    function photo_list_page_icon($str,$album_id,$id){
        if($this->loggedin){
            return $str.'<li><a href="javascript:void(0);" onclick="Mui.bubble.show(this,\''.site_link('utils','copyurl',array('id'=>$id)).'\',true);Mui.bubble.resize(320)" title="'.lang('copyimg:copy_to_clipboard').'"><span class="i_copyclip sprite"></span></a></li>';
        }else{
            return $str;
        }
    }
    
    function copyurl_act(){
        include_once('utils.cct.php');
        $ctl = new utils_cct();
        $ctl->_init();
        $ctl->copyurl();
        $ctl->_called();
    }
    
    function html_head($str){
        global $base_path;
        $head_str = <<<eot
<script type="text/javascript" src="{$base_path}plugins/copyimg/ZeroClipboard.js"></script>
<script>
    function show_copy_notice(o,notice){
        var pos = $(o).offset();
        var width = $(o).width();
        var left = pos.left+width-80;
        var top = pos.top;
        
        if($("#copy_notice").length == 0){
            $("body").prepend('<div id="copy_notice"></div>');
        }
        $("#copy_notice").css({"left":left,"top":top});
        $("#copy_notice").html(notice).show().animate({opacity: 1.0}, 1000).fadeOut();
    }
</script>
<style>
    #copy_notice{
        position:absolute;
        z-index:1103;
        height:15px;
        width:80px;
        padding:5px;
        border:1px solid #eee;
        background:#FFFFEE;
    }
</style>
eot;
        return $str.$head_str;
    }
}