<?php

$lang = array(
    'zh_cn' => '简体中文',
    
    'myalbum' => '我的相册',
	'title_install' => SOFT_NAME.' 安装向导',
	'agreement_yes' => '我同意',
	'agreement_no' => '我不同意',
	
	'install_locked' => '安装锁定，已经安装过了，如果您确定要重新安装，请到服务器上删除<br /> '.str_replace(ROOTDIR, '', $lockfile),
	'error_quit_msg' => '您必须解决以上问题，安装才可以继续',
	
	'click_to_back' => '点击返回',
	'method_undefined' => '未定义方法',
	'license' => '这里是版权信息 license',
	
	'notset' => '无限制',
	'writeable' => '可写',
	'unwriteable' => '不可写',
	'nodir' => '不是目录',
	
	'step_env_title' => '开始安装',
	'step_env_desc' => '检查安装环境及文件目录权限',
	'php_version_too_low' => 'php版本过低',
	
	'old_step' => '上一步',
	'new_step' => '下一步',
	
	'not_continue' => '检查到错误，无法继续，请修正后继续',
	
	'supportted' => '支持',
	'unsupportted' => '不支持',
	'project' => '项目',
	'center_required' => '程序所需配置',
	'center_best' => '程序推荐配置',
	'curr_server' => '当前配置',
	'env_check' => '环境检查',
	'os' => '操作系统',
	'php' => 'PHP 版本',
	'attachmentupload' => '附件上传',
	'unlimit' => '不限制',
	'version' => '版本',
	'gdversion' => 'GD 库',
	'noext' => '没有扩展',
	'allow' => '允许',
	'unix' => '类Unix',
	'diskspace' => '磁盘空间',
	'priv_check' => '目录、文件权限检查',
	'func_depend' => '函数依赖性检查',
	'func_name' => '函数名称',
	'check_result' => '检查结果',
	'suggestion' => '建议',
	'advice_copy' => '建议修改 php.ini 中打开copy函数',
	'advice_file_get_contents' => '该函数需要 php.ini 中 allow_url_fopen 选项开启。请联系空间商，确定开启了此项功能',
	'none' => '无',
	
	'step1_file' => '目录文件',
	'step1_need_status' => '所需状态',
	'step1_status' => '当前状态',
	
	'database' => '数据库',
	'0db' => '不支持数据库',
	'1db' => '支持Mysql',
	'2db' => '支持Sqlite',
	'3db' => '支持Mysql及Sqlite',
	
	'step_db_init_title' => '安装数据库',
	'step_db_init_desc' => '正在执行数据库安装',
	'sel_db_type' => '选择数据库类型',
	'db_type' => '数据库类型',
	'db_type_comments' => '正式环境不推荐用Sqlite',
	
	'tips_mysqldbinfo' => '数据库信息',
	'tips_mysqldbinfo_comment' => '',
	
	'tips_admininfo' => '填写管理员信息',
	'tips_admininfo_comment' => '',
	'username' => '管理员账号',
	'password' => '管理员密码',
	'password2' => '重复密码',
	'email' => '管理员 Email',
	
	'password_comment' => '密码员密码不能为空，请填写',
	
	'sqlite' => '使用Sqlite体验',
	'sqlite_check_label' => '安装sqlite',
	'sqlite_comment' => '正式环境不建议使用',
	
	'dbhost' => '数据库服务器',
	'dbuser' => '数据库用户名',
	'dbport' => '数据库端口',
	'dbpw' => '数据库密码',
	'dbname' => '数据库名',
	'tablepre' => '数据表前缀',
	'dbport_comment' => '一般为3306',
	'dbhost_comment' => '数据库服务器地址, 一般为 localhost',
	'tablepre_comment' => '同一数据库运行多个相册系统时，请修改',
	
	'mysqldbinfo_dbhost_invalid' => '数据库服务器为空，或者格式错误，请检查',
	'mysqldbinfo_dbname_invalid' => '数据库名为空，或者格式错误，请检查',
	'mysqldbinfo_dbuser_invalid' => '数据库用户名为空，或者格式错误，请检查',
	'mysqldbinfo_dbpw_invalid' => '数据库密码为空，或者格式错误，请检查',
	'mysqldbinfo_adminemail_invalid' => '系统邮箱为空，或者格式错误，请检查',
	'mysqldbinfo_tablepre_invalid' => '数据表前缀为空，或者格式错误，请检查',
	'admininfo_username_invalid' => '管理员用户名为空，或者格式错误，请检查',
	'admininfo_email_invalid' => '管理员Email为空，或者格式错误，请检查',
	'admininfo_password_invalid' => '管理员密码为空，请填写',
	'admininfo_password2_invalid' => '两次密码不一致，请检查',
	
	'admininfo_invalid' => '管理员信息不完整，请检查管理员账号，密码，邮箱',
	'dbname_invalid' => '数据库名为空，请填写数据库名称',
	'tablepre_invalid' => '数据表前缀为空，或者格式错误，请检查',
	'admin_username_invalid' => '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字',
	'admin_password_invalid' => '密码和上面不一致，请重新输入',
	'admin_email_invalid' => 'Email 地址错误，此邮件地址已经被使用或者格式无效，请更换为其他地址',
	'admin_invalid' => '您的信息管理员信息没有填写完整，请仔细填写每个项目',
	'admin_exist_password_error' => '该用户已经存在，如果您要设置此用户为论坛的管理员，请正确输入该用户的密码，或者请更换论坛管理员的名字',
	
	'tips_siteinfo' => '填写网站信息',
	
	'siteurl' => '站点URL',
	'sitename' => '网站名',
	'siteurl_comment' => '请带上URL末端的"/" ',
	
	'dbname_invalid' => '数据库名错误',
	'tablepre_invalid' => '表前缀错误',
	'database_errno_2003' => '无法连接数据库，请检查数据库是否启动，数据库服务器地址是否正确',
	'database_errno_1044' => '无法创建新的数据库，请检查数据库名称填写是否正确',
	'database_errno_1045' => '无法连接数据库，请检查数据库用户名或者密码是否正确',
	'database_connect_error' => '数据库连接错误',
	
	'admininfo_invalid' => '管理员信息不完整，请检查管理员账号，密码，邮箱',
	'admin_username_invalid' => '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字',
	'admin_password_invalid' => '密码和上面不一致，请重新输入',
	'admin_email_invalid' => 'Email 地址错误，此邮件地址已经被使用或者格式无效，请更换为其他地址',
	
	'install_in_processed' => '正在安装...',
	'create_table' => '创建数据库表',
	'succeed' => '成功',
	'install_data_sql' => '安装初始化数据',
	
	'undefine_func' => 'mysql扩展未安装',
	
	'clear_dir' => '清空目录',
	'create_admin_account' => '创建管理员账号',
	'failed' => '失败',
	'update_user_setting' =>'更新用户设置',
	'installed_complete' => '安装完成...',
	
	'forceinstall' => '强制安装',
	'mysqldbinfo_forceinstall_invalid' => '当前数据库当中已经含有同样表前缀的数据表，您可以修改“表名前缀”来避免删除旧的数据，或者选择强制安装。强制安装会删除旧数据，且无法恢复',
    'forceinstall_check_label' => '我要删除数据，强制安装 !!!',
    
    'copy_sqlite' => '拷贝数据库至',
    
    'tips_sqlite' => 'Sqlite信息',
    'sqlite_forceinstall_invalid' => '目标目录中已经存在Sqlite数据库,您可以选择强制安装。强制安装会删除旧数据，且无法恢复',
    
    'step_complete_title' => '安装成功',
    'step_complete_desc' => '&nbsp;',
    'install_succeed' => '恭喜您安装成功',
    'auto_redirect' => '程序将自动跳转',
);