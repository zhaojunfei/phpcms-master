<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<div class="explain-col search-form">
<form name="searchform" method="get" action="">
<input type="hidden" name="m" value="booking">
<input type="hidden" name="c" value="admin_booking">
<input type="hidden" name="a" value="init">
姓名: <input type="text" name="name" value="<?php echo isset($_GET['name'])?htmlspecialchars($_GET['name']):''; ?>" size="12">&nbsp;&nbsp;手机号: <input type="text" name="mobile" value="<?php echo isset($_GET['mobile'])?htmlspecialchars($_GET['mobile']):''; ?>" size="12">&nbsp;&nbsp;&nbsp;<input type="submit" value="搜索" class="button">
<a href="?m=booking&c=admin_booking&a=init" class="button">重置</a>
</form>
</div>
<div class="table-list">
<form name="myform" action="?m=booking&c=admin_booking&a=delete&pc_hash=<?php echo $_GET['pc_hash'];?>" method="post">
<input type="hidden" name="pc_hash" value="<?php echo $_GET['pc_hash'];?>">
<table width="100%" cellspacing="0">
<thead><tr><th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th><th align="center">姓名</th><th align="center">手机号</th><th align="center">预约时间</th><th width="120" align="center">操作</th></tr></thead>
<tbody>
<?php if (is_array($list) && $list) { foreach($list as $v) { ?>
<tr><td align="center"><input type="checkbox" name="id[]" value="<?php echo $v['id']; ?>"></td><td align="center"><?php echo $v['name']; ?></td><td align="center"><?php echo $v['mobile']; ?></td><td align="center"><?php echo $v['booking_time']; ?></td><td align="center"><a href="?m=booking&c=admin_booking&a=edit&id=<?php echo $v['id']; ?>">修改</a></td></tr>
<?php } } else { ?><tr><td colspan="5" align="center" style="padding:20px;color:#999">暂无数据</td></tr><?php } ?>
</tbody>
</table>
<div class="btn"><label for="check_box">全选/取消</label>&nbsp;&nbsp;
<input type="submit" class="button" value="删除所选" onclick="return confirm('确认删除选中记录?')">
&nbsp;&nbsp;<a href="?m=booking&c=admin_booking&a=add" class="button">添加</a>
</div>
</form>
</div>
<div id="pages"><?php echo $pages; ?></div>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
