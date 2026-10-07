<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
function showDriver(){
	var d = document.getElementById('driver').value;
	var rows = document.querySelectorAll('[data-dgroup]');
	for(var i=0;i<rows.length;i++){
		var g = rows[i].getAttribute('data-dgroup');
		// S3 兼容配置块在 "亚马逊 S3" 或 "自定义" 时显示
		var show = (g==d) || (g=='s3compat' && (d=='s3'||d=='__custom'));
		rows[i].style.display = show ? '' : 'none';
	}
}
$(function(){ showDriver(); });
//-->
</script>
<form action="?m=admin&c=storage&a=save" method="post" id="myform">
<div class="pad-10">
<div class="content-menu ib-a blue line-x"><em>存储设置</em></div>
<p class="tips" style="margin:8px 0;color:#666">选择存储驱动后填写对应配置，保存后立即生效（默认磁盘，无需额外配置）。非磁盘驱动下，图片访问 URL 会自动带时效签名。</p>
<table width="100%" class="table_form">
  <tr>
    <th width="160">存储驱动</th>
    <td class="y-bg">
      <select name="storage[driver]" id="driver" onchange="showDriver()">
        <option value="disk" <?php if($driver=='disk') echo 'selected';?>>磁盘（本地服务器）</option>
        <option value="minio" <?php if($driver=='minio') echo 'selected';?>>MinIO</option>
        <option value="oss" <?php if($driver=='oss') echo 'selected';?>>阿里云 OSS</option>
        <option value="cos" <?php if($driver=='cos') echo 'selected';?>>腾讯云 COS</option>
        <option value="qiniu" <?php if($driver=='qiniu') echo 'selected';?>>七牛云存储</option>
        <option value="s3" <?php if($driver=='s3') echo 'selected';?>>亚马逊云 S3</option>
        <option value="__custom" <?php if(!in_array($driver, array('disk','minio','oss','cos','qiniu','s3'))) echo 'selected';?>>自定义（S3兼容）</option>
      </select>
    </td>
  </tr>

  <!-- ===== 磁盘 ===== -->
  <tr data-dgroup="disk">
    <th>上传目录路径</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[disk][upload_path]" value="<?php echo htmlspecialchars($storage['disk']['upload_path']);?>" size="50" placeholder="如 uploadfile/"/></td>
  </tr>
  <tr data-dgroup="disk">
    <th>上传访问URL</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[disk][upload_url]" value="<?php echo htmlspecialchars($storage['disk']['upload_url']);?>" size="50" placeholder="如 http://你的域名/uploadfile/"/></td>
  </tr>

  <!-- ===== MinIO ===== -->
  <tr data-dgroup="minio">
    <th>Endpoint 服务地址</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[minio][endpoint]" value="<?php echo htmlspecialchars($storage['minio']['endpoint']);?>" size="50" placeholder="如 http://127.0.0.1:9000"/></td>
  </tr>
  <tr data-dgroup="minio">
    <th>Access Key</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[minio][access_key]" value="<?php echo htmlspecialchars($storage['minio']['access_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="minio">
    <th>Secret Key</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[minio][secret_key]" value="<?php echo htmlspecialchars($storage['minio']['secret_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="minio">
    <th>Bucket 桶名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[minio][bucket]" value="<?php echo htmlspecialchars($storage['minio']['bucket']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="minio">
    <th>Region 区域</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[minio][region]" value="<?php echo htmlspecialchars($storage['minio']['region']);?>" size="50" placeholder="如 us-east-1"/></td>
  </tr>
  <tr data-dgroup="minio">
    <th>启用 HTTPS</th>
    <td class="y-bg"><input type="checkbox" name="storage[minio][secure]" value="1" <?php if($storage['minio']['secure']) echo 'checked';?>/> 勾选则使用 https</td>
  </tr>
  <tr data-dgroup="minio">
    <th>CDN/访问域名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[minio][cdn_domain]" value="<?php echo htmlspecialchars($storage['minio']['cdn_domain']);?>" size="50" placeholder="如 https://cdn.example.com（配置后图片用此域名返回持久直链）"/></td>
  </tr>

  <!-- ===== 阿里云 OSS ===== -->
  <tr data-dgroup="oss">
    <th>Endpoint 节点</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[oss][endpoint]" value="<?php echo htmlspecialchars($storage['oss']['endpoint']);?>" size="50" placeholder="如 oss-cn-hangzhou.aliyuncs.com"/></td>
  </tr>
  <tr data-dgroup="oss">
    <th>AccessKey ID</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[oss][access_id]" value="<?php echo htmlspecialchars($storage['oss']['access_id']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="oss">
    <th>AccessKey Secret</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[oss][access_secret]" value="<?php echo htmlspecialchars($storage['oss']['access_secret']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="oss">
    <th>Bucket 桶名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[oss][bucket]" value="<?php echo htmlspecialchars($storage['oss']['bucket']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="oss">
    <th>启用 HTTPS</th>
    <td class="y-bg"><input type="checkbox" name="storage[oss][is_https]" value="1" <?php if($storage['oss']['is_https']) echo 'checked';?>/> 勾选则使用 https</td>
  </tr>
  <tr data-dgroup="oss">
    <th>CDN/访问域名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[oss][cdn_domain]" value="<?php echo htmlspecialchars($storage['oss']['cdn_domain']);?>" size="50" placeholder="如 https://cdn.example.com（配置后图片用此域名返回持久直链）"/></td>
  </tr>

  <!-- ===== 腾讯云 COS ===== -->
  <tr data-dgroup="cos">
    <th>Bucket 桶名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[cos][bucket]" value="<?php echo htmlspecialchars($storage['cos']['bucket']);?>" size="50" placeholder="如 example-1250000000"/></td>
  </tr>
  <tr data-dgroup="cos">
    <th>Region 区域</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[cos][region]" value="<?php echo htmlspecialchars($storage['cos']['region']);?>" size="50" placeholder="如 ap-guangzhou"/></td>
  </tr>
  <tr data-dgroup="cos">
    <th>SecretId</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[cos][secret_id]" value="<?php echo htmlspecialchars($storage['cos']['secret_id']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="cos">
    <th>SecretKey</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[cos][secret_key]" value="<?php echo htmlspecialchars($storage['cos']['secret_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="cos">
    <th>启用 HTTPS</th>
    <td class="y-bg"><input type="checkbox" name="storage[cos][is_https]" value="1" <?php if($storage['cos']['is_https']) echo 'checked';?>/> 勾选则使用 https</td>
  </tr>
  <tr data-dgroup="cos">
    <th>CDN/访问域名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[cos][cdn_domain]" value="<?php echo htmlspecialchars($storage['cos']['cdn_domain']);?>" size="50" placeholder="如 https://cdn.example.com（配置后图片用此域名返回持久直链）"/></td>
  </tr>

  <!-- ===== 七牛 ===== -->
  <tr data-dgroup="qiniu">
    <th>Access Key</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[qiniu][access_key]" value="<?php echo htmlspecialchars($storage['qiniu']['access_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="qiniu">
    <th>Secret Key</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[qiniu][secret_key]" value="<?php echo htmlspecialchars($storage['qiniu']['secret_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="qiniu">
    <th>Bucket 空间名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[qiniu][bucket]" value="<?php echo htmlspecialchars($storage['qiniu']['bucket']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="qiniu">
    <th>CDN 域名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[qiniu][domain]" value="<?php echo htmlspecialchars($storage['qiniu']['domain']);?>" size="50" placeholder="如 你的七牛绑定域名"/></td>
  </tr>
  <tr data-dgroup="qiniu">
    <th>上传域名（可选）</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[qiniu][upload_host]" value="<?php echo htmlspecialchars($storage['qiniu']['upload_host']);?>" size="50" placeholder="如 up-z2.qiniup.com"/></td>
  </tr>
  <tr data-dgroup="qiniu">
    <th>启用 HTTPS</th>
    <td class="y-bg"><input type="checkbox" name="storage[qiniu][is_https]" value="1" <?php if($storage['qiniu']['is_https']) echo 'checked';?>/> 勾选则使用 https</td>
  </tr>

  <!-- ===== 自定义驱动名（选“自定义”时显示） ===== -->
  <tr data-dgroup="__custom">
    <th>自定义驱动名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[custom_name]" value="<?php echo htmlspecialchars($storage['custom_name']);?>" size="50" placeholder="如 backblaze，仅字母数字下划线"/></td>
  </tr>

  <!-- ===== 亚马逊 S3 / 自定义（S3 兼容，共用配置组） ===== -->
  <tr data-dgroup="s3compat">
    <th>Endpoint 服务地址（可选）</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[s3compat][endpoint]" value="<?php echo htmlspecialchars($s3compat['endpoint']);?>" size="50" placeholder="AWS 默认 s3.<region>.amazonaws.com，自定义可填完整地址"/></td>
  </tr>
  <tr data-dgroup="s3compat">
    <th>Access Key</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[s3compat][access_key]" value="<?php echo htmlspecialchars($s3compat['access_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="s3compat">
    <th>Secret Key</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[s3compat][secret_key]" value="<?php echo htmlspecialchars($s3compat['secret_key']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="s3compat">
    <th>Bucket 桶名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[s3compat][bucket]" value="<?php echo htmlspecialchars($s3compat['bucket']);?>" size="50"/></td>
  </tr>
  <tr data-dgroup="s3compat">
    <th>Region 区域</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[s3compat][region]" value="<?php echo htmlspecialchars($s3compat['region']);?>" size="50" placeholder="如 us-east-1"/></td>
  </tr>
  <tr data-dgroup="s3compat">
    <th>启用 HTTPS</th>
    <td class="y-bg"><input type="checkbox" name="storage[s3compat][secure]" value="1" <?php if($s3compat['secure']) echo 'checked';?>/> 勾选则使用 https</td>
  </tr>
  <tr data-dgroup="s3compat">
    <th>CDN/访问域名</th>
    <td class="y-bg"><input type="text" class="input-text" name="storage[s3compat][cdn_domain]" value="<?php echo htmlspecialchars($s3compat['cdn_domain']);?>" size="50" placeholder="如 https://cdn.example.com（配置后图片用此域名返回持久直链，无需填 bucket）"/></td>
  </tr>
</table>
<div class="bk15"></div>
<input type="submit" class="button" value="保存" />
</div>
</form>
<?php include $this->admin_tpl('footer');?>
