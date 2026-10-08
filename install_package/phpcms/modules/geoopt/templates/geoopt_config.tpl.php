<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="myform" action="?m=geoopt&c=admin_geoopt&a=config&pc_hash=<?php echo $_GET['pc_hash'];?>" method="post">
<input type="hidden" name="pc_hash" value="<?php echo $_GET['pc_hash'];?>">
<div class="table-list">
<table width="100%" cellspacing="0">
<tr><th width="140" align="right">站点名称</th><td><input type="text" name="site_name" value="<?php echo htmlspecialchars($conf['site_name']); ?>" size="40"> <span style="color:#888">用于 Schema 的 publisher.name</span></td></tr>
<tr><th align="right">站点描述</th><td><textarea name="site_desc" cols="60" rows="3"><?php echo htmlspecialchars($conf['site_desc']); ?></textarea> <span style="color:#888">站点简介，供大模型理解</span></td></tr>
<tr><th align="right">输出结构化 Schema</th><td><label><input type="checkbox" name="schema_enable" value="1" <?php echo $conf['schema_enable']?'checked':''; ?>> 在内容详情页输出 JSON-LD(NewsArticle + FAQPage) 结构化数据</label></td></tr>
<tr><th align="right">启用 FAQ 区块</th><td><label><input type="checkbox" name="faq_enable" value="1" <?php echo $conf['faq_enable']?'checked':''; ?>> 在内容详情页显示 FAQ 问答区块</label></td></tr>
</table>
</div>
<div class="btn"><input type="submit" class="button" value="保存配置"></div>
</form>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
