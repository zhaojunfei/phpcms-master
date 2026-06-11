<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$upurl = "index.php?m=attachment&c=attachments&a=crop_upload&module=$module&catid=$catid&file=".urlencode($picurl);
?>		    
<script type="text/javascript" src="<?php echo JS_PATH?>crop/swfobject.js"></script>
<link href="<?php echo JS_PATH?>cropper/ImgCropping.css" rel="stylesheet">
<link href="<?php echo JS_PATH?>cropper/cropper.min.css" rel="stylesheet">
<script src="<?php echo JS_PATH?>cropper/cropper.min.js"></script>
<style>
#crop-area {
	height: 430px;
	display: flex;
	align-items: center;
}

#crop-info {
	display: flex;
	justify-content: space-between;
	padding: 10px;
	font-size: 14px;
	background: #F5F5F5;
	border-top: 2px solid #9E9E9E;
	border-bottom: 1px solid #ccc;
}

.crop-btns {
	display: flex;
	align-items: center;
}

.crop-btns a {
	text-decoration: none;
	background: #DDD;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#DDDDDD');
	background: linear-gradient(top, #FFF, #DDD);
	background: -moz-linear-gradient(top, #FFF, #DDD);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFF), to(#DDD));
	text-shadow: 0px 1px 1px rgba(255, 255, 255, 1);
	box-shadow: 0 1px 0 rgba(255, 255, 255, .7), 0 -1px 0 rgba(0, 0, 0, .09);
	color: #757575;
	border-radius: 4px;
	font-size: 12px;
	margin-left: 10px;
	display: inline-block;
	height: 22px;
	width: 58px;
	text-align: center;
	border: 1px solid #9E9E9E;
}
</style>
<script>
	var crop_mode = 'h5';
	var isIE = navigator.userAgent.indexOf("compatible") > -1 && navigator.userAgent.indexOf("MSIE") > -1;
	//检测placeholder得知是否为ie10+
	function is_support_placeholder() {
		var input = document.createElement('input');
		return 'placeholder' in input;
	};
	// 获取页面上的flash实例。
	// @param flashID 这个参数是：flash 的 ID 。本例子的flash ID是 "myFlashID" ，在本页面搜索一下 "myFlashID" 可看到。
	function getFlash(flashID) 
	{
		// 判断浏览器类型
		if (navigator.appName.indexOf("Microsoft") != -1) 
		{
			return window[flashID];
		} 
		else 
		{
			return document[flashID];
		}
	}
	
	// flash 上传图片完成时回调的函数。
	function uploadComplete(pic)
	{
		
		if(parent.document.getElementById('<?php echo $_GET['input']?>')) {
			var input = parent.document.getElementById('<?php echo $_GET['input']?>');
		} else {
			var input = parent.right.document.getElementById('<?php echo $_GET['input']?>');
		}
		<?php if (!empty($_GET['preview'])):?>
		if(parent.document.getElementById('<?php echo $_GET['preview']?>')) {
			var preview = parent.document.getElementById('<?php echo $_GET['preview']?>');
		} else {
			var preview = parent.right.document.getElementById('<?php echo $_GET['preview']?>');
		}
		<?php else:?>
		var preview = '';
		<?php endif;?>
		if(pic) {
			input.value = pic;
			if (preview) preview.src = pic;
		}
		window.top.art.dialog({id:'crop'}).close();
	}

	function uploadfile() {
		if(crop_mode == 'h5') {
			var cas = $('#crop-img').cropper('getCroppedCanvas', {
				imageSmoothingQuality: 'high'
			});
			cas.toBlob(function(img) {
				var oReq = new XMLHttpRequest();
				oReq.open("POST", '<?php echo $upurl?>', true);
				oReq.onload = function(oEvent) {
					uploadComplete(oReq.response)
				};
				oReq.send(img);
			}, "image/jpeg");
		} else {
			getFlash('myFlashID').upload();
		}
	}
	
	$(function(){
		if(isIE && !is_support_placeholder()) {
			crop_mode = 'flash';
			var swfVersionStr = "10.0.0";
			var xiSwfUrlStr = "<?php echo JS_PATH?>crop/images/playerProductInstall.swf";
			
			var flashvars = {};
			// 图片地址
			flashvars.picurl = "<?php echo $picurl?>";
			// 上传地址，使用了 base64 加密
			flashvars.uploadurl = "<?php echo base64_encode($upurl);?>";
			
			var params = {};
			params.quality = "high";
			params.bgcolor = "#ffffff";
			params.allowscriptaccess = "always";
			params.allowfullscreen = "true";
			var attributes = {};
			attributes.id = "myFlashID";
			attributes.name = "myFlashID";
			attributes.align = "middle";
			swfobject.embedSWF(
				"<?php echo JS_PATH?>crop/images/Main.swf", "flashContent", 
				"680", "480", 
				swfVersionStr, xiSwfUrlStr, 
				flashvars, params, attributes);
			<!-- JavaScript enabled so display the flashContent div in case it is not replaced with a swf object. -->
			swfobject.createCSS("#flashContent", "display:block;text-align:left;");
		} else {
			$('#flashContent').hide();
			$('#h5Canvas').show();
			//cropper init
			$('#crop-img').cropper({
				viewMode:1,
				dragMode:"crop",
				zoomable:false,
				resizable:true,
				touchDragZoom:false,
				crop: function(e) {
					$('#crop_size_w').html(parseInt(e.width))
					$('#crop_size_h').html(parseInt(e.height))
				}
			});
			//左旋转
			$('.J_rotateLeft').on('click', function() {
				$('#crop-img').cropper("rotate", 45);
			})
			//右旋转
			$('.J_rotateRight').on('click', function() {
				$('#crop-img').cropper("rotate", -45);
			})
			//重置
			$('.J_reset').on('click', function() {
				$('#crop-img').cropper("reset");
			})
		}
	})
	//实现toBlob
	if (!HTMLCanvasElement.prototype.toBlob) {
		Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
			value: function(callback, type, quality) {
				var canvas = this;
				setTimeout(function() {
					var binStr = atob(canvas.toDataURL(type, quality).split(',')[1]);
					var len = binStr.length;
					var arr = new Uint8Array(len);
					for (var i = 0; i < len; i++) {
						arr[i] = binStr.charCodeAt(i);
					}
					callback(new Blob([arr], {
						type: type || 'image/jpeg'
					}));
				});
			}
		});
	}
	</script>
    </head>
    <body>
        <div id="flashContent">
        	<p>
	        	To view this page ensure that Adobe Flash Player version 
				10.0.0 or greater is installed. 
			</p>
			<script type="text/javascript"> 
				var pageHost = ((document.location.protocol == "https:") ? "https://" :	"http://"); 
				document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
								+ pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
			</script> 
        </div>
		   <div id="h5Canvas" style="display:none">
			<div id="crop-area" class="cropper-bg">
				<img id="crop-img" src="<?php echo $picurl?>" />
			</div>
			<div id="crop-info">
				<div class="info">
					裁剪尺寸（宽：<span id="crop_size_w">-</span>px 高：<span id="crop_size_h">-</span>px）
				</div>
				<div class="crop-btns">
					<a href="javascript:void(0)" class="J_rotateLeft">向左旋转</a>
					<a href="javascript:void(0)" class="J_rotateRight">向右旋转</a>
					<a href="javascript:void(0)" class="J_reset">重置裁剪</a>
				</div>
			</div>
		</div>
       	<noscript>
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="680" height="480" id="myFlashID">
                <param name="movie" value="<?php echo JS_PATH?>crop/images/Main.swf" />
                <param name="quality" value="high" />
                <param name="bgcolor" value="#ffffff" />
                <param name="allowScriptAccess" value="always" />
                <param name="allowFullScreen" value="true" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="<?php echo JS_PATH?>crop/images/Main.swf" width="680" height="480">
                    <param name="quality" value="high" />
                    <param name="bgcolor" value="#ffffff" />
                    <param name="allowScriptAccess" value="always" />
                    <param name="allowFullScreen" value="true" />
                <!--<![endif]-->
                <!--[if gte IE 6]>-->
                	<p> 
                		Either scripts and active content are not permitted to run or Adobe Flash Player version
                		10.0.0 or greater is not installed.
                	</p>
                <!--<![endif]-->
                    <a href="http://www.adobe.com/go/getflashplayer">
                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                    </a>
                <!--[if !IE]>-->
                </object>
                <!--<![endif]-->
            </object>
	    </noscript>
       </body>
       </html>
