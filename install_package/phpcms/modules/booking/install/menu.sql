-- 确保'扩展模块'分组存在
INSERT INTO `v9_menu` (`name`,`parentid`,`m`,`c`,`a`,`data`,`listorder`,`display`)
SELECT 'module_extend',2,'admin','module','','',0,1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `v9_menu` WHERE `name`='module_extend' AND `parentid`=2);
SET @extend_pid = (SELECT `id` FROM `v9_menu` WHERE `name`='module_extend' AND `parentid`=2 LIMIT 1);
-- 新模块主菜单
INSERT INTO `v9_menu` (`name`,`parentid`,`m`,`c`,`a`,`data`,`listorder`,`display`) VALUES ('menu_booking', @extend_pid, 'booking', 'admin_booking', 'init', '', 0, 1);
SET @booking_pid = LAST_INSERT_ID();
INSERT INTO `v9_menu` (`name`,`parentid`,`m`,`c`,`a`,`data`,`listorder`,`display`) VALUES ('menu_booking_add', @booking_pid, 'booking', 'admin_booking', 'add', '', 0, 0);
INSERT INTO `v9_menu` (`name`,`parentid`,`m`,`c`,`a`,`data`,`listorder`,`display`) VALUES ('menu_booking_edit', @booking_pid, 'booking', 'admin_booking', 'edit', '', 0, 0);
INSERT INTO `v9_menu` (`name`,`parentid`,`m`,`c`,`a`,`data`,`listorder`,`display`) VALUES ('menu_booking_delete', @booking_pid, 'booking', 'admin_booking', 'delete', '', 0, 0);
