<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<style>
.geo-score{width:100px;height:8px;background:#eef2f6;border-radius:6px;overflow:hidden;display:inline-block;vertical-align:middle;margin-right:6px;}
.geo-score i{display:block;height:100%;background:linear-gradient(90deg,#f79009,#37c5a8);border-radius:6px;}
.score-low{color:#e5484d;font-weight:600;}
.score-mid{color:#f79009;font-weight:600;}
.score-high{color:#12b76a;font-weight:600;}
</style>
<div class="pad-lr-10">
<div class="explain-col search-form">
<form name="searchform" method="get" action="">
<input type="hidden" name="m" value="geoopt">
<input type="hidden" name="c" value="admin_geoopt">
<input type="hidden" name="a" value="list">
内容关键词: <input type="text" name="keyword" value="<?php echo isset($_GET['keyword'])?htmlspecialchars($_GET['keyword']):''; ?>" size="20">&nbsp;&nbsp;<input type="submit" value="搜索" class="button">
<a href="?m=geoopt&c=admin_geoopt&a=list" class="button">重置</a>
</form>
</div>
<div class="table-list">
<table width="100%" cellspacing="0">
<thead><tr>
<th align="center">内容标题</th><th align="center">模型</th><th align="center">摘要</th><th align="center">关键词</th><th align="center">FAQ</th><th align="center">完成度</th><th width="100" align="center">操作</th>
</tr></thead>
<tbody>
<?php if ($page_rows) { foreach($page_rows as $v) {
  $cls = $v['score']<40?'score-low':($v['score']<80?'score-mid':'score-high');
  $summ = $v['description'] ? mb_substr($v['description'],0,40,'utf-8') : '<span style="color:#e5484d">未设摘要</span>';
  $kws = $v['keywords'] ? $v['keywords'] : '<span style="color:#e5484d">未设关键词</span>';
  $faq = $v['faq_count']>0 ? $v['faq_count'].' 条' : '<a href="?m=geoopt&c=admin_geoopt&a=faq_add&contentid='.$v['id'].'&pc_hash='.$_GET['pc_hash'].'" style="color:#e5484d">添加FAQ</a>';
  $faq_link = $v['faq_count']>0 ? '?m=geoopt&c=admin_geoopt&a=faq&contentid='.$v['id'].'&pc_hash='.$_GET['pc_hash'] : $faq;
?>
<tr>
  <td align="center"><?php echo $v['title']; ?></td>
  <td align="center"><?php echo $v['model']; ?></td>
  <td align="center"><?php echo $summ; ?></td>
  <td align="center"><?php echo $kws; ?></td>
  <td align="center"><?php echo $faq_link; ?></td>
  <td align="center"><span class="<?php echo $cls; ?>"><?php echo $v['score']; ?></span> <span class="geo-score"><i style="width:<?php echo $v['score']; ?>%;"></i></span></td>
  <td align="center"><a href="?m=geoopt&c=admin_geoopt&a=faq_add&contentid=<?php echo $v['id']; ?>&pc_hash=<?php echo $_GET['pc_hash'];?>">配FAQ</a></td>
</tr>
<?php } } else { ?>
<tr><td colspan="7" align="center" style="padding:20px;color:#999">暂无内容，可先在「内容管理」中添加文章</td></tr>
<?php } ?>
</tbody>
</table>
</div>
<div id="pages"><?php echo $pages; ?></div>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
