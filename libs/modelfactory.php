<?php

require_once(LIBDIR.'auth.php');
class modelfactory{
    
    function modelfactory(){
        $this->output =& get_output();
        $this->db =& db();
        $this->auth = new auth();
    }
    
    function txtsql_topage($query_arr,$page){
        
        if(isset($query_arr['where'])){
            $total = $this->db->rows_count($query_arr);
        }else{
            $total = $this->db->table_count($query_arr['table']);
        }
        $totalpage=ceil($total/PAGE_SET);

        if($page<1) $page=1;
        if($page>$totalpage) $page=$totalpage;

        if($total>0){
            $query_arr['limit'] = array(($page-1)*PAGE_SET,$page*PAGE_SET-1);
            $rows = $data = $this->db->select($query_arr);
        }else{
            $rows = null;
        }

        $arr["ls"]= $rows; //记录内容

        $arr["total"]= $totalpage;//总数页数
        $arr['start']=$page; //开始页
        $arr['count']=$total;
        
        return $arr;
        
    }
}