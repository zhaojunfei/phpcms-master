-- ============================================================
-- phpcms api 模块注册（iscore=1 核心模块）
-- 背景：api 是纯接口层模块（modules/api 下只有 article/category/index 三个接口文件，
--       无 install 目录、不走安装/卸载机制），默认不在 v9_module 注册表里，
--       导致后台「模块 → 模块管理」将 api 显示为"未知/无法安装"。
-- 本脚本把 api 补进 v9_module（iscore=1），使后台正常显示为已安装的核心模块。
-- 前台 ?m=api&c=... 本身即可用，无需额外操作。
-- 幂等：可重复执行；已存在则更新字段。
-- 执行后请在后台「扩展 → 更新缓存」刷新模块缓存。
-- 用法（本 Docker 环境）：
--   docker exec -i phpcms-db mysql -uphpcms -p'phpcms123' phpcms --default-character-set=utf8mb4 < api_module.sql
-- ============================================================

INSERT INTO v9_module (module, name, url, iscore, version, description, setting, listorder, disabled, installdate, updatedate)
VALUES ('api', 'API接口', 'api/', 1, '1.0', 'API接口模块', '', 0, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  name = 'API接口',
  iscore = 1,
  version = '1.0',
  description = 'API接口模块',
  disabled = 0;
