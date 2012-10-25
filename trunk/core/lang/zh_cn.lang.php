<?php

$language   =   array(
    
'lang_name'                        =>    '中文-简体',
//core
'file_not_exists'                  =>   '%s文件不存在!',
'db_config_error'                  =>   '数据库配置错误,请检查配置文件!',
'sqlite_not_exists'                =>   'Sqlite数据库不存在!',
'miss_dbname'                      =>   '请设置数据库名!',
'connect_mysql'                    =>    '连接至Mysql (%s,%s) 失败!',
'can_not_use_db'                   =>    '不能使用数据库 %s',
'img_engine_not_exists'            =>   '加载图像引擎错误: %s 不存在！',
'storage_engine_not_exists'        =>   '存储引擎错误: %s 不存在！',
'plugin_can_not_call'              =>   '插件 %s 不能执行！',
'config_file_error'                =>   '配置文件格式错误！',
'config_file_not_exists'           =>   '配置文件不存在！',
'load_model_error'                 =>   '装载model "%s" 错误!',
'load_lib_error'                   =>   '装载library "%s" 错误!',
'pagination_tpl_not_exists'        => '分页模版不存在！',
'system_notice'                    =>    '系统消息',
//page
'pageset_total'                    =>    '共%s页',
'pageset_prev'                     =>    '前页',
'pageset_next'                     =>    '后页',
'no_records'                       =>    '没有记录！',

//公共
'type'                             =>  '类型',
'no_limit'                         =>  '无限制',
'config'                           =>  '配置',
'enable'                           =>  '启用',
'disable'                          =>  '禁用',
'disabled'                         =>  '已禁用',
'enabled'                          =>  '已启用',
'not_installed'                    =>  '未安装',
'install'                          =>  '安装',
'unkown'                           =>  '未知',
'manual'                           =>  '手动',
'auto'                             =>  '自动',
'delete'                           =>  '删除',
'cancel'                           =>  '取消',
'copy'                             =>  '复制',
'not_authorized'                   =>  '您没有权限，需要登录！',   
'sort'                             =>  '排序',
'sort_by'                          =>  '排序',
'show_nums_per_page'               =>  '显示数',
'404_not_found'                    =>  '404 页面不存在!',
'others'                           =>  '其他',
'not_defined'                      =>  '未定义',
'open'                             =>  '打开',
'close'                            =>  '关闭',
'save'                             =>  '保存',
'modify'                           =>  '修改',
'sel_all'                          =>  '全选',
'no_access'                        =>  '没有访问权限',
'confirm'                          =>  '确定',
'submit'                           =>  '提交',
'all'                              =>  '全部',
'album'                            =>  '相册',
'photo'                            =>  '照片',
'using'                            =>  '启用中',
'edit'                             =>  '编辑',
'not_set'                          =>  '未指定',
'show_all'                         =>  '显示全部',
'yes'                              =>  '是',
'no'                               =>  '否',

//head
'myalbum'                          =>  '我的相册',
'album_index'                      =>  '首页',
'tags_title'                       =>  '查看所有标签列表，快速找到照片',
'tags'                             =>  '标签',
'upload_photo'                     =>  '上传照片',
'upload_photo_title'               =>  '上传照片',
'sys_setting'                      =>  '系统设置',
'sys_setting_title'                =>  '系统设置',
'trash'                            =>  '回收站',
'trash_title'                      =>  '进入回收站',  
'login'                            =>  '登录',
'login_title'                      =>  '登录后管理',
'profile'                          =>  '我的资料',
'profile_title'                    =>  '查看/修改我的资料',
'logout'                           =>  '登出',
'logout_title'                     =>  '退出管理',
//head html
'you_can'                          =>  '你可以',
'click_to_login'                   =>  '点击这里登录',
'back_to_index'                    =>  '返回首页',

//notices
'no_album_notice'                  =>    '当前没有任何相册，点击“创建新相册”建立您自己的相册吧。',
'no_cate_album_notice'             => '当前分类没有相册，点击“创建新相册”按钮创建相册。',
'no_cate_album_notice_notlogin'    => '当前分类还没有相册！',
'no_album_notice_notlogin'         => '系统中目前还没有任何相册。',
'no_photo_notice'                  =>    '当前相册内没有任何照片，点击“上传新照片”充实你的相册吧。',
'no_photo_notice_notlogin'         => '当前相册内没有任何照片。',
'no_album_search_notice'           => '未能搜索到相册，请重新使用其他关键字搜索。',
'no_photo_search_notice'           => '未能搜索到相关照片，请重新使用其他关键字搜索。',
'no_script_notice'                 =>    '<h1>程序需要JavaScript支持，您需要改变浏览器设置！</h1>
<p> 美优相册系统需要 <strong>JavaScript</strong>。 所有现代的浏览器都支持 JavaScript。您只需要修改浏览器的一个设置项就可以打开此功能。</p>
<p>请看这里: <a href="http://www.google.com/support/bin/answer.py?answer=23852" target="blank">如何开启JavaScript</a>。</p>
<p>如果您安装了屏蔽广告的软件，那么请将该网站设为允许JavaScript。</p>
<p>一旦你开启了JavaScript, <a href="">点击此处重载页面</a>.</p>
<p>谢谢.</p>',


//album
'create_time'                      =>  '创建时间',
'upload_time'                      =>  '上传时间',
'photo_nums'                       =>  '照片数',
'album_name_empty'                 =>  '相册名不能为空！',
'album_password_empty'             =>  '密码不能为空！',
'album_question_empty'             =>  '问题不能为空！',
'album_answer_empty'               =>  '答案不能为空！',
'create_album_success'             =>   '创建相册成功！',
'create_album_failed'              =>  '创建相册失败！',
'modify_album_success'             =>  '修改相册成功！',
'modify_album_failed'              =>  '修改相册失败！',
'set_cover_success'                =>'成功设为封面！',
'set_cover_failed'                 =>  '未能成功设为封面！',
'delete_album_success'             => '成功删除相册！',
'delete_album_failed'              => '删除相册失败！',
'pls_sel_album_to_delete'          =>'请先选择要删除的相册！',
'batch_delete_album_success'       => '成功批量删除相册！',
'batch_delete_album_failed'        => '批量删除相册失败！',
'failed_to_rename_album'           =>  '修改相册名失败！',
'modify_tags_failed'               =>  '编辑相册标签失败！',
'empty_album_desc'                 =>  '相册描述不能为空！',
'modify_album_desc_failed'         =>  '编辑相册描述失败！',
'modify_album_priv_success'        =>  '修改相册权限成功！',
'modify_album_priv_failed'         =>  '修改相册权限失败！',
//album html
'total_ablum'                      =>    '共%s个相册',
'album_list'                       =>    '相册列表',
'create_new_album'                 =>    '创建新相册',
'notice_title'                     =>  '提示',
'confirm_delete_album'             => '确定要删除相册 “%s” 么？删除后的相册及相册内图片可以在“回收站”恢复！',
'confirm_delete_album_batch'       => '确定要删除这些相册么？删除后的相册可以在“回收站”恢复！',
'create_album'                     =>  '创建相册',
'album_name'                       =>  '相册名',
'album_desc'                       =>  '相册描述',
'priv_setting'                     =>  '权限设置',
'type_private'                     =>   '私人',
'type_public'                      =>   '公开',
'type_passwd'                      =>   '密码访问',
'type_ques'                        =>   '问题访问',
'input_passwd'                     =>  '输入密码',
'show_pass'                        =>  '显示密码',
'input_question'                   =>  '输入问题',
'input_answer'                     =>  '问题答案',
'input_question_tips'              =>  '举例：我的名字？',
'input_answer_tips'                =>  '举例：张三',
'album_tags'                       => '相册标签',
'tags_tips'                        =>    '用空格或,分割',
'modify_album'                     =>  '修改相册',
'move_to_trash'                    => '移动到回收站',
'move_to_trash_short'              => '删除',
'click_to_rename'                  => '点击重命名',
'photos_num'                       =>  '%s张照片',
'delete_selected'                  => '删除选中项',
'create_after_login'               => '登录后创建',
'view_all_album'                   =>  '查看所有相册',
'in_upload_time'                   =>  '上传于',
'in_create_time'                   =>  '创建于',
'modify_album_priv'                =>  '修改相册权限',

//photo
'upload_time'                      =>  '上传时间',
'taken_time'                       =>   '拍摄时间',
'hits'                             =>  '浏览数',
'comments_nums'                    =>   '评论数',
'photo_name'                       =>  '照片名',
'album_not_exists'                 =>   '您要访问的相册不存在！',
'view_type'                        =>  '浏览模式',
'flat_mode'                        =>  '平铺模式',
'slide_mode'                       => '幻灯模式',
'album_pass_error'                 =>  '相册密码输入错误！',
'validate_success'                 =>  '验证成功！',
'album_answer_error'               =>  '相册答案输入错误！',
'album_priv_error'                 =>  '相册权限错误！',
'has_validate'                     =>  '已认证，正在转入...',
'title_need_validate'              =>    '访问需要验证',
'slideshow'                        =>  '幻灯片',
'search_result'                    => '搜索结果',
'photo_name_empty'                 => '照片名不能为空！',
'modify_photo_success'             =>  '修改照片信息成功！',
'modify_photo_failed'              =>  '修改照片信息失败！',
'havnt_sel_album'                  =>  '您没有选择要移动至的相册！',
'move_photo_success'               =>  '移动照片成功！',
'move_photo_failed'                =>  '移动照片失败！',
'pls_sel_photo_want_to_move'       =>  '请先选择要移动的照片！',
'batch_move_photo_success'         =>   '成功批量移动照片！',
'batch_move_photo_failed'          =>   '批量移动照片失败！',
'delete_photo_success'             =>  '成功删除照片！',
'delete_photo_failed'              =>  '删除照片失败！',
'pls_sel_photo_want_to_delete'     =>  '请先选择要删除的照片！',
'batch_delete_photo_success'       => '成功批量删除照片！',
'batch_delete_photo_failed'        => '批量删除照片失败！',
'save_photo_name_failed'           =>  '照片名保存失败！',
'photo_not_exists'                 =>  '您要访问的照片不存在！',
'no_access_view_exif'              =>  '无权查看EXIF！',
'view_photo_exif'                  => '查看照片%s的EXIF信息',
'view_exif'                        => '查看EXIF信息',
'modify_photo_tags_failed'         =>  '编辑照片标签失败！',
'empty_photo_desc'                 =>  '照片描述不能为空！',
'modify_photo_desc_failed'         =>  '编辑照片描述失败！',
'confirm_delete_photo'             =>  '确定要删除图片 “%s” 么？删除后的图片可以在“回收站”恢复！',
'confirm_delete_photo_batch'       =>  '确定要删除这些图片么？删除后的图片可以在“回收站”恢复！',
//photo html
'photo_list'                       =>    '照片列表',
'total_photo'                      =>    '共%s张图片',
'upload_new_photo'                 =>    '上传新照片',
'set_cover'                        =>  '设为封面',
'move_photo'                       =>  '移动照片',
'in_taken_time'                    =>  '拍摄于',
'view_nums'                        =>  '%s浏览',
'cover'                            =>  '封面',
'move_selected'                    =>  '移动选中项',
'all_photo_this_album'             =>   '此相册所有照片',
'click_editable'                   =>  '点击可编辑',
'no_album_desc'                    =>  '还没有描述，为相册添加描述吧！',
'no_album_tags'                    =>  '点击添加标签吧！',
'view_priv'                        =>  '访问权限',
'create_date'                      => '创建日期：',
'uploaded_date'                    => '最近上传：',
'current_album'                    =>  '当前相册',
'all_album'                        =>  '所有相册',
'go_back'                          =>  '返回页面',
'view_more_meta'                   =>  '查看照片%s的更多信息',
'modify_photo'                     =>  '修改照片信息',
'photo_name'                       =>  '照片名',
'photo_desc'                       =>  '照片描述',
'photo_tags'                       =>  '照片标签',
'move_photo'                       =>  '移动照片',
'move_photo_short'                 =>  '至相册',
'move_photo_to'                    =>  '移动照片到',
'move_photo_batch'                 =>  '批量移动照片',
'album_need_auth'                  =>  '相册“%s”需要认证',
'pls_input_passwd'                 =>  '请输入访问密码',
'question'                         =>  '问题',
'pls_input_answer'                 =>  '请输入答案',
'owner_could'                      =>  '如果您是相册拥有者，您可以',
'you_can_also'                     =>  '您也可以',
'view_photo'                       =>  '查看照片',
'photo_nav_title'                  =>   '当前第%s张，共%s张',
'back_to_photo_list'               =>  '返回照片列表',
'first_photo'                      =>  '第一张',
'prev_photo'                       =>  '上一张',
'next_photo'                       =>  '下一张',
'last_photo'                       =>  '最后张',
'slideshow_view'                   =>  '幻灯浏览',
'image_size'                       =>  '图片尺寸',
'taken_width'                      =>  '由%s拍摄',
'more_exif'                        =>  '更多Exif',
'viewed_nums'                      =>  '被查看了%s次',
'view_orgi_photo'                  =>  '查看原图',
'no_photo_desc'                    =>  '还没有描述，为照片添加描述吧！',
'no_photo_tags'                    =>  '点击添加标签吧！',
'post_comments'                    =>  '发表评论',
'this_first_photo'                 =>  '这是首张',
'this_last_photo'                  =>  '这是末张',

//tags
'tag_list'                         =>  '标签列表',
'search_tag'                       =>  '标签：%s',
//tags html
'no_tags'                          =>  '当前没有标签！',
//users
'modify_profile'                   =>  '修改个人资料',
'username_empty'                   =>  '请输入用户名！',
'userpass_empty'                   =>  '请输入密码！',
'login_success'                    =>  '登录成功！',
'username_pass_error'              =>  '请验证用户名和密码是否正确！',
'old_pass_error'                   =>  '旧密码输入错误！',
'pass_twice_error'                 =>  '两次密码输入不一致！',
'modify_success'                   =>  '修改成功！',
'modify_failed'                    =>  '修改失败！',
'pass_edit_ok'                     =>  '您的密码已经修改，请重新登录！',
'logout_success'                   =>  '退出登录成功！',


//users html
'user_login'                       =>  '用户登录',
'username'                         =>  '用户名',
'password'                         =>  '密码',
'remember_pass'                    =>  '记住密码',
'my_profile'                       =>  '我的资料',
'loginname'                        =>  '登录名',
'nickname'                         =>  '昵称',
'old_passport'                     =>  '原始密码',
'new_passport'                     =>  '新密码',
'confirm_newpass'                  =>  '确认新密码',


'photo_has_priv'                   =>  '图片设置了访问权限，您无权查看！',

'album_type_private'               =>    '私人相册',
'album_type_public'                =>    '公开相册',
'album_type_passwd'                =>    '凭密码访问',
'album_type_ques'                  =>    '凭问题答案',

//search
'search_albums'                    =>    '搜索相册',
'search_photos'                    =>    '搜索照片',
'search'                           =>    '搜索',
'search_s'                         =>    '搜索：%s',
//comments languages
'comments_num'                     =>    '%s评论',
'all_album_comments'               =>    '对该相册的评论',
'all_photo_comments'               =>    '对该照片的评论',
't_comments_num'                   =>    '共%s个评论',
'email'                            =>    'Email',
'comment_user'                     =>    '评论者',
'comment_content'                  =>    '评论内容',
'album_comment_closed'             =>  '相册关闭了评论！',
'error_email'                      =>  '请输入有效的Email地址！',
'error_comment_author'             =>   '请输入评论者名字！',
'empty_content'                    =>    '内容不能为空！',
'miss_argument'                    =>    '参数丢失！',
'post_comment_success'             =>  '评论成功！',
'post_comment_failed'              =>  '评论失败！',
'reply_failed'                     =>  '回复失败！',
'delete_comment_success'           => '成功删除评论!',
'delete_comment_failed'            => '删除评论失败!',
'block_comment_success'            => '成功屏蔽评论！',
'block_comment_failed'             => '屏蔽评论失败！',
'loginwith'                        =>  '以 %s 的身份登录。',

'confirm_delete_comments'          =>  '确定要删除这条评论么？删除后无法恢复！',
'reply'                            =>  '回复',
'block'                            =>  '屏蔽',
'approve'                          =>  '获准',
'load_more_comments'               =>  '载入更多评论',
'comments_manage_title'            => '评论管理',
'comments_manage'                  => '评论管理',
'comments_manage_list_title'       => '评论列表',
'no_comments'                      =>  '当前还没有评论！',
'approve_comment_success'          =>  '审核评论成功！',
'approve_comment_failed'           =>  '审核评论失败！',
'pls_sel_comments_want_to_delete'  =>    '请选择需要删除的评论！',
'batch_delete_comments_success'    =>    '批量删除评论成功！',
'batch_delete_comments_failed'     =>    '批量删除评论失败！',
'pls_sel_comments_want_to_block'   =>    '请选择需要屏蔽的评论！',
'batch_block_comments_success'     =>    '批量屏蔽评论成功！',
'batch_block_comments_failed'      =>    '批量屏蔽评论失败！',
'pls_sel_comments_want_to_approve' =>    '请选择需要获准的评论！',
'batch_approve_comments_success'   =>    '批量获准评论成功！',
'batch_approve_comments_failed'    =>    '批量获准评论失败！',
'confirm_approve_comments_batch'   =>  '确定要获准这些评论么？仔细检查后按确定。',
'confirm_block_comments_batch'     =>  '确定要屏蔽这些评论么？屏蔽后游客将无法看到这些评论！',
'confirm_delete_comments_batch'    =>  '确定要删除这些评论么？删除后将无法恢复。',
'moderated'                        =>  '待审',
'blocked'                          =>  '已屏蔽',
'approved'                         =>  '已获准',
'reply_to'                         =>  '回应给',
'block_selected'                   =>  '屏蔽选中项',
'approve_selected'                 =>  '获准选中项',
'posted_at'                        =>  '提交于',
'replyed_to'                       =>  '回复给',

//trash
'recycle'                          =>  '回收站',
'real_delete_success'              =>  '彻底删除成功！',
'real_delete_failed'               =>  '彻底删除失败！',
'pls_sel_photo_album_del'          =>  '请先选择要彻底删除的照片/相册！',
'real_delete_batch_success'        =>  '成功批量删除！',
'restore_success'                  =>  '成功还原！',
'restore_failed'                   =>  '还原失败！',
'pls_sel_photo_album_restore'      =>  '请先选择要还原的照片/相册！',
'restore_batch_success'            =>  '成功批量还原！',
'empty_trash_success'              =>  '成功清空回收站!',
//trash html
'trash_is_empty'                   =>  '您的回收站是空的！',
'clear_recycle'                    =>  '清空回收站',
'real_delete'                      =>  '彻底删除',
'restore'                          =>  '还原',
'real_delete_selected'             =>  '彻底删除选中项',
'restore_selected'                 =>  '还原选中项',
'no_album_in_trash'                =>  '回收站中没有已删除的相册！',
'no_photo_in_trash'                =>  '回收站中没有已删除的照片！',
'confirm_real_delete'              =>  '确定要彻底删除 “%s” 么？删除后无法恢复！',
'confirm_real_delete_batch'        => '确定要删除这些图片/相册么？删除后的无法恢复！',
'confirm_emptying_trash'           => '确定清空回收站么？删除后的无法恢复！',
'confirm_restore_batch'            =>  '确定要还原这些图片/相册么？',

//upload
'pls_login_before_upload'          =>  '请先登录后上传',
'pls_sel_album'                    =>  '请先选择相册！',
'upload_photo_success'             =>  '上传照片成功！',
'view_album'                       =>  '查看相册',
'need_sel_upload_file'             =>  '您没有选择图片上传，请重新上传！',
'file_upload_failed'               =>  '文件%s上传失败！',
'failed_larger_than_server'        =>  '文件%s上传失败:文件大小超过服务器限制！',
'failed_larger_than_usetting'      =>  '文件%s上传失败:大小超过用户限制！',
'failed_if_file'                   =>  '文件%s上传失败:请确认上传的是否为文件！',
'failed_not_support'               =>  '文件%s上传失败:不支持此格式！',
//u html
'switch_upload_type'               =>  '切换上传方式',
'expert_mode'                      =>  '高级上传',
'normal_mode'                      =>  '普通模式',
'select_album'                     =>  '选择相册',
'new_album'                        =>  '新建相册',
'upload_immediatly'                =>  '立即上传',
'loading'                          =>  '载入中...',
'if_no_response_click_here'        =>  '如果长时间没有响应，可以点此处切换至普通上传方式！',
'must_upload_one'                  =>  '至少选择一个文件上传.',
'filename'                         =>  '文件名',
'status'                           =>  '状态',
'size'                             =>  '大小',
'add_file'                         =>  '添加图片',
'stop_upload'                      =>  '停止上传',
'start_upload'                     =>  '开始上传',
'upload_status'                    =>  '已上传 %%d/%%d 图片',
'drag_file_here'                   =>  '拖拽文件至此处.',
'Failed to save file.'             =>  '文件上传失败！',

//Exif languages
'exif_Make'                        => '相机品牌',
'exif_Model'                       => '相机型号',
'exif_ApertureFNumber'             => '光圈',
'exif_ExposureTime'                => '曝光时间',
'exif_Flash'                       => '闪光灯',
'exif_FocalLength'                 => '焦距',
'exif_FocalLengthIn35mmFilm'       => '35mm等效焦距',
'exif_ISOSpeedRatings'             => 'ISO感光度',
'exif_WhiteBalance'                => '白平衡',
'exif_ExposureBiasValue'           => '曝光补偿',
'exif_DateTimeOriginal'            => '拍摄时间',
'exif_FocusDistance'               => '对焦距离',
'exif_FileSize'                    => '文件大小',
'exif_MimeType'                    => '文件类型',
'exif_Width'                       => '图片宽度',
'exif_Height'                      => '图片高度',
'exif_Orientation'                 => '方向',
'exif_XResolution'                 => '水平分辨率',
'exif_YResolution'                 => '垂直分辨率',
'exif_ResolutionUnit'              => '分辨率单位',
'exif_Software'                    => '创建软件',
'exif_DateTime'                    => '修改时间',
'exif_Artist'                      => '作者',
'exif_Copyright'                   => '版权',
'exif_MaxApertureValue'            => '最大光圈',
'exif_FNumber'                     => 'F-Number',
'exif_MeteringMode'                => '测光模式',
'exif_LightSource'                 => '光源',
'exif_ColorSpace'                  => '色彩空间',
'exif_ExposureMode'                => '曝光模式',
'exif_ExposureProgram'             => '曝光程序',
'exif_DateTimeDigitized'           => '数字化时间',
'exif_GPSLatitude'                 => '纬度',
'exif_GPSLongitude'                => '经度',

'standard_procedure'               =>  '标准程序',
'aperture_priority'                =>  '光圈先决',
'shutter_priority'                 =>  '快门先决',
'depth_priority'                   =>  '景深先决',
'sport_mode'                       =>  '运动模式',
'portrait_mode'                    =>  '肖像模式',
'landscape_mode'                   =>  '风景模式',
'top_left'                         =>  '上/左',
'top_right'                        =>  '上/右',
'bottom_right'                     =>  '下/右',
'bottom_left'                      =>  '下/左',
'left_top'                         =>  '左/上',
'right_top'                        =>  '右/上',
'right_bottom'                     =>  '右/下',
'left_bottom'                      =>  '左/下',
'in-ch'                            =>  '英寸',
'cm'                               =>  '厘米',

'avg'                              => "平均",
'center_weighted_average'          => "中央重点平均测光",
'point_measurement'                =>  "点测",
'zoning'                           =>  "分区",
'assess'                           =>  "评估",
'portion'                          =>  "局部",
'sun_light'                        =>  "日光",
'fluorescent'                      =>  "荧光灯",
'tungsten'                         =>  "钨丝灯",
'flash_lamp'                       =>  "闪光灯",
'standard_lighting_A'              => "标准灯光A",
'standard_lighting_B'              => "标准灯光B",
'standard_lighting_C'              => "标准灯光C",
'd55'                              => "D55",
'd65'                              => "D65",
'd75'                              => "D75",

'open1'                            =>          "打开(不探测返回光线)",
'open2'                            =>          "打开(探测返回光线)",
'open3'                            =>          "打开(强制)",
'open4'                            =>          "打开(强制/不探测返回光线)",
'open5'                            =>          "打开(强制/探测返回光线)",
'open6'                            =>          "关闭(强制)",
'close1'                           =>           "关闭(自动)",
'open7'                            =>          "打开(自动)",
'open8'                            =>          "打开(自动/不探测返回光线)",
'open9'                            =>          "打开(自动/探测返回光线)",
'no_flash'                         =>             "没有闪光功能",
'open10'                           =>           "打开(防红眼)",
'open11'                           =>           "打开(防红眼/不探测返回光线)",
'open12'                           =>           "打开(防红眼/探测返回光线)",
'open13'                           =>           "打开(强制/防红眼)",
'open14'                           =>           "打开(强制/防红眼/不探测返回光线)",
'open15'                           =>           "打开(强制/防红眼/探测返回光线)",
'open16'                           =>           "打开(自动/防红眼)",
'open17'                           =>           "打开(自动/防红眼/不探测返回光线)",
'open18'                           =>           "打开(自动/防红眼/探测返回光线)",

//setting
'system_setting'                   =>  '系统设置',
'basic_setting'                    =>  '基本设置',
'empty_site_name'                  =>  '站点名称不能为空！',
'empty_site_url'                   =>  '相册URL不能为空！',
'save_setting_success'             =>  '保存设置成功！',
'save_setting_failed'              =>  '保存设置失败！',
'upload_setting'                   =>  '上传设置',
'resize_width_error'               =>  '图片的最大宽度不能为空，并且必须为数字！',
'resize_height_error'              =>  '图片的最大高度不能为空，并且必须为数字！',
'resize_quality_error'             =>  '图片质量必须介于1-100！',
'watermark_setting'                =>  '水印设置',
'water_mark_image_error'           =>  '图片水印地址不能为空！',
'water_mark_opacity_error'         =>  '水印透明度必须介于0-100！',
'water_mark_string_error'          =>  '水印文字内容不能为空！',
'water_mark_fontsize_error'        =>  '水印文字大小必须大于1！',
'water_mark_color_error'           =>  '水印文字颜色不是有效的颜色！',
'water_mark_font_error'            =>  '请选择水印文字字体！',
'water_mark_angle_error'           =>  '水印文字角度必须在0-360度之间！',
'water_mark_opacity_error'         =>  '水印透明度必须介于0-100！',
'upload_error'                     =>  '上传失败！',
'theme_setting'                    =>  '主题设置',
'enable_success'                   =>  '启用成功！',
'empty_theme'                      =>  '请确认要删除的主题是否存在！',
'can_not_delete_default'           =>  '默认主题不能删除！',
'theme_is_using'                   =>  '此主题正在使用中，无法删除！',
'delete_theme_success'             =>  '成功删除主题！',
'delete_theme_failed'              =>  '删除主题失败！',
'user_theme'                       =>  '用户主题',
'plugin_setting'                   =>  '插件管理',
'install_plugin_success'           =>  '安装插件成功！',
'install_plugin_failed'            =>  '安装插件失败！',
'enable_plugin_success'            =>  '启用插件成功！',
'enable_plugin_failed'             =>  '启用插件失败！',
'stop_plugin_success'              =>  '停用插件成功！',
'stop_plugin_failed'               =>  '停用插件失败！',
'remove_plugin_success'            =>  '删除插件成功！',
'remove_plugin_failed'             =>  '删除插件失败！',
'system_info'                      =>  '系统信息',
'clear_cache_success'              =>  '清空缓存成功！',

'site_title_label'                 =>  '站点名称',
'site_title_tips'                  =>  '显示在每个页面的最顶端',

'site_url_label'                   =>  '你的相册URL',
'site_url_tips'                    =>  '图片地址及超链接的前缀，请保留最后的"/"',
'site_keywords_label'              =>  '你的相册默认关键字',
'site_keywords_tips'               =>  '便于搜索引擎抓取，meta keywords',
'site_logo_label'                  =>  '相册LOGO',
'site_logo_tips'                   =>  '显示于页面左上角，请上传logo或填入logo的相对地址',

'site_description_label'           =>  '你的相册描述',
'site_description_tips'            =>  '便于搜索引擎抓取，meta description',
'site_footer_label'                =>  '页面底部代码',
'site_footer_tips'                 =>  '可以插入备案号，统计代码等，支持html',
'show_process_info_label'          =>  '显示页脚程序运行信息',
'show_process_info_tips'           =>  '包括页面执行时间和数据库请求次数',
'enable_comment_label'             =>  '是否允许评论',
'enable_comment_tips'              =>  '如果关闭此选项，用户无法对所有相册/照片进行评论',
'enable_auto_update'               =>  '是否自动检查更新',
'enable_auto_update_tips'          =>  '自动从官网检查是否有更新',
'gravatar_url_label'               =>  'Gravatar头像地址设置',
'gravatar_url_tips'                =>  '系统将自动替换{idstring}为相应的gravatar_id',

'save_setting'                     =>  '保存设置',
'cache_size'                       =>  '缓存大小：',
'clear_all_cache'                  =>  '清空所有缓存',
'more_system_info'                 =>  '更多系统信息',
'edit_plugin_setting'              =>  '编辑插件配置',
'in_safe_mode'                     =>  '您当前处在安全模式，所有插件均未生效，若想使用插件请关闭安全模式！',
'no_plugins'                       =>  '没有任何可用的插件！',
'plugin_id'                        =>  '插件id',
'plugin_name'                      =>  '插件名',
'plugin_desc'                      =>  '插件介绍',
'version'                          =>  '版本',
'developer'                        =>  '开发者',
'status'                           =>  '状态',
'operate'                          =>  '操作',
'confirm_delete_theme'             =>  '确定要删除主题 “%s” 么？',
'edit_style'                       =>  '编辑风格',
'old_imgname_label'                =>  '是否使用原文件名保存',
'old_imgname_tips'                 =>  '如果文件名非中文，则按原文件名保存。',
'enable_pre_resize_label'          =>  '是否开启客户端预处理',
'enable_pre_resize_tips'           =>  '启用此选项，在高级模式下会自动将大图片缩小，然后再上传，有利于大大减少网络传输，缩短上传时间。',
'upload_pre_resize_label'          =>  '客户端预处理图片尺寸 (宽度/高度)',
'upload_pre_resize_tips'           =>  '当图片超过指定宽度和高度时在浏览器中自动缩放图片',
'enable_cut_big_pic_label'         =>  '自动裁剪大图片',
'enable_cut_big_pic_tips'          =>  '当图片超过指定宽度和高度时自动缩放图片',
'max_picture_size_label'           =>  '最大图片大小 (宽度/高度)',
'max_picture_size_tips'            =>  '大于该指定大小的图片自动按比例缩放',
'thumb_size_label'                 =>  '缩略图尺寸 (宽度/高度)',
'thumb_size_tips'                  =>  '小于该指定大小的图片将不生成缩略图,已经上传过的图片不会重新处理',
'cut_thumb'                        =>  '裁剪',
'upload_resize_quality_label'      =>  '图片质量',
'upload_resize_quality_tips'       =>  '处理图片的质量 1-100',
'upload_allow_size_label'          =>  '允许上传的图片大小',
'upload_allow_size_tips'           =>  '请谨慎选择，如果空间服务商配置有限制，尺寸过大可能会导致系统瘫痪，高级上传不受影响',
'watermark_type_label'             =>  '是否启用水印',
'watermark_type_tips'              =>  '启用此选项，会在每张上传的图片上打上水印，可以防止别人盗用图片。',
'enable_img_wm'                    =>  '启用图片水印',
'enable_font_wm'                   =>  '启用文字水印',
'water_mark_image_label'           =>  '图片水印地址',
'water_mark_image_tips'            =>  '请上传水印图片，或填入水印图片的相对地址',
'upload'                           =>  '上传',
'view'                             =>  '查看',
'water_mark_string_label'          =>  '水印文字',
'water_mark_string_tips'           =>  '水印文字内容',
'water_mark_font_label'            =>  '水印文字字体',
'water_mark_font_tips'             =>  '请把所需的字体文件上传到相册服务器根目录下的/statics/font文件夹中，字体文件位于本机C:\WINDOWS\Fonts下，例如文件SimSun.ttc表示宋体',
'water_mark_fontsize_label'        =>  '水印文字大小',
'water_mark_fontsize_tips'         =>  '水印文字大小设置，单位为px',
'water_mark_color_label'           =>  '水印文字颜色',
'water_mark_color_tips'            =>  '请使用HEX颜色代码。如:#332211',
'water_mark_angle_label'           =>  '水印角度',
'water_mark_angle_tips'            =>  '角度可取值范围为0-360度，逆时针方向（即如果值为 90 则表示从下向上阅读文本）',
'water_mark_opacity_label'         =>  '水印透明度',
'water_mark_opacity_tips'          =>  '透明度请设置为0-100之间的数字，0代表完全透明，100代表不透明。若水印图片本身透明请填0',
'water_mark_pos_label'             =>  '水印位置',
'water_mark_pos_label'             =>  '设置水印位置',
'pos_topleft'                      =>  '顶部居左',
'pos_topcenter'                    =>  '顶部居中',
'pos_topright'                     =>  '顶部居右',
'pos_centerleft'                   =>  '左部居中',
'pos_center'                       =>  '图片中心',
'pos_centerright'                  =>  '右部居中',
'pos_bottomleft'                   =>  '底部居左',
'pos_bottomcenter'                 =>  '底部居中',
'pos_bottomright'                  =>  '底部居右',
'pos_random'                       =>  '随机',

'language_and_locale'              =>  '区域语言设置',
'system_language_label'            =>  '选择系统语言',
'system_language_tips'             =>  '如果没有您要的语言可以从meiupic.meiu.cn下载相应的语言包',
'system_timeoffset_label'          =>  '选择时区',
'system_timeoffset_tips'           =>  '请选择所在地的时区',
'empty_langset'                    =>  '请选择语言！',
'empty_timezone'                   =>  '请选择时区！',

//系统信息
'meiupic_version'                  =>  '相册系统版本',
'operate_system'                   =>  '操作系统',
'server_software'                  =>  '服务器软件',
'php_runmode'                      =>  'php运行模式',
'php_version'                      =>  'php版本',
'mysql_support'                    =>  '是否支持Mysql',
'mysqli_support'                   =>  '是否支持Mysqli',
'sqlite_support'                   =>  '是否支持Sqlite',
'database_version'                 =>  '数据库及版本',
'gd_info'                          =>  'GD库',
'imagick_support'                  =>  'Imagick扩展',
'exif_support'                     =>  'Exif支持',
'zlib_support'                     =>  '是否支持Zlib',
'support'                          =>  '支持',
'notsupport'                       =>  '不支持',

'nothing_to_do'                    =>  '未做任何操作！',
'recounter_success'                =>  '更新统计成功！',
'recounter'                        =>  '更新统计',
'comment_recounter'                =>  '评论数重计',
'photo_recounter'                  =>  '照片数重计',
'tag_recounter'                    =>  '标签数重计',
'check_update'                     =>  '检查更新',
'connect_to_server_failed'         =>  '连接服务器失败！',

'sel_album_to_upload'              => '选择要上传的相册',
'you_chose_album'                  => '您选择了相册',
'back_to_re_select'                => '返回重选',

//翻转图片
'rotate_image'                     => '旋转图片',
'rotate_image_short'               => '旋转',
'rotate_left_90'                   => '向左旋转90°',
'rotate_right_90'                  => '向右旋转90°',
'do_nothing'                       => '您没有做任何操作！',
'rotate_image_success'             => '旋转图片成功！',
'rotate_image_failed'              => '旋转图片失败！',

//重新上传
'new_photo'                        => '新照片',
'reupload_photo'                   => '重新上传照片',
'reupload_photo_short'             => '重新上传',

//评论验证码//added 12-07-11
'captcha_code'                     => '验证码',
'click_to_reload'                  => '点击刷新',
'invalid_captcha_code'             => '验证码输入错误!',
'enable_comment_captcha_label'     => '启用评论验证码',
'enable_comment_captcha_tips'      => '游客必须输入验证码才能提交评论',
'comment_audit_label'              => '评论审核方式',
'comment_audit_tips'               => '如果选择直接获准，将不需要管理员审核直接显示',
'comment_audit_auto'               => '直接获准',
'comment_audit_manual'             => '手动审核',
'import_mode'                      => '导入模式',
'scan_dir'                         => '需要扫描的文件夹',
'auto_del_added'                   => '添加后自动删除图片',
'save_mode'                        => '保存方式',
'save_to_current_album'            => '存入当前相册',
'create_dir_by_folder'             => '按照文件夹名创建新相册',
'scan_dir_not_exists'              => '要扫描的文件夹不存在！',
'dir_cannot_read'                  => '文件夹不可读！',
'dir_has_no_files'                 => '该目录没有任何文件！',
'import_success'                   => '成功扫描并添加了: %s个相册,%s张图片！',
'display_setting'                  => '显示设置',
'album_sort_default_label'         => '相册列表默认排序',
'album_pageset_label'              => '相册列表默认每页显示',
'photo_sort_default_label'         => '照片列表默认排序',
'photo_pageset_label'              => '照片列表默认每页显示',
'desc'                             => '逆序',
'asc'                              => '正序',
'get_cache_size'                   => '计算缓存大小',
//added 12-09-28
'enable_login_captcha_label'       =>  '启用登录验证码',
'enable_login_captcha_tips'        =>  '启用后登录时必须输入验证码',
'thank_list'                       =>  '感谢者名单',
'donatenow'                        =>  '<a href="https://me.alipay.com/meiu" target="_blank"><img src="{base_path}statics/img/donate.png" /></a>',

//分类
'category'                         => '分类',
'category_manage'                  => '分类管理',
'category_list'                    => '分类列表',
'all_category'                     => '所有分类',
'category_name_empty'              => '分类名不能为空！',
'create_category_succ'             => '创建分类成功！',
'create_category_fail'             => '创建分类失败！',
'edit_category_succ'               => '编辑分类成功！',
'edit_category_fail'               => '编辑分类失败,上级分类不能是自己或子分类!',
'confirm_delete_category'          => '确定要删除分类“%s”吗？分类下的相册将自动移动到未分类相册！',
'create_category'                  => '创建分类',
'create_sub_category'              => '创建子分类',
'category_name'                    => '分类名',
'parent_category'                  => '上级分类',
'no_parent'                        => '无上级',
'back_to_manage_album'             => '返回相册管理',
'delete_cate_succ'                 => '删除分类成功！',
'delete_cate_fail'                 => '删除分类失败！请先删除子分类！',
'not_cate'                         => '未分类',
'belong_category'                  => '所属的分类',
'add_to_nav'                       => '添加到菜单',
//自定义菜单
'setting_nav'                      => '自定义菜单',
'delete?'                          => '删?',
'add_menu'                         => '添加菜单',
'nav_sort'                         => '排序',
'nav_name'                         => '名称',
'nav_url'                          => '链接',
'nav_inside'                       => '内置',
'nav_custom'                       => '自定义',
'nav_save_succ'                    => '保存成功！',
'nav_save_fail'                    => '部分内容保存失败，请确认是否内容都填写正确了！',
'no_cate_album'                    => '未分类相册',

//分享按钮提示设置
'share_title_label'                => '自定义分享内容设置',
'share_title_tips'                 => '{name}为照片名',

'side_category'                    => '按分类筛选相册',
'share_title'                      => '分享张很赞的照片:{name}',

//自动升级
'your_system_is_up_to_date'        => '您的系统是最新的！',
'new_update_available'             => '检测到新的版本：<strong>%s</strong> ,发布日期：<strong>%s</strong> ！',
'update_immediately'               => '立刻升级',
'no_need_to_update'                => '无需升级！',
'version_can_not_be_empty'         => '版本号不能为空！',
'dir_not_writable'                 => '目录：%s 不可写！',
'file_not_writable'                => '文件：%s 不可写！',
'file_has_been_downloaded'         => '文件已下载！',
'download_package_failed'          => '下载升级包失败！',
'download_package_succ'            => '下载文件成功！',
'unzip_package_succ'               => '解压文件成功！',
'delete_tmp_download_file'         => '删除下载的临时文件！',
'upgrade_after_jump'               => '跳转后执行升级脚本！',
'get_update_fail'                  => '获取更新失败！',
'have_been_updated'                => '已经升级过了！',
'could_not_degrade'                => '脚本无法执行降级操作！',
'too_old_to_update'                => '对不起, 您的版本太旧！无法自动升级！',
'upgrade_success'                  => '升级成功，跳转至首页！',
'click_to_jump'                    => '点击此处跳转',
'update_manually'                  => '下载手动安装',
'or'                               => '或',
'upgrade_title'                    => 'MeiuPic 版本升级 - 升级到%s',
'upgrade_need_login'               => '只有管理员才能进行升级操作：请先登录。',
);