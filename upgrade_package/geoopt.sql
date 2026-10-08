-- ============================================================
-- GEO（生成式引擎优化）模块 安装 SQL
-- 1. 建 FAQ 表 v9_geo_faq
-- 2. 后台三级菜单：GEO优化(顶级) -> 优化管理(分组) -> 效果统计/内容优化列表/FAQ管理/GEO配置(三级)
-- 3. 注册 geoopt 到 v9_module（后台 module_exists 判定需要）
-- 幂等：表用 CREATE IF NOT EXISTS；菜单先查再插
-- 注：模块文件需放入 phpcms/modules/geoopt/、phpcms/model/geoopt_model.class.php、
--     phpcms/languages/zh-cn/geoopt.lang.php，并在 system_menu.lang.php 追加
--     menu_geoopt/menu_geoopt_group/geoopt_index/geoopt_list/geoopt_faq/geoopt_config 语言定义
-- ============================================================

CREATE TABLE IF NOT EXISTS v9_geo_faq (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  contentid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联内容ID',
  modelid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  question varchar(255) NOT NULL DEFAULT '' COMMENT '问题',
  answer text COMMENT '回答',
  listorder smallint(6) NOT NULL DEFAULT '0',
  inputtime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY contentid (contentid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 注册 geoopt 模块到 v9_module（幂等）
INSERT INTO v9_module (module,name,url,iscore,version,description,setting,listorder,disabled,installdate,updatedate)
VALUES ('geoopt','GEO优化','geoopt/',0,'1.0','生成式引擎优化(大模型优化)','',0,0,NOW(),NOW())
ON DUPLICATE KEY UPDATE name='GEO优化', disabled=0;

-- 顶级菜单「GEO优化」（不存在才插入）
INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'menu_geoopt',0,'geoopt','admin_geoopt','init','',99,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND c='admin_geoopt' AND parentid=0);

SET @geo_root = (SELECT id FROM v9_menu WHERE m='geoopt' AND c='admin_geoopt' AND a='init' AND parentid=0 LIMIT 1);

-- 二级分组「优化管理」（a='' 空动作作分组，不存在才插入）
INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'menu_geoopt_group',@geo_root,'geoopt','admin_geoopt','','',1,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='' AND parentid=@geo_root);

SET @geo_group = (SELECT id FROM v9_menu WHERE m='geoopt' AND a='' AND parentid=@geo_root LIMIT 1);

-- 三级功能子菜单（挂在优化管理分组下，不存在才插入）
INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_index',@geo_group,'geoopt','admin_geoopt','init','',1,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='init' AND parentid=@geo_group);

INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_list',@geo_group,'geoopt','admin_geoopt','list','',2,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='list' AND parentid=@geo_group);

INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_faq',@geo_group,'geoopt','admin_geoopt','faq','',3,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='faq' AND parentid=@geo_group);

INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_config',@geo_group,'geoopt','admin_geoopt','config','',4,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='config' AND parentid=@geo_group);
