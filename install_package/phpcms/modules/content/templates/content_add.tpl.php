<?php
defined('IN_ADMIN') or exit('No permission resources.');$addbg=1;
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = '<?php echo pc_base::load_config('system','upload_url')?>';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">var catid=<?php echo $catid;?></script>
<form name="myform" id="myform" action="?m=content&c=content&a=add" method="post" enctype="multipart/form-data">
<div class="addContent">
<div class="crumbs"><?php echo L('add_content_position');?></div>
<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">
<?php
if(is_array($forminfos['senior'])) {
 foreach($forminfos['senior'] as $field=>$info) {
	if($info['isomnipotent']) continue;
	if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<h6><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?></h6>
	 <?php echo $info['form']?><?php echo $info['tips']?> 
<?php
} }
?>
<?php if($_SESSION['roleid']==1 || $priv_status) {?>
<h6><?php echo L('c_status');?></h6>
<span class="ib" style="width:90px"><label><input type="radio" name="status" value="99" checked/> <?php echo L('c_publish');?> </label></span>
<?php if($workflowid) { ?><label><input type="radio" name="status" value="1" > <?php echo L('c_check');?> </label><?php }?>
<?php }?>
          </div>
        </div>
    </div>
    <a title="展开与关闭" class="r-close" hidefocus="hidefocus" style="outline-style: none; outline-width: medium;" id="RopenClose" href="javascript:;"><span class="hidden">展开</span></a>
    <div class="col-auto">
    	<div class="col-1">
        	<div class="content pad-6">
<table width="100%" cellspacing="0" class="table_form">
	<tbody>	
<?php
if(is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
	 if($info['isomnipotent']) continue;
	 if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<tr>
      <th width="80"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>
	  </th>
      <td><?php echo $info['form']?>  <?php echo $info['tips']?></td>
    </tr>
<?php
} }
?>
    <tr>
      <th width="80">GEO地区编码</th>
      <td><input type="text" class="input-text" name="info[area_id]" id="geo_area_id" value="<?php echo isset($_POST['info']['area_id']) ? intval($_POST['info']['area_id']) : '';?>" size="20"/> 地区编码（可选，地图选点后自动填入，用于后台GEO搜索）</td>
    </tr>
    <tr>
      <th width="80">经纬度</th>
      <td>
        经度：<input type="text" class="input-text" name="info[lng]" id="geo_lng" onblur="autoFillAreaCode()" value="<?php echo isset($_POST['info']['lng']) ? htmlspecialchars($_POST['info']['lng']) : '';?>" size="14"/>
        纬度：<input type="text" class="input-text" name="info[lat]" id="geo_lat" onblur="autoFillAreaCode()" value="<?php echo isset($_POST['info']['lat']) ? htmlspecialchars($_POST['info']['lat']) : '';?>" size="14"/>
        <input type="button" class="button" value="地图选点" onclick="openGEOPicker()"/>
        <span id="geo_addr" style="color:#888;margin-left:6px"></span>
        <div style="margin-top:4px;color:#999">点击「地图选点」打开地图，点击目标位置即可自动填入经纬度与地区编码。</div>
      </td>
    </tr>
    <!-- 地图选点弹窗 -->
    <tr id="geopicker_row" style="display:none">
      <th width="80"></th>
      <td>
        <div style="border:1px solid #ccc;padding:4px">
          <div style="margin-bottom:4px">在地图上点击目标位置选点：<input type="button" class="button" value="关闭" onclick="closeGEOPicker()"/></div>
          <div id="geopicker_map" style="width:600px;height:360px"></div>
        </div>
      </td>
    </tr>

    </tbody></table>
                </div>
        	</div>
        </div>
        
    </div>
</div>

<div class="fixed-bottom">
	<div class="fixed-but text-c">
    <div class="button"><input value="<?php echo L('save_close');?>" type="submit" name="dosubmit" class="cu" style="width:145px;" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" style="width:130px;" title="Alt+X" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('c_close');?>" type="button" name="close" onclick="refersh_window();close_window();" class="cu" style="width:70px;"></div>
      </div>
</div>
</form>

</body>
</html>
<script type="text/javascript"> 
<!--
//只能放到最下面
var openClose = $("#RopenClose"), rh = $(".addContent .col-auto").height(),colRight = $(".addContent .col-right"),valClose = getcookie('openClose');
$(function(){
	if(valClose==1){
		colRight.hide();
		openClose.addClass("r-open");
		openClose.removeClass("r-close");
	}else{
		colRight.show();
	}
	openClose.height(rh);
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({id:'check_content_id',content:msg,lock:true,width:'200',height:'50'}, 	function(){$(obj).focus();
	boxid = $(obj).attr('id');
	if($('#'+boxid).attr('boxid')!=undefined) {
		check_content(boxid);
	}
	})}});
	<?php echo $formValidator;?>
	
/*
 * 加载禁用外边链接
 */

	$('#linkurl').attr('disabled',true);
	$('#islink').attr('checked',false);
	$('.edit_content').hide();
	jQuery(document).bind('keydown', 'Alt+x', function (){close_window();});
})
document.title='<?php echo L('add_content');?>';
self.moveTo(-4, -4);
function refersh_window() {
	setcookie('refersh_time', 1);
}
openClose.click(
	  function (){
		if(colRight.css("display")=="none"){
			setcookie('openClose',0,1);
			openClose.addClass("r-close");
			openClose.removeClass("r-open");
			colRight.show();
		}else{
			openClose.addClass("r-open");
			openClose.removeClass("r-close");
			colRight.hide();
			setcookie('openClose',1,1);
		}
	}
)
//-->
</script>
<script type="text/javascript">
// ===== 高德地图选点 =====
var GEO_AMAP_KEY = "<?php
  $_gc = array();
  if(file_exists(CACHE_PATH.'configs/geo.php')){ $_gc = include CACHE_PATH.'configs/geo.php'; }
  echo isset($_gc['amap_key']) ? $_gc['amap_key'] : '';
?>";
var GEO_SECURITY_CODE = "<?php echo isset($_gc['security_js_code']) ? $_gc['security_js_code'] : '';?>";
if(GEO_SECURITY_CODE && typeof window._AMapSecurityConfig === 'undefined'){
	window._AMapSecurityConfig = { securityJsCode: GEO_SECURITY_CODE };
}
function loadAMap(cb){
	if(window.AMap){ cb(); return; }
	var s = document.createElement('script');
	s.src = 'https://webapi.amap.com/maps?v=2.0&key='+GEO_AMAP_KEY+'&plugin=AMap.Geocoder';
	s.onload = cb; s.onerror = function(){ alert('高德地图加载失败，请检查 GEO搜索 里的 Key'); };
	document.head.appendChild(s);
}
function openGEOPicker(){
	if(!GEO_AMAP_KEY){ alert('请先在 设置→相关设置→GEO搜索 中填写高德地图 Key'); return; }
	document.getElementById('geopicker_row').style.display = '';
	loadAMap(function(){
		var map = new AMap.Map('geopicker_map', {zoom:11, center:[116.397, 39.908]});
		map.on('click', function(e){
			var lng = e.lnglat.getLng(), lat = e.lnglat.getLat();
			document.getElementById('geo_lng').value = lng.toFixed(6);
			document.getElementById('geo_lat').value = lat.toFixed(6);
			AMap.plugin('AMap.Geocoder', function(){
				var gc = new AMap.Geocoder();
				gc.getAddress([lng, lat], function(status, result){
					var addr = '';
					if(status==='complete' && result.regeocode){
						addr = result.regeocode.formattedAddress;
						var adcode = result.regeocode.addressComponent.adcode;
						document.getElementById('geo_area_id').value = adcode;
					}
					document.getElementById('geo_addr').innerHTML = '已选：' + addr;
					closeGEOPicker();
				});
			});
		});
	});
}
function closeGEOPicker(){ document.getElementById('geopicker_row').style.display='none'; }
// 经纬度失焦后自动反查地区编码（高德逆地理编码）
function autoFillAreaCode(){
	var lng = document.getElementById('geo_lng').value;
	var lat = document.getElementById('geo_lat').value;
	var aid = document.getElementById('geo_area_id').value;
	if(!lng || !lat || aid) return;
	if(!GEO_AMAP_KEY) return;
	loadAMap(function(){
		AMap.plugin('AMap.Geocoder', function(){
			var gc = new AMap.Geocoder();
			gc.getAddress([parseFloat(lng), parseFloat(lat)], function(status, result){
				if(status==='complete' && result.regeocode){
					var adcode = result.regeocode.addressComponent.adcode;
					if(adcode){ document.getElementById('geo_area_id').value = adcode; }
					document.getElementById('geo_addr').innerHTML = '地区编码已自动填入：' + adcode;
				}
			});
		});
	});
}
</script>