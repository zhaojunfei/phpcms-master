<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="content-menu ib-a blue line-x"><em>预约管理</em></div>
<form action="?m=booking&c=admin_booking&a=<?php echo isset($action)?$action:'add'; ?>" method="post" id="myform">
<table width="100%" class="table_form">
  <tr><th>姓名 <span style="color:red">*</span></th><td class="y-bg"><input type="text" name="info[name]" size="40" value="<?php echo isset($data['name'])?$data['name']:''; ?>"/></td></tr>
  <tr><th>手机号 <span style="color:red">*</span></th><td class="y-bg"><input type="text" name="info[mobile]" size="40" value="<?php echo isset($data['mobile'])?$data['mobile']:''; ?>"/></td></tr>
  <tr><th>预约时间 <span style="color:red">*</span></th><td class="y-bg"><input type="text" name="info[booking_time]" size="40" value="<?php echo isset($data['booking_time'])?$data['booking_time']:''; ?>"/></td></tr>
  <tr><th>备注</th><td class="y-bg"><textarea name="info[remark]" style="width:500px;height:100px"><?php echo isset($data['remark'])?$data['remark']:''; ?></textarea></td></tr>
</table>
<div class="bk15"></div>
<input type="submit" name="dosubmit" value="保存" class="button"/>
</form>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
