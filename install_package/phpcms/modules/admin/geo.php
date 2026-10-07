<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
/**
 * 后台"GEO搜索"设置/说明页：展示 GEO 用法与各内容表 GEO 数据状态
 */
class geo extends admin {

	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');
	}

	public function init() {
		$show_header = true;
		// 高德地图 Key / 安全密钥 配置
		$amap_key = '';
		$security_js_code = '';
		if(file_exists(CACHE_PATH.'configs/geo.php')) {
			$gc = include CACHE_PATH.'configs/geo.php';
			if(is_array($gc)) {
				if(isset($gc['amap_key'])) $amap_key = $gc['amap_key'];
				if(isset($gc['security_js_code'])) $security_js_code = $gc['security_js_code'];
			}
		}
		pc_base::load_sys_class('db_factory','',0);
		$db = db_factory::get_instance()->get_database('default');
		$tablepre = 'v9_';
		$cfg = pc_base::load_config('database');
		if(isset($cfg['default']['tablepre'])) $tablepre = $cfg['default']['tablepre'];
		$tables = array('news','download','picture');
		$stat = array();
		$list = array();
		foreach($tables as $t) {
			$name = $tablepre.$t;
			$db->query("SELECT COUNT(*) AS total FROM `$name`");
			$r = $db->fetch_next();
			$db->query("SELECT COUNT(*) AS c FROM `$name` WHERE (lat<>0 OR lng<>0 OR area_id<>0)");
			$g = $db->fetch_next();
			$stat[$t] = array(
				'table' => $name,
				'total' => $r ? $r['total'] : 0,
				'has_geo' => $g ? $g['c'] : 0,
			);
			// 列出已有 GEO 数据的内容
			$db->query("SELECT c.id, c.title, c.catid, c.area_id, c.lng, c.lat FROM `$name` c WHERE (c.lat<>0 OR c.lng<>0 OR c.area_id<>0)");
			while($row = $db->fetch_next()) {
				$row['tbl'] = $t;
				$list[] = $row;
			}
		}
		// 栏目名
		foreach($list as &$v) {
			$db->query("SELECT catname FROM `".$tablepre."category` WHERE catid=".intval($v['catid'])." LIMIT 1");
			$c = $db->fetch_next();
			$v['catname'] = $c ? $c['catname'] : '-';
		}
		include $this->admin_tpl('geo');
	}

	/**
	 * 保存高德地图 Key 配置
	 */
	public function save() {
		if(!isset($_POST['dosubmit'])) showmessage(L('operation_failure'));
		$amap_key = isset($_POST['amap_key']) ? trim($_POST['amap_key']) : '';
		$security_js_code = isset($_POST['security_js_code']) ? trim($_POST['security_js_code']) : '';
		$data = "<?php\nreturn array(\n\t'amap_key' => '".addslashes($amap_key)."',\n\t'security_js_code' => '".addslashes($security_js_code)."',\n);\n";
		@file_put_contents(CACHE_PATH.'configs/geo.php', $data);
		showmessage('保存成功', '?m=admin&c=geo&a=init&pc_hash='.$_SESSION['pc_hash'], '', 1);
	}
}
