<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="content-menu ib-a blue line-x"><em>示例模块</em></div>
<form action="?m=demo&c=admin_demo&a=<?php echo isset($action)?$action:'add'; ?>" method="post" id="myform">
<table width="100%" class="table_form">
  <tr><th>标题 <span style="color:red">*</span></th><td class="y-bg"><input type="text" name="info[title]" size="40" value="<?php echo isset($data['title'])?$data['title']:''; ?>"/></td></tr>
  <tr><th>内容</th><td class="y-bg"><textarea name="info[content]" style="width:500px;height:100px"><?php echo isset($data['content'])?$data['content']:''; ?></textarea></td></tr>
  <tr><th>录入时间</th><td class="y-bg"><input type="text" name="info[inputtime]" size="40" value="<?php echo isset($data['inputtime'])?$data['inputtime']:''; ?>"/></td></tr>
</table>
<div class="bk15"></div>
<input type="submit" name="dosubmit" value="保存" class="button"/>
</form>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
