<?php

/**
 *  数据升级-20210614
 */

define('PHPCMS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
include PHPCMS_PATH . '/phpcms/base.php';

$tables = array('category' => 'catid', 'module' => 'module', 'model_field' => 'fieldid', 'site' => 'siteid');

$results = array();
$db = pc_base::load_model('sitemodel_field_model');
foreach ($tables as $table => $pk) {
	$db->change_table($table);
	$datas = $db->select();
	foreach ($datas as $r) {
		$setting = array2string(old_string2array($r['setting']));
		if ($db->update(array('setting' => $setting), array($pk => $r[$pk]))) {
			$results[$table]++;
		}
	}
}
@unlink(__FILE__);
echo '<pre>
升级完成！
请删除此文件，并在管理后台更新全站缓存。
</pre>';

function old_string2array($data)
{
	$data = trim($data);
	if ($data == '') return array();
	if (strpos($data, 'array') === 0) {
		@eval("\$array = $data;");
	} else {
		if (strpos($data, '{\\') === 0) $data = stripslashes($data);
		$array = json_decode($data, true);
		if (strtolower(CHARSET) == 'gbk') {
			$array = mult_iconv("UTF-8", "GBK//IGNORE", $array);
		}
	}
	return $array;
}
