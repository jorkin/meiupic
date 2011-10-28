<?php
/**
 * $Id: category.mdl.php 231 2011-10-22 02:47:38Z lingter@gmail.com $
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 - 2011 meiu.cn lingter@gmail.com
 */
class category_mdl extends modelfactory {
    var $table_name = '#@cate';

    /**
     * 获取树状分类 tree
     * 带参数标示取出分类及子分类的树状结构
     * 不带参数表示取出所有的
     */
    function get_categorys($catid=0){
        if($catid>0){
            $where = "cate_path like '%,".intval($catid).",%'";
        }else{
            $where = null;
        }
        $this->db->select($this->table_name,'*',$where,'sort asc');
        $cateList = $this->db->getAll();
        $pArr = array();
        if(is_array($cateList)){
            $sArr = array();
            foreach($cateList as $cate){
                $sArr['s_'.$cate['par_id']][] = $cate;
            }
            $pArr = $this->_sub($sArr,0);
        }
        unset($cateList);
        unset($sArr);
        return $pArr;
    }
    //平面化分类
    function get_flat_category(){
        $oarr = $this->get_categorys();
        $arr = array();
        if(is_array($oarr)){
            $this->_deep($arr,$oarr,0);
        }
        return $arr;
    }
    /**
     * 获取属于当前分类的所有分类的id，包含它自己
     * 返回数组
     *
     * @param int $id
     * @return array
     */
    function get_belong_ids($id){
        $this->db->select($this->table_name,'id','cate_path like \'%,'.intval($id).',%\'');
        return $this->db->getCol(0);
    }

    //递归显示
    function _deep(& $arr,& $oarr,$deep = 0){
        foreach($oarr as $v){
            $v['deep'] = $deep;
            $tmp = $v['sub'];
            unset($v['sub']);
            $arr[] = $v;
            if($tmp){
                $this->_deep($arr,$tmp,$deep+1);
            }
        }
        return $arr;
    }
    //递归分类私有函数
    function _sub(& $arr,$parent){
        if(isset($arr['s_'.$parent])){
            $tarr = $arr['s_'.$parent];
            foreach($tarr as $k=>$v){
                $tarr[$k]['sub'] = $this->_sub($arr,$v['id']);
            }
            return $tarr;
        }else{
            return false;
        }
    }

    /**
     * 删除分类
     *
     * @param Int $ids
     * @return Boolean
     */
    function delete($id){
        $this->db->select($this->table_name,'count(*)','par_id='.$id);
        if($this->db->getOne() > 0){
            return false;
        }

        $this->db->delete($this->table_name,'id = '.$id);

        return $this->db->query();
    }

    //编辑分类
    function update($id,$arr){
        if(isset($arr['par_id'])){
            $this->db->select($this->table_name,'par_id',"id = '$id'");
            $old_row = $this->db->getRow();
            if($old_row['par_id'] != $arr['par_id']){
                $this->db->select($this->table_name,'cate_path','id = '.$arr['par_id']);
                $row = $this->db->getRow();
                $path_ids = explode(',',trim($row['cate_path'],','));
                if(in_array($id,$path_ids)){
                    return false;
                }
            
                if($arr['par_id'] != 0){
                    $arr['cate_path'] = $row['cate_path'].$id.',';
                }else{
                    $arr['cate_path'] = ','.$id.',';
                }
            
                $this->db->select($this->table_name,'id,cate_path',"cate_path like '%,".$id.",%' and id<>".intval($id));
                $result = $this->db->getAll();
                if($result){
                    foreach ($result as $v){
                         $path = $arr['cate_path'].substr( $v['cate_path'], strpos( $v['cate_path'], ",".$id."," ), strlen( $v['cate_path'] ) );
                         $path = preg_replace('/^.*\,'.$id.'\,(.*)$/',$arr['cate_path']."\${1}",$v['cate_path']);
                         $this->db->update($this->table_name,'id='.$v['id'],array( 'cate_path'=>$path ));
                         $this->db->query();
                    }
                }
            }
        }

        $this->db->update($this->table_name,'id='.$id,$arr);
        return $this->db->query();
    }
    //添加分类
    function save($arr){
        $this->db->select($this->table_name,'cate_path','id = '.$arr['par_id']);
        $row = $this->db->getRow();
        
        $this->db->startTrans();
        $this->db->insert($this->table_name,$arr);
        
        if(!$this->db->query()){
            $this->db->rollback();
            return false;
        }
        $id = $this->db->insertId();
        
        if($arr['par_id'] != 0){
            $uparr['cate_path'] = $row['cate_path'].$id.',';
        }else{
            $uparr['cate_path'] = ','.$id.',';
        }

        $this->db->update($this->table_name,'id='.$id,$uparr);
        if(!$this->db->query()){
            $this->db->rollback();
            return false;
        }
        $this->db->commit();
        return true;
    }
}
