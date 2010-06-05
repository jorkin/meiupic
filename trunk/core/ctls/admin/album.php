<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends pagefactory{
    
    function controller(){
        parent::pagefactory();
        $this->mdl_album = & load_model('album');
        $this->mdl_picture = & load_model('picture');
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $this->output->set('current_nav','album');
    }
    
    function index(){
        
        $page = $_GET['page'];
        if(!$page){
            $page = 1;
        }
        
        $albums = $this->mdl_album->get_all_album($page);
        
        $pageurl='index.php?page=[#page#]';
        if($albums['ls']){
            foreach($albums['ls'] as $k=>$v){
                $albums['ls'][$k]['cover'] = $this->mdl_album->get_cover($v['id'],$v['cover']);
            }
        }
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',pageshow($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->view->display('admin/album.php');
    }
    
    function photos(){
        $album = intval($_GET['album']);
        $page = $_GET['page'];
        if(!$page){
            $page = 1;
        }
        
        $flag = isset($_GET['flag'])?$_GET['flag']:0;
        
        switch($flag){
            case '1':
            $msginfo = '<div id="msginfo" class="fail">操作失败！请先选择要操作的图片！ <a href="javascript:void(0)"onclick="$(\'#msginfo\').hide()">[关闭]</a></div>';
            break;
            case '2':
            $msginfo = '<div id="msginfo" class="fail">操作失败！请选择要移动的相册！<a href="javascript:void(0)"onclick="$(\'#msginfo\').hide()">[关闭]</a></div>';
            break;
            case '3':
            $msginfo = '<div id="msginfo" class="succ">操作成功！<a href="javascript:void(0)"onclick="$(\'#msginfo\').hide()">[关闭]</a></div>';
            break;
            default:
            $msginfo = '';
        }
        
        $pics = $this->mdl_picture->get_all_pic($page,$album);
        
        $pageurl="index.php?ctl=album&act=photos&album={$album}&page=[#page#]";
        $this->output->set('pics',$pics['ls']);
        $this->output->set('albums_list',$this->mdl_album->get_albums_assoc($album));
        $this->output->set('album_name',$this->mdl_album->get_album_name($album));
        $this->output->set('album',$album);
        $this->output->set('page',$page);
        $this->output->set('msginfo',$msginfo);
        $this->output->set('pageset',pageshow($pics['total'],$pics['start'],$pageurl));
        $this->output->set('total_num',$pics['count']);
        $this->view->display('admin/album_photos.php');
    }
    
    function ajax_create_album(){
        $album_name = $_POST['album_name'];
        
        if($this->mdl_album->insert_album(array('name'=>$album_name))){
            $list = $this->mdl_album->get_albums_assoc();
            
            echo json_encode(array('ret'=>true,'list'=>$list));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'创建相册失败！'));
        }
    }
    
    function ajax_delphoto(){
        if($this->isPost()){
            $id = intval($_GET['id']);
            
            $row = $this->mdl_picture->get_one_pic($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要删除的图片不存在！'));
                exit;
            }
            @unlink(DATADIR.$row['path']);
            @unlink(DATADIR.$row['thumb']);
            
            $this->mdl_album->remove_cover($id);
            
            if($this->mdl_picture->del_pic($id)){
                echo json_encode(array('ret'=>true));
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'删除图片失败！'));
            }
        }
    }
    
    function ajax_renamephoto(){
        if($this->isPost()){
            $id = intval($_GET['id']);
            $name = trim($_POST['name']);

            $row = $this->mdl_picture->get_one_pic($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要编辑的图片不存在！'));
                exit;
            }
            if($name){
                if($this->mdl_picture->update_pic($id,$name)){
                    echo json_encode(array('ret'=>true,'picname'=>$name));
                }else{
                    echo json_encode(array('ret'=>false,'msg'=>'重命名图片失败！'));
                }
            }else{
                echo json_encode(array('ret'=>true,'picname'=>$row['name']));
            }
        }
    }
    
    function ajax_delalbum(){
        if($this->isPost()){
            $id = intval($_GET['id']);

            $row = $this->mdl_album->get_one_album($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要删除的相册不存在！'));
                exit;
            }
            
            $albums = $this->mdl_picture->get_all_pic(NULL,$id);
            
            if($albums){
                foreach($albums as $v){
                    @unlink(DATADIR.$v['path']);
                    @unlink(DATADIR.$v['thumb']);
                }
            }
            
            if($this->mdl_album->del_album($id)){
                echo json_encode(array('ret'=>true));
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'删除相册失败！'));
            }
        }
    }
    
    function ajax_renamealbum(){
        if($this->isPost()){
            $id = intval($_GET['id']);
            $name = trim($_POST['name']);
            
            $row = $this->mdl_album->get_one_album($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要编辑的相册不存在！'));
                exit;
            }
            if($name){                
                if($this->mdl_album->update_album($id,$name)){
                    echo json_encode(array('ret'=>true,'albumname'=>$name));
                }else{
                    echo json_encode(array('ret'=>false,'msg'=>'重命名相册失败！'));
                }
            }else{
                echo json_encode(array('ret'=>true,'albumname'=>$row['name']));
            }
        }
    }
    
    function ajax_set_cover(){
        if($this->isPost()){
            $id = intval($_GET['id']);
            $row = $this->mdl_picture->get_one_pic($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'图片已被删除无法设为封面！'));
                exit;
            }
            if($this->mdl_album->set_cover($row['album'],$id)){
                echo json_encode(array('ret'=>true));
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'未能设为封面！'));
            }
        }
    }
    
    function ajax_get_albums(){
        $id = intval($_POST['id']);
        $row = $this->mdl_picture->get_one_pic($id);
        $album_id = $row['album'];
        
        $list = $this->mdl_album->get_albums_assoc($album_id);
        if($list){
            echo json_encode(array('ret'=>true,'list'=>$list));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'目前无其他相册！'));
        }
    }
    
    function ajax_move_to_albums(){
        $id = intval($_POST['id']);
        
        $row = $this->mdl_picture->get_one_pic($id);
        if(!$row){
            echo json_encode(array('ret'=>false,'msg'=>'要移动的照片不存在！'));
            exit;
        }
        
        $this->mdl_album->remove_cover($id);

        if($this->mdl_picture->update_pic($id,$row['name'],intval($_POST['album_id']))){
            echo json_encode(array('ret'=>true));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'未能移动照片！'));
        }
    }
}