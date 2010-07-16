<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */

class controller extends adminpage{
    
    function controller(){
        parent::adminpage();
        $this->mdl_album = & load_model('album');
        $this->mdl_picture = & load_model('picture');
        if(!$this->auth->isLogedin()){
            redirect_c('default','login');
        }
        $this->output->set('current_nav','album');
    }
    
    function index(){
        
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        
        $albums = $this->mdl_album->get_all_album($page);
        
        $pageurl='admin.php?ctl=album&page=[#page#]';
        if($albums['ls']){
            foreach($albums['ls'] as $k=>$v){
                $cover = $this->mdl_album->get_cover($v['id'],$v['cover']);
                $albums['ls'][$k]['cover'] = $cover?mkImgLink($cover['dir'],$cover['pickey'],$cover['ext'],'thumb'):'img/nopic.jpg';
            }
        }
        $this->output->set('albums',$albums['ls']);
        $this->output->set('pageset',pageshow($albums['total'],$albums['start'],$pageurl));
        $this->output->set('total_num',$albums['count']);
        $this->view->display('admin/album.php');
    }
    
    function photos(){
        $album = intval($this->getGet('album',0));
        $page = $this->getGet('page',0);
        if(!$page){
            $page = 1;
        }
        if(!$album){
            showInfo('相册参数错误！',false);
        }
        
        $flag = $this->getGet('flag',0);
        
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
        $sort = $this->getGet('sort','time_desc');
        
        $pics = $this->mdl_picture->get_all_pic($page,$album,$sort);
        
        $pageurl="admin.php?ctl=album&act=photos&album={$album}&page=[#page#]&sort=".$sort;
        
        $this->output->set('pics',$pics['ls']);
        $this->output->set('albums_list',$this->mdl_album->get_albums_assoc($album));
        $this->output->set('album_name',$this->mdl_album->get_album_name($album));
        $this->output->set('album',$album);
        $this->output->set('sort',$sort);
        $this->output->set('page',$page);
        $this->output->set('msginfo',$msginfo);
        $this->output->set('pageset',pageshow($pics['total'],$pics['start'],$pageurl));
        $this->output->set('total_num',$pics['count']);
        $this->view->display('admin/album_photos.php');
    }
    
    function ajax_create_album(){
        $album_name = $this->getPost('album_name','');
        $album_private = intval($this->getPost('album_private',0));
        @ob_clean();
        if($this->mdl_album->insert_album(array('name'=>$album_name,'private'=>$album_private,'create_time'=>time() ))){
            $list = $this->mdl_album->get_albums_assoc();
            
            echo json_encode(array('ret'=>true,'list'=>$list));
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'创建相册失败！'));
        }
    }
    
    function ajax_delphoto(){
        @ob_clean();
        if($this->isPost()){
            $id = intval($this->getGet('id',0));
            
            $row = $this->mdl_picture->get_one_pic($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要删除的图片不存在！'));
                exit;
            }
            $upload =& load_model('upload');
            $upload->delpicfile($row['dir'],$row['pickey'],$row['ext']);
            
            $this->mdl_album->remove_cover($id);
            
            if($this->mdl_picture->del_pic($id)){
                echo json_encode(array('ret'=>true));
                exit;
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'删除图片失败！'));
                exit;
            }
        }
    }
    
    function ajax_renamephoto(){
        @ob_clean();
        if($this->isPost()){
            $id = intval($this->getGet('id',0));
            $name = trim($this->getPost('name'));

            $row = $this->mdl_picture->get_one_pic($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要编辑的图片不存在！'));
                exit;
            }
            if($name){
                if($this->mdl_picture->update_pic($id,$name)){
                    echo json_encode(array('ret'=>true,'picname'=>$name));
                    exit;
                }else{
                    echo json_encode(array('ret'=>false,'msg'=>'重命名图片失败！'));
                    exit;
                }
            }else{
                echo json_encode(array('ret'=>true,'picname'=>$row['name']));
                exit;
            }
        }
    }
    
    function ajax_delalbum(){
        @ob_clean();
        if($this->isPost()){
            $id = intval($this->getGet('id',0));

            $row = $this->mdl_album->get_one_album($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要删除的相册不存在！'));
                exit;
            }
            
            $albums = $this->mdl_picture->get_all_pic(NULL,$id);
            
            if($albums){
                $upload =& load_model('upload');
                foreach($albums as $v){
                    $upload->delpicfile($v['dir'],$v['pickey'],$v['ext']);
                }
            }
            
            if($this->mdl_album->del_album($id)){
                echo json_encode(array('ret'=>true));
                exit;
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'删除相册失败！'));
                exit;
            }
        }
    }
    
    function ajax_renamealbum(){
        @ob_clean();
        if($this->isPost()){
            $id = intval($this->getGet('id',0));
            $name = trim($this->getPost('name'));
            
            $row = $this->mdl_album->get_one_album($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'要编辑的相册不存在！'));
                exit;
            }
            if($name){                
                if($this->mdl_album->update_album($id,$name)){
                    echo json_encode(array('ret'=>true,'albumname'=>$name));
                    exit;
                }else{
                    echo json_encode(array('ret'=>false,'msg'=>'重命名相册失败！'));
                    exit;
                }
            }else{
                echo json_encode(array('ret'=>true,'albumname'=>$row['name']));
                exit;
            }
        }
    }
    
    function ajax_set_cover(){
        @ob_clean();
        if($this->isPost()){
            $id = intval($this->getGet('id',0));
            $row = $this->mdl_picture->get_one_pic($id);
            if(!$row){
                echo json_encode(array('ret'=>false,'msg'=>'图片已被删除无法设为封面！'));
                exit;
            }
            if($this->mdl_album->set_cover($row['album'],$id)){
                echo json_encode(array('ret'=>true));
                exit;
            }else{
                echo json_encode(array('ret'=>false,'msg'=>'未能设为封面！'));
                exit;
            }
        }
    }
    
    function ajax_get_albums(){
        @ob_clean();
        $id = intval($this->getPost('id',0));
        $row = $this->mdl_picture->get_one_pic($id);
        $album_id = $row['album'];
        
        $list = $this->mdl_album->get_albums_assoc($album_id);
        if($list){
            echo json_encode(array('ret'=>true,'list'=>$list));
            exit;
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'目前无其他相册！'));
            exit;
        }
    }
    
    function ajax_move_to_albums(){
        @ob_clean();
        
        $id = intval($this->getPost('id',0));
        
        $row = $this->mdl_picture->get_one_pic($id);
        if(!$row){
            echo json_encode(array('ret'=>false,'msg'=>'要移动的照片不存在！'));
            exit;
        }
        
        $this->mdl_album->remove_cover($id);

        if($this->mdl_picture->update_pic($id,$row['name'],intval($this->getPost('album_id',0)))){
            echo json_encode(array('ret'=>true));
            exit;
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'未能移动照片！'));
            exit;
        }
    }
    
    function ajax_edit_priv_albums(){
        @ob_clean();
        
        $id = intval($this->getPost('id',0));
        $album = $this->mdl_album->get_one_album($id);
        if($album){
            $html = '<div class="album_name_f"> <span>私有相册</span><input name="private" type="checkbox" value="1"';
            if($album['private'] == '1'){
                $html .=' checked="checked"';
            }
            $html .= ' /></div>';
            echo json_encode(array('ret'=>true,'html'=>$html));
            exit;
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'相册不存在！'));
            exit;
        }
    }
    
    function ajax_do_edit_priv_albums(){
        @ob_clean();
        
        $id = intval($this->getPost('id',0));
        $private = intval($this->getPost('private_v',0));

        if($this->mdl_album->priv_album($id,$private)){
            echo json_encode(array('ret'=>true,'html'=>''));
            exit;
        }else{
            echo json_encode(array('ret'=>false,'msg'=>'修改相册权限失败！'));
            exit;
        }
    }
}