<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad-10">
<div class="content-menu ib-a blue line-x"><em>GEO 搜索</em></div>
<p class="tips" style="margin:10px 0;color:#666">在内容中记录地区编码 / 经纬度，即可在后台按地区或周边坐标检索内容。</p>

<form action="?m=admin&c=geo&a=save" method="post" id="geofrm">
<table width="100%" class="table_form">
  <tr>
    <th width="160">高德地图 Key</th>
    <td class="y-bg">
      <input type="text" name="amap_key" id="amap_key" value="<?php echo $amap_key;?>" style="width:420px" placeholder="填写高德开放平台 JS API 的 Key"/>　
      <input type="submit" name="dosubmit" class="button" value="保存"/>
      <div style="margin-top:6px;color:#999">用于「地图选点」：在内容添加/编辑时打开地图点击选点，自动回填经纬度。申请地址：<a href="https://console.amap.com" target="_blank">高德开放平台 console.amap.com</a>（免费，创建 Web端JS API 应用即可获得 Key）。</div>
    </td>
  </tr>
  <tr>
    <th width="160">安全密钥 securityJsCode</th>
    <td class="y-bg">
      <input type="text" name="security_js_code" id="security_js_code" value="<?php echo $security_js_code;?>" style="width:420px" placeholder="高德 JS API 应用详情里的「安全密钥」"/>　
      <input type="submit" name="dosubmit" class="button" value="保存"/>
      <div style="margin-top:6px;color:#999">高德自 2021 年起 Web 端 JS API 强制要求安全密钥，否则地图/逆地理编码报 error。获取：控制台 → 你的 JS API 应用 → 「添加安全密钥」，复制后填此；同时建议把服务器域名（<code>localhost:8080</code>）加入该 key 的「白名单」。</div>
    </td>
  </tr>
</table>
</form>

<table width="100%" class="table_form">
  <tr>
    <th width="160">使用方式</th>
    <td class="y-bg">
      ① 在「内容 → 管理内容 → 文章栏目 → 添加/编辑内容」中，填写表单底部的 <b>GEO地区编码 / 经度 / 纬度</b>；<br/>
      ② 在「内容 → 管理内容 → 栏目列表 → 右上角点"搜索"」，展开的 <b>GEO搜索</b> 区可填：<br/>
      &nbsp;&nbsp;&nbsp;- <b>地区编码</b>：精确筛选某地区的内容；<br/>
      &nbsp;&nbsp;&nbsp;- <b>经度 + 纬度 + 半径(km)</b>：以该点为中心做周边距离检索。
    </td>
  </tr>
  <tr>
    <th>地区编码说明</th>
    <td class="y-bg">为数字编码（如 110100=北京、310100=上海），与内容表的 <code>area_id</code> 字段对应，录入时保持一致即可。</td>
  </tr>
  <tr>
    <th>各内容表 GEO 数据状态</th>
    <td class="y-bg">
      <table width="60%" class="table_form">
        <tr><th>内容表</th><th>总内容</th><th>已设GEO(地区/坐标)</th></tr>
        <?php foreach($stat as $t=>$s) { ?>
        <tr><td><?php echo $s['table'];?></td><td><?php echo $s['total'];?></td><td><?php echo $s['has_geo'];?></td></tr>
        <?php } ?>
      </table>
    </td>
  </tr>
  <tr>
    <th>已有 GEO 数据的内容</th>
    <td class="y-bg">
      <?php if(empty($list)) { ?>
      <span style="color:#999">暂无内容设置地区/坐标。可在内容编辑表单填写 GEO 字段后在此查看。</span>
      <?php } else { ?>
      <table width="100%" class="table_form">
        <tr><th>ID</th><th>标题</th><th>内容表</th><th>栏目</th><th>地区编码</th><th>经度</th><th>纬度</th><th>操作</th></tr>
        <?php foreach($list as $v) { ?>
        <tr>
          <td><?php echo $v['id'];?></td>
          <td><?php echo htmlspecialchars($v['title']);?></td>
          <td><?php echo $v['tbl'];?></td>
          <td><?php echo htmlspecialchars($v['catname']);?></td>
          <td><?php echo $v['area_id'];?></td>
          <td><?php echo $v['lng'];?></td>
          <td><?php echo $v['lat'];?></td>
          <td><a href="?m=content&c=content&a=edit&catid=<?php echo $v['catid'];?>&id=<?php echo $v['id'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>" target="_blank">编辑</a></td>
        </tr>
        <?php } ?>
      </table>
      <?php } ?>
    </td>
  </tr>
  <tr>
    <th>快速前往</th>
    <td class="y-bg">
      <a href="?m=content&c=content&a=init&catid=6&pc_hash=<?php echo $_SESSION['pc_hash'];?>">→ 前往内容管理（文章测试栏目）</a>
    </td>
  </tr>
</table>
</div>
<?php include $this->admin_tpl('footer');?>
