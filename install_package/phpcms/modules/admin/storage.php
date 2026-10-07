<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
/**
 * 后台"存储设置"：可视化配置文件存储驱动（磁盘/MinIO/OSS/COS/七牛）
 * 配置保存到 caches/configs/storage.php，storage_factory 优先读取
 */
class storage extends admin {

	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');
	}

	/**
	 * 存储配置页
	 */
	public function init() {
		$show_header = true;
		$show_validator = true;
		$config_file = CACHE_PATH.'configs/storage.php';
		$storage = array();
		if (file_exists($config_file)) {
			$storage = include $config_file;
		}
		if (!is_array($storage) || empty($storage['driver'])) {
			$storage = pc_base::load_config('system','storage');
		}
		$driver = isset($storage['driver']) ? $storage['driver'] : 'disk';
		$default = array('disk'=>array(),'minio'=>array(),'oss'=>array(),'cos'=>array(),'qiniu'=>array(),'s3'=>array());
		foreach ($default as $d=>$v) {
			if (!isset($storage[$d]) || !is_array($storage[$d])) $storage[$d] = array();
		}
		// 计算 S3 兼容配置组（亚马逊 S3 或自定义驱动共用，供设置页表单回显）
		$s3compat = array();
		if (is_array($storage['s3'])) $s3compat = $storage['s3'];
		elseif ($driver != 'disk' && !in_array($driver, array('minio','oss','cos','qiniu')) && isset($storage[$driver]) && is_array($storage[$driver])) $s3compat = $storage[$driver];
		$storage['custom_name'] = isset($storage['custom_name']) ? $storage['custom_name'] : '';
		include $this->admin_tpl('storage');
	}

	/**
	 * 保存存储配置
	 */
	public function save() {
		if (!isset($_POST['storage']) || !is_array($_POST['storage'])) {
			showmessage('参数错误', HTTP_REFERER);
		}
		$allow = array('disk','minio','oss','cos','qiniu');
		$storage = $_POST['storage'];
		$driver = isset($storage['driver']) ? trim($storage['driver']) : 'disk';
		// 自定义驱动：driver 下拉值为 __custom 时，取用户填的自定义驱动名
		if ($driver == '__custom') {
			$driver = preg_replace('/[^a-z0-9_]/', '', isset($storage['custom_name']) ? $storage['custom_name'] : '');
			if (empty($driver)) $driver = 'custom';
		}
		$storage['driver'] = $driver;
		// S3 或自定义（S3 兼容）：将 s3compat 配置组归到对应驱动名下
		if (!in_array($driver, array('disk', 'minio', 'oss', 'cos', 'qiniu'))) {
			$storage[$driver] = isset($storage['s3compat']) && is_array($storage['s3compat']) ? $storage['s3compat'] : array();
		}
		unset($storage['s3compat'], $storage['custom_name']);

		// 递归去空格，仅保留字符串/空值
		array_walk_recursive($storage, function(&$v){ if (is_string($v)) $v = trim($v); });

		$str = "<?php\n/**\n * 存储配置（由后台“存储设置”保存，请勿手动编辑）\n * driver: disk | minio | oss | cos | qiniu | s3 | 自定义\n */\nreturn ".var_export($storage, true).";\n";
		$config_file = CACHE_PATH.'configs/storage.php';
		@file_put_contents($config_file, $str);
		showmessage('存储配置保存成功', HTTP_REFERER);
	}
}
