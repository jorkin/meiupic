<?php

class controller extends pagefactory{
    
    function controller(){
        parent::pagefactory();
        
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
        $this->db->select('#albums',"*",'','id desc');
        $albums = $this->db->toPage($page,PAGE_SET);
        $pageurl='index.php?page=[#page#]';
        if($albums['ls']){
            foreach($albums['ls'] as $k=>$v){
                if(!$v['cover']){
                    $albums['ls'][$k]['cover'] = $this->_get_cover($v['id']);
                }
            }
        }
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',pageshow($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->view->display('album.php');
    }
    
    function photos(){
        $album = intval($_GET['album']);
        $page = $_GET['page'];
        if(!$page){
            $page = 1;
        }
        $this->db->select('#imgs',"*",'album='.$album,'id desc');
        $pics = $this->db->toPage($page,PAGE_SET);
        $pageurl="index.php?ctl=album&act=photos&album={$album}&page=[#page#]";
        $this->output->set('pics',$pics['ls']);
        $this->output->set('album_name',$this->_get_album_name($album));
        $this->output->set('album',$album);
        $this->output->set('pageset',pageshow($pics['total'],$pics['start'],$pageurl));
        $this->output->set('total_num',$pics['count']);
        $this->view->display('album_photos.php');
    }
    
    function _get_album_name($id){
        $this->db->select('#albums',"name",'id='.intval($id));
        return $this->db->getOne();
    }
    
    function _get_cover($album_id){
        $this->db->select('#imgs',"*",'album='.intval($album_id),'id asc limit 1');
        $row = $this->db->getRow();
        if($row){
            return $row['thumb'];
        }else{
            return 'nopic.jpg';
        }
    }
    
    function ajax_delphoto(){
        if($this->isPost()){
            $id = intval($_GET['id']);
        
            $this->db->select('#imgs','*','id='.$id);
            $row = $this->db->getRow();
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要删除的图片不存在！'));
                exit;
            }
            @unlink(DATADIR.$row['path']);
            @unlink(DATADIR.$row['thumb']);
        
            $this->db->delete('#imgs','id='.$id);
            if($this->db->query()){
                $this->db->update('#albums',"cover='".$row['thumb']."' and id =".intval($row['album']),array('cover'=>''));
                $this->db->query();
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
            
            $this->db->select('#imgs','*','id='.$id);
            $row = $this->db->getRow();
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要编辑的图片不存在！'));
                exit;
            }
            if($name){
                $this->db->update('#imgs','id='.$id,array('name'=>$name));
                
                if($this->db->query()){
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
        
            $this->db->select('#albums','*','id='.$id);
            $row = $this->db->getRow();
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要删除的相册不存在！'));
                exit;
            }
            
            $this->db->select('#imgs','*','album='.$id);
            $albums = $this->db->getAll();
            if($albums){
                foreach($albums as $v){
                    @unlink(DATADIR.$v['path']);
                    @unlink(DATADIR.$v['thumb']);
                }
            }
            
        
            $this->db->delete('#imgs','album='.$id);
            $this->db->query();
            
            $this->db->delete('#albums','id='.$id);
            if($this->db->query()){
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
            
            $this->db->select('#albums','*','id='.$id);
            $row = $this->db->getRow();
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要编辑的相册不存在！'));
                exit;
            }
            if($name){
                $this->db->update('#albums','id='.$id,array('name'=>$name));
                
                if($this->db->query()){
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
            $this->db->select('#imgs','*','id='.$id);
            $row = $this->db->getRow();
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'图片已被删除无法设为封面！'));
                exit;
            }
            $this->db->update('#albums','id='.$row['album'],array('cover'=>$row['thumb']));
            if($this->db->query()){
                echo json_encode(array('ret'=>true));
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'未能设为封面！'));
            }
        }
    }
    
    function ajax_get_albums(){
        $this->db->select('#imgs','album','id='.intval($_POST['id']));
        $album_id = $this->db->getOne();
        $this->db->select('#albums','*','id <> '.intval($album_id));
        $list = $this->db->getAssoc();
        if($list){
            echo json_encode(array('ret'=>true,'list'=>$list));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'未能获取到相册！'));
        }
    }
    
    function ajax_move_to_albums(){
        $id = intval($_POST['id']);
        
        $this->db->select('#imgs','*','id='.$id);
        $row = $this->db->getRow();
        if(!$row){
            echo json_encode(array('ret'=>false,'msg'=>'要移动的照片不存在！'));
            exit;
        }
        
        $this->db->update('#imgs','id='.$id,array('album'=>intval($_POST['album_id'])));
        if($this->db->query()){
            $this->db->update('#albums',"cover='".$row['thumb']."' and id =".intval($row['album']),array('cover'=>''));
            $this->db->query();
            echo json_encode(array('ret'=>true));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'未能移动照片！'));
        }
    }
}