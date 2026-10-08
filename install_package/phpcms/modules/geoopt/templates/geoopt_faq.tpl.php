<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<div class="explain-col search-form">
<form name="searchform" method="get" action="">
<input type="hidden" name="m" value="geoopt">
<input type="hidden" name="c" value="admin_geoopt">
<input type="hidden" name="a" value="faq">
问答关键词: <input type="text" name="keyword" value="<?php echo isset($_GET['keyword'])?htmlspecialchars($_GET['keyword']):''; ?>" size="18">&nbsp;&nbsp;内容ID: <input type="text" name="contentid" value="<?php echo isset($_GET['contentid'])?intval($_GET['contentid']):''; ?>" size="6">&nbsp;&nbsp;<input type="submit" value="搜索" class="button">
<a href="?m=geoopt&c=admin_geoopt&a=faq" class="button">重置</a>
</form>
</div>
<div class="table-list">
<form name="myform" action="?m=geoopt&c=admin_geoopt&a=faq_delete&pc_hash=<?php echo $_GET['pc_hash'];?>" method="post">
<input type="hidden" name="pc_hash" value="<?php echo $_GET['pc_hash'];?>">
<table width="100%" cellspacing="0">
<thead><tr><th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th><th align="center">内容ID</th><th align="center">问题</th><th align="center">回答(截断)</th><th width="60" align="center">排序</th><th width="130" align="center">操作</th></tr></thead>
<tbody>
<?php if (is_array($list) && $list) { foreach($list as $v) { ?>
<tr>
<td align="center"><input type="checkbox" name="id[]" value="<?php echo $v['id']; ?>"></td>
<td align="center"><?php echo $v['contentid']; ?></td>
<td align="center"><?php echo $v['question']; ?></td>
<td align="center"><?php echo mb_substr(strip_tags($v['answer']),0,45,'utf-8'); ?></td>
<td align="center"><?php echo $v['listorder']; ?></td>
<td align="center"><a href="?m=geoopt&c=admin_geoopt&a=faq_edit&id=<?php echo $v['id']; ?>&pc_hash=<?php echo $_GET['pc_hash'];?>">修改</a></td>
</tr>
<?php } } else { ?><tr><td colspan="6" align="center" style="padding:20px;color:#999">暂无 FAQ 问答，点击下方"添加FAQ"创建</td></tr><?php } ?>
</tbody>
</table>
<div class="btn"><label for="check_box">全选/取消</label>&nbsp;&nbsp;
<input type="submit" class="button" value="删除所选" onclick="return confirm('确认删除选中记录?')">
&nbsp;&nbsp;<a href="?m=geoopt&c=admin_geoopt&a=faq_add&pc_hash=<?php echo $_GET['pc_hash'];?>" class="button">添加FAQ</a>
</div>
</form>
</div>
<div id="pages"><?php echo $pages; ?></div>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
