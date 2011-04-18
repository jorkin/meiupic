
-- --------------------------------------------------------

--
-- 表的结构 `meu_albummeta`
--

CREATE TABLE IF NOT EXISTS `meu_albummeta` (
  `ameta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`ameta_id`),
  KEY `album_id` (`album_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_albums`
--

CREATE TABLE IF NOT EXISTS `meu_albums` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `cover_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `cover_ext` varchar(20) DEFAULT NULL,
  `comments_num` int(11) unsigned NOT NULL DEFAULT '0',
  `photos_num` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `up_time` int(11) unsigned NOT NULL DEFAULT '0',
  `tags` varchar(255) DEFAULT NULL,
  `priv_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0,开放  1,凭密码 2,凭问题及答案 3,私人',
  `priv_pass` varchar(100) DEFAULT NULL,
  `priv_question` varchar(255) DEFAULT NULL,
  `priv_answer` varchar(255) DEFAULT NULL,
  `desc` longtext,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cover_id` (`cover_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_commentmeta`
--

CREATE TABLE IF NOT EXISTS `meu_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_comments`
--

CREATE TABLE IF NOT EXISTS `meu_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1, 相册评论 2，照片评论',
  `ref_id` bigint(20) unsigned NOT NULL,
  `quote_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `reply_author` varchar(100) DEFAULT NULL,
  `author_ip` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ref_id` (`ref_id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_photometa`
--

CREATE TABLE IF NOT EXISTS `meu_photometa` (
  `pmeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`pmeta_id`),
  KEY `photo_id` (`photo_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_photos`
--

CREATE TABLE IF NOT EXISTS `meu_photos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `album_id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `hits` bigint(20) NOT NULL DEFAULT '0',
  `comments_num` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `taken_time` int(11) NOT NULL DEFAULT '0',
  `taken_y` smallint(4) NOT NULL DEFAULT '0' COMMENT '拍摄年',
  `taken_m` tinyint(2) NOT NULL DEFAULT '0' COMMENT '拍摄月',
  `taken_d` tinyint(2) NOT NULL DEFAULT '0' COMMENT '拍摄日',
  `create_y` smallint(4) NOT NULL DEFAULT '0' COMMENT '创建年',
  `create_m` tinyint(2) NOT NULL DEFAULT '0' COMMENT '创建月',
  `create_d` tinyint(2) NOT NULL DEFAULT '0' COMMENT '创建日',
  `desc` longtext,
  `exif` text,
  `tags` varchar(255) DEFAULT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `album_id` (`album_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_plugins`
--

CREATE TABLE IF NOT EXISTS `meu_plugins` (
  `plugin_id` varchar(32) NOT NULL,
  `plugin_name` varchar(200) NOT NULL,
  `description` varchar(255) NOT NULL,
  `plugin_config` longtext,
  `local_ver` varchar(20) NOT NULL,
  `remote_ver` varchar(20) DEFAULT NULL,
  `available` enum('true','false') NOT NULL DEFAULT 'false',
  `author_name` varchar(100) DEFAULT NULL,
  `author_url` varchar(100) DEFAULT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_setting`
--

CREATE TABLE IF NOT EXISTS `meu_setting` (
  `name` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `meu_setting` (`name`, `value`) VALUES
('system', 'a:6:{s:7:"baoling";s:6:"sdfsdf";s:11:"path_suffix";s:2:"sd";s:13:"current_theme";s:1:"1";s:19:"current_theme_style";s:7:"default";s:8:"sdfsdfsf";s:7:"baoling";s:8:"username";s:7:"baoling";}'),
('site', 'a:3:{s:5:"title";s:12:"我的相册";s:8:"keywords";s:26:"相册,我的相册,分享";s:11:"description";s:90:"我的相册是使用美优相册管理系统架设的网络相册！相册开源免费！";}');
-- --------------------------------------------------------

--
-- 表的结构 `meu_tags`
--

CREATE TABLE IF NOT EXISTS `meu_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1,相册 2，照片',
  `tag_name` varchar(100) NOT NULL,
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_tag_rel`
--

CREATE TABLE IF NOT EXISTS `meu_tag_rel` (
  `tag_id` bigint(20) unsigned NOT NULL,
  `rel_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`tag_id`,`rel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_themes`
--

CREATE TABLE IF NOT EXISTS `meu_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `cname` varchar(200) NOT NULL,
  `copyright` text NOT NULL,
  `config` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `meu_themes` (`id`, `name`, `cname`, `copyright`, `config`) VALUES
(1, '默认模版', 'default', '美优网络', 'a:16:{s:10:"link_color";s:7:"#1d64ad";s:16:"link_hover_color";s:4:"#fff";s:9:"header_bg";s:4:"#036";s:9:"header_h1";s:4:"#fc0";s:10:"settingtxt";s:4:"#fff";s:9:"tablinkbg";s:7:"#1A4F85";s:7:"tablink";s:4:"#fff";s:14:"tablinkcurrent";s:4:"#090";s:12:"tablinkhover";s:4:"#fff";s:14:"tablinkhoverbg";s:7:"#1D64AD";s:10:"updateinfo";s:4:"#fff";s:12:"updateinfobg";s:7:"#1A4F85";s:10:"alertcolor";s:7:"#ffb2b2";s:7:"titlebg";s:7:"#edf3fe";s:10:"titlelabel";s:4:"#036";s:9:"boxborder";s:4:"#036";}');

-- --------------------------------------------------------

--
-- 表的结构 `meu_usermeta`
--

CREATE TABLE IF NOT EXISTS `meu_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`umeta_id`),
  KEY `userid` (`userid`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `meu_users`
--

CREATE TABLE IF NOT EXISTS `meu_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_pass` varchar(50) NOT NULL,
  `user_nicename` varchar(100) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `meu_users` (`id`, `user_name`, `user_pass`, `user_nicename`, `create_time`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 1280644129);