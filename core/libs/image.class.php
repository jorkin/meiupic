<?php
/**
 * $Id: image.class.php 99 2011-02-20 13:57:57Z lingter $
 * 
 * Image class: resize, cut, rotate, add water mark 
 *      
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */
class image_cla{
    function image_cla(){
        $this->class_name = 'image_'.(defined('IMG_ENGINE')?constant('IMG_ENGINE'):'gd');
        if(file_exists(LIBDIR.'img_engine/'.$this->class_name.'.php')){
            require_once(LIBDIR.'img_engine/'.$this->class_name.'.php');
            $this->worker = new $this->class_name;
        }else{
            exit('Image Engine Error: Engine '.IMG_ENGINE.' not exists!');
        }
    }
    
    function load($filename){
        return $this->worker->load($filename);
    }
    
    function supportType(){
        return $this->worker->supportType();
    }
    
    function getWidth(){
        return $this->worker->getWidth();
    }
    
    function getHeight(){
        return $this->worker->getHeight();
    }
    
    function getExtension(){
        $this->worker->getExtension();
    }
    
    function save($path){
        $this->worker->save($path);
    }
    
    function output(){
        $this->worker->output();
    }
    
    function resizeTo($w,$h){
        $this->worker->resizeTo($w,$h);
    }
    function resizeScale($w,$h){
        $this->worker->resizeScale($w,$h);
    }
    
    function square($v){
        $this->worker->square($v);
    }
    
    function rotate($a){
        $this->worker->rotate($a);
    }
    
    function waterMarkSetting($param){
        $this->worker->waterMarkSetting($param);
    }
    
    function waterMark(){
        $this->worker->waterMark();
    }
}
?>