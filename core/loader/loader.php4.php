<?php
/**
 * 此文件为超级装载器
 *
 */

define('SP_LOADER', '__SP__LOADER_CORE__');

$GLOBALS[SP_LOADER] = array(
    'SELF'                => null,
    'OBJECTS'             => array(),
    'CONFIGS'             => array(),
    'HELPERS'             => array(),
    'MODELS'              => array(),
    'DATABASES'           => array()
);

class loader{
    function &instance(){
        if(!isset($GLOBALS[SP_LOADER]['SELF'])){
            $GLOBALS[SP_LOADER]['SELF'] =& new Loader();
        }
        return $GLOBALS[SP_LOADER]['SELF'];
    }
        /**
         * 装载库文件
         */
    function &lib($class){
        //$class = ucfirst($class);
        if (!isset($GLOBALS[SP_LOADER]['OBJECTS'][$class])){
            if(file_exists(LIBDIR.$class.'.class.php')){
                require(LIBDIR.$class.'.class.php');
                $name = $class.'_cla';
                $GLOBALS[SP_LOADER]['OBJECTS'][$class] =& new $name();
            }else{
                exit('error lib');
            }
        }
        return $GLOBALS[SP_LOADER]['OBJECTS'][$class];
    }
    /**
     * 装载模型
     */
    function &model($modelName){
        if(!isset($GLOBALS[SP_LOADER]['MODELS'][$modelName])){
            $modelPath = MODELDIR.$modelName.'.mdl.php';
            $modelClass = $modelName.'_mdl';
            if(file_exists($modelPath)) {
                require($modelPath);
            }else{ 
                exit('error model');
            }
            $GLOBALS[SP_LOADER]['MODELS'][$modelName] =& new $modelClass;
        }
        return $GLOBALS[SP_LOADER]['MODELS'][$modelName];
    }
    /**
     * 装载数据库
     */
    function &database($dbstr='default',$config = ''){
        if (!isset($GLOBALS[SP_LOADER]['DATABASES'][$dbstr])){
            if(is_array($config)){
                $DB_config = $config;
            }else{
                $Config =& loader::config();
                $DB_config = $Config['database'][$dbstr];
            }
            $db_class =& loader::lib('db');
            
            $db_class->init($DB_config);
            $GLOBALS[SP_LOADER]['DATABASES'][$dbstr] =& $db_class;
        }
        return $GLOBALS[SP_LOADER]['DATABASES'][$dbstr];
    }
    /**
     * 装载视图
     */
    function view($tplFile,$isDisplay = true){
        global $base_path;
        
        if(file_exists(TPLDIR.'/info.php')){
            include_once(TPLDIR.'/info.php');
            if(isset($style_configs[$current_theme_style])){
                extract($style_configs[$current_theme_style]);
            }elseif(isset($style_configs['default'])){
                extract($style_configs['default']);
            }
        }
        $style_path = $base_path.TPLDIR.'/';
        require_once INCDIR.'template.func.php';
        $params = loader::lib('output')->getAll();
        extract($params);
        ob_start();
        include template($tplFile);
        $content = ob_get_clean();
        if($isDisplay){
            echo $content;
        }else{
            return $content;
        }
    }
    /**
     * 装载配置
     */
    function &config($name = 'config'){
        if ( !isset($GLOBALS[SP_LOADER]['CONFIGS'][$name])){
            if (!file_exists(ROOTDIR."conf/{$name}.php")){
                exit('配置文件不存在');
            }
            require(ROOTDIR."conf/{$name}.php");

            if ( ! isset($CONFIG) || ! is_array($CONFIG)){
                exit('配置文件错误');
            }

            $GLOBALS[SP_LOADER]['CONFIGS'][$name] =& $CONFIG;
        }
        return $GLOBALS[SP_LOADER]['CONFIGS'][$name];
    }
}
