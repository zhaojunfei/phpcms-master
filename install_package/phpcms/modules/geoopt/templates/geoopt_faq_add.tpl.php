<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="myform" action="?m=geoopt&c=admin_geoopt&a=<?php echo $data?'faq_edit&id='.$data['id']:'faq_add'; ?>&pc_hash=<?php echo $_GET['pc_hash'];?>" method="post">
<input type="hidden" name="pc_hash" value="<?php echo $_GET['pc_hash'];?>">
<div class="table-list">
<table width="100%" cellspacing="0">
<tr><th width="140" align="right">关联内容ID</th><td><input type="text" name="info[contentid]" value="<?php echo isset($data)?$data['contentid']:intval($_GET['contentid']); ?>" size="8"> <span style="color:#888">填 phpcms 内容 id（文章/图集/视频等）</span></td></tr>
<tr><th align="right">问题</th><td><input type="text" name="info[question]" value="<?php echo isset($data)?htmlspecialchars($data['question']):''; ?>" size="60"> <span style="color:#888">用户可能向大模型提出的问题</span></td></tr>
<tr><th align="right">回答</th><td><textarea name="info[answer]" cols="70" rows="6"><?php echo isset($data)?htmlspecialchars($data['answer']):''; ?></textarea> <span style="color:#888">权威、清晰的回答，便于被 AI 引用</span></td></tr>
<tr><th align="right">排序</th><td><input type="text" name="info[listorder]" value="<?php echo isset($data)?$data['listorder']:'0'; ?>" size="6"></td></tr>
</table>
</div>
<div class="btn"><input type="submit" class="button" value="保存"></div>
</form>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
