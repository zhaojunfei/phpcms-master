<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="<?php echo CHARSET;?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo L('phpcms_logon')?></title>
<style type="text/css">
	*{margin:0;padding:0;box-sizing:border-box;}
	html,body{height:100%;}
	body{
		font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif;
		background:#0f2027;
		background:-webkit-linear-gradient(135deg,#0f2027 0%,#203a43 45%,#2c5364 100%);
		background:linear-gradient(135deg,#0f2027 0%,#203a43 45%,#2c5364 100%);
		display:flex;align-items:center;justify-content:center;
		min-height:100vh;
		position:relative;overflow:hidden;
	}
	/* 背景装饰光斑 */
	body::before,body::after{
		content:"";position:absolute;border-radius:50%;filter:blur(90px);opacity:.55;z-index:0;
	}
	body::before{width:420px;height:420px;background:#4facfe;top:-120px;left:-100px;}
	body::after{width:380px;height:380px;background:#00f2fe;bottom:-120px;right:-100px;}

	.login-wrap{position:relative;z-index:1;width:100%;max-width:400px;padding:20px;}
	.login-card{
		background:rgba(255,255,255,.08);
		-webkit-backdrop-filter:blur(18px);
		backdrop-filter:blur(18px);
		border:1px solid rgba(255,255,255,.16);
		border-radius:16px;
		box-shadow:0 20px 60px rgba(0,0,0,.35);
		padding:40px 36px 30px;
	}
	.login-head{text-align:center;margin-bottom:30px;}
	.login-logo{
		width:56px;height:56px;margin:0 auto 14px;border-radius:14px;
		background:linear-gradient(135deg,#4facfe,#00f2fe);
		display:flex;align-items:center;justify-content:center;
		font-size:26px;font-weight:700;color:#fff;
		box-shadow:0 8px 24px rgba(79,172,254,.4);
	}
	.login-title{font-size:22px;font-weight:600;color:#fff;letter-spacing:1px;}
	.login-sub{font-size:13px;color:rgba(255,255,255,.6);margin-top:6px;}

	.form-item{position:relative;margin-bottom:18px;}
	.form-item label{
		display:block;font-size:13px;color:rgba(255,255,255,.85);margin-bottom:7px;font-weight:500;
	}
	.form-item input[type=text],.form-item input[type=password]{
		width:100%;height:44px;padding:0 14px;border-radius:9px;
		border:1px solid rgba(255,255,255,.22);background:rgba(255,255,255,.1);
		color:#fff;font-size:14px;outline:none;transition:all .25s;
	}
	.form-item input:focus{border-color:#4facfe;background:rgba(255,255,255,.16);box-shadow:0 0 0 3px rgba(79,172,254,.22);}
	.form-item input::placeholder{color:rgba(255,255,255,.4);}

	.code-row{display:flex;gap:12px;align-items:flex-end;}
	.code-row .code-input{flex:1;}
	.code-row img{height:44px;border-radius:9px;cursor:pointer;border:1px solid rgba(255,255,255,.22);background:#fff;}
	.code-tip{font-size:11px;color:rgba(255,255,255,.5);margin-top:6px;}
	.code-tip a{color:#7ec8ff;text-decoration:none;}
	.code-tip a:hover{text-decoration:underline;}

	.btn-login{
		width:100%;height:46px;margin-top:8px;border:none;border-radius:9px;cursor:pointer;
		background:linear-gradient(135deg,#4facfe,#00c6fb);
		color:#fff;font-size:15px;font-weight:600;letter-spacing:2px;
		box-shadow:0 10px 26px rgba(79,172,254,.38);transition:all .25s;
	}
	.btn-login:hover{transform:translateY(-2px);box-shadow:0 14px 32px rgba(79,172,254,.5);}
	.btn-login:active{transform:translateY(0);}

	.login-footer{text-align:center;margin-top:24px;font-size:12px;color:rgba(255,255,255,.45);}
	.login-footer a{color:rgba(255,255,255,.6);text-decoration:none;}
	.login-footer a:hover{color:#7ec8ff;}

	.copyright{position:relative;z-index:1;text-align:center;margin-top:22px;font-size:12px;color:rgba(255,255,255,.4);}
</style>
<script language="JavaScript">
	if(top!=self)
		if(self!=top) top.location=self.location;
</script>
</head>
<body onload="javascript:document.myform.username.focus();">
	<div class="login-wrap">
		<div class="login-card">
			<div class="login-head">
				<div class="login-logo">PC</div>
				<div class="login-title">PHPCMS 后台管理</div>
				<div class="login-sub">Content Management System</div>
			</div>
			<form action="index.php?m=admin&c=index&a=login&dosubmit=1" method="post" name="myform">
				<div class="form-item">
					<label><?php echo L('username')?></label>
					<input name="username" type="text" placeholder="<?php echo L('username')?>" value="" autocomplete="username" />
				</div>
				<div class="form-item">
					<label><?php echo L('password')?></label>
					<input name="password" type="password" placeholder="<?php echo L('password')?>" value="" autocomplete="current-password" />
				</div>
				<div class="form-item">
					<label><?php echo L('security_code')?></label>
					<div class="code-row">
						<div class="code-input">
							<input name="code" type="text" placeholder="<?php echo L('security_code')?>" />
						</div>
						<?php echo form::checkcode('code_img')?>
					</div>
					<div class="code-tip">
						<a href="javascript:document.getElementById('code_img').src='<?php echo SITE_PROTOCOL.SITE_URL.WEB_PATH;?>api.php?op=checkcode&m=admin&c=index&a=checkcode&time='+Math.random();void(0);"><?php echo L('click_change_validate')?></a>
					</div>
				</div>
				<button type="submit" class="btn-login">登 录</button>
			</form>
			<div class="login-footer">
				<?php echo L("copyright")?>
			</div>
		</div>
	</div>
</body>
</html>
