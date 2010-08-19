<?php
/**
 * $Id$
 * 
 * @author : Lingter
 * @support : http://www.meiu.cn
 * @copyright : (c)2010 meiu.cn lingter@gmail.com
 */
require_once INCDIR.'template.func.php';
class View{
    var $tplFile;
    function View($tplFile = '')    {
        if(!empty($tplFile))
            $this->tplFile =  $tplFile;
    }
    /**
     * 返回待输出的view内容
     */
    function fetch($tplFile = ''){
        if(!empty($tplFile))
            $this->tplFile =  $tplFile;
        
        ob_start();

        $params = get_output()->getAll();
        extract($params);
        include template($this->tplFile);
            
        $content = ob_get_clean();
        return $content;
    }
    
    function fetchphp($tplFile = ''){
        if(!empty($tplFile))
            $this -> tplFile =  "{$tplFile}";
        
        @ob_clean();
        ob_start();
        //模板中直接使用的对象
        $res =& get_output();

        if(file_exists(VIEWDIR.$this->tplFile))
            include_once(VIEWDIR.$this -> tplFile);
        else 
            exit("TplFile doesn't exist!");
            
        $content = ob_get_clean();
        return str_replace("\xEF\xBB\xBF", '', $content);
    }
    /**
     * 输出到浏览器
     */
    function display($tplFile = '',$php = false){
        if(!empty($tplFile))
            $this->tplFile =  $tplFile;
        if($php)
            echo $this->fetchphp();
        else
            echo $this->fetch();
    }
}
?>