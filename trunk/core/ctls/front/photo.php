<?php
/**
 * $Id: album.php 13 2010-05-29 16:42:01Z lingter $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends frontpage{
    
    function controller(){
        parent::frontpage();
        $this->mdl_album = & load_model('album');
        $this->mdl_picture = & load_model('picture');
        $this->output->set('current_nav','album');
    }
    
    function resize(){
        $size = $this->getGet('size','thumb');
        $key = $this->getGet('key'); 
        
        include_once(LIBDIR.'image.class.php');
        $imgobj = new Image();

        $pic = $this->mdl_picture->get_one_pic_by_key($key);
        if(!in_array($size,array('small','square','medium','big','thumb')) || !$pic){
            $imgobj->load(DATADIR.'nopic.jpg');
            $imgobj->output();
            exit;
        }
        $square = false;
        if($size=='small'){
            $width = '240';
            $height = '240';
        }elseif($size=='thumb'){
            $width = '110';
            $height = '150';
        }elseif($size=='square'){
            $width = '75';
            $height = '75';
            $square = true;
        }elseif($size=='medium'){
            $width = '500';
            $height = '500';
        }elseif($size=='big'){
            $width = '700';
            $height = '700';
        }
        $orig = mkImgLink($pic['dir'],$key,$pic['ext'],'orig'); 
        $resized = mkImgLink($pic['dir'],$key,$pic['ext'],$size); 
        
        if(file_exists(ROOTDIR.$resized)){
            $imgobj->load(ROOTDIR.$resized);
            $imgobj->output();
            exit;
        }
        
        $imgobj->load(ROOTDIR.$orig);
        $imgobj->setQuality(95);
        if($square){
            $imgobj->square($width);
        }else{
            $imgobj->resizeScale($width,$height);
        }
        $imgobj->save(ROOTDIR.$resized);
        @chmod(ROOTDIR.$resized,0755);
        $imgobj->output();
    }
    
}