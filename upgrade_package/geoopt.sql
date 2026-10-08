-- ============================================================
-- GEO（生成式引擎优化）模块 安装 SQL
-- 1. 建 FAQ 表 v9_geo_faq
-- 2. 后台独立顶级菜单「GEO优化」+ 4 个子菜单
-- 幂等：表用 CREATE IF NOT EXISTS；菜单用 INSERT IGNORE（无唯一键则先查再插）
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

-- 顶级菜单「GEO优化」（不存在才插入）
INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'menu_geoopt',0,'geoopt','admin_geoopt','init','',99,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND c='admin_geoopt' AND parentid=0);

SET @geo_root = (SELECT id FROM v9_menu WHERE m='geoopt' AND c='admin_geoopt' AND a='init' AND parentid=0 LIMIT 1);

-- 子菜单（不存在才插入）
INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_index',@geo_root,'geoopt','admin_geoopt','init','',1,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='init' AND parentid=@geo_root);

INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_list',@geo_root,'geoopt','admin_geoopt','list','',2,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='list' AND parentid=@geo_root);

INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_faq',@geo_root,'geoopt','admin_geoopt','faq','',3,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='faq' AND parentid=@geo_root);

INSERT INTO v9_menu (name,parentid,m,c,a,data,listorder,display)
SELECT 'geoopt_config',@geo_root,'geoopt','admin_geoopt','config','',4,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM v9_menu WHERE m='geoopt' AND a='config' AND parentid=@geo_root);
