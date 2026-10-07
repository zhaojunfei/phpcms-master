<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_func('global');
$db = pc_base::load_model('booking_model');
$list = $db->select('1=1', '*', '20', 'id DESC');
?><!DOCTYPE html><html><head><meta charset="utf-8"><title>预约管理</title></head><body>
<h1>预约管理</h1>
<?php if (is_array($list) && $list) { foreach($list as $v) { ?>
<div style="border-bottom:1px solid #eee;padding:8px"><?php echo htmlspecialchars($v['name']); ?> <span style="color:#999"><?php echo $v['inputtime']?date('Y-m-d',$v['inputtime']):''; ?></span></div>
<?php } } else { echo '暂无数据'; } ?>
</body></html>
