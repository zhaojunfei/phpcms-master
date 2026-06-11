<?php
	/**
	 * 返回附件类型图标
	 * @param $file 附件名称
	 * @param $type png为大图标，gif为小图标
	 */
	function file_icon($file,$type = 'png') {
		$ext_arr = array('doc','docx','ppt','xls','txt','pdf','mdb','jpg','gif','png','bmp','jpeg','rar','zip','swf','flv');
		$ext = fileext($file);
		if($type == 'png') {
			if($ext == 'zip' || $ext == 'rar') $ext = 'rar';
			elseif($ext == 'doc' || $ext == 'docx') $ext = 'doc';
			elseif($ext == 'xls' || $ext == 'xlsx') $ext = 'xls';
			elseif($ext == 'ppt' || $ext == 'pptx') $ext = 'ppt';
			elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb') $ext = 'flv';
			else $ext = 'do';
		}
		if(in_array($ext,$ext_arr)) return 'statics/images/ext/'.$ext.'.'.$type;
		else return 'statics/images/ext/blank.'.$type;
	}
	
	/**
	 * 附件目录列表，暂时没用
	 * @param $dirpath 目录路径
	 * @param $currentdir 当前目录
	 */
	function file_list($dirpath,$currentdir) {
		$filepath = $dirpath.$currentdir;
		$list['list'] = glob($filepath.DIRECTORY_SEPARATOR.'*');
		if(!empty($list['list'])) rsort($list['list']);
		$list['local'] = str_replace(array(PC_PATH, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('',DIRECTORY_SEPARATOR), $filepath);
		return $list;
	}

	/**
	 * 初始化上传参数
	 *
	 * @param string $module
	 * @param integer $catid
	 * @param string $args
	 * @param integer $userid
	 * @param integer $groupid
	 * @param integer $isadmin
	 * @param string $userid_flash
	 * @return void
	 */
	function init_upload($module, $catid, $args, $userid, $groupid = 8, $isadmin = 0, $userid_flash = '0')
	{
		$grouplist = getcache('grouplist', 'member');
		if ($isadmin == 0 && !$grouplist[$groupid]['allowattachment']) {
			return false;
		}
		$admin_url = pc_base::load_config('system', 'admin_url');
		$upload_path = $isadmin && !empty($admin_url) ? SITE_PROTOCOL . $admin_url . '/' : APP_PATH;
		$upload_url = $upload_path.'index.php?m=attachment&c=attachments&a=h5upload&args='.$args.'&authkey='.upload_key($args).'&dosubmit=1';
		$args_arr = array_combine(array('upload_num', 'allowext', 'isselectimage', 'thumb_width', 'thumb_height', 'watermark'), explode(',', $args));
		$siteid = param::get_cookie('siteid');
		$site_setting = get_site_setting($siteid);
		if($args_arr['watermark'] === '') {
			$args_arr['watermark'] = $site_setting['watermark_enable'];
		}
		$output = '
		var uploadurl = "";
		$(document).ready(function(){			
			$("#file_upload").uploadifive({
				"auto": false,
				"buttonText" :"",
				"queueSizeLimit":'.$args_arr['upload_num'].',
				"fileSizeLimit": '.$site_setting['upload_maxsize'].',
				"fileType": "'.(empty($args_arr['allowext']) ? '' : '.'.str_replace('|', ',.', $args_arr["allowext"])).'",
				"uploadLimit" : '.$args_arr['upload_num'].',
				"formData": {
					"module" : "'.$module.'",
					"catid" : "'.$catid.'",
					"siteid" : "'.$siteid.'",
					"groupid" : '.$groupid.',
					"isadmin" : '.$isadmin.',
					"watermark" : "'.$args_arr["watermark"].'"
				},
				"queueID": "h5UploadProgress",
				"uploadScript": "'.$upload_url.'",
				"onFallback" : handle_uploadFallback,
				"onProgress": handle_uploadProgress,
				"onUploadComplete" : handle_uploadComplete, 
				"onError": handle_uploadError,
				"overrideEvents" : ["onError"],
				"width":75,
				"height":28
			});
		})';
		return $output;
	}

	/**
	 * 获取站点配置信息
	 * @param  $siteid 站点id
	 */
	function get_site_setting($siteid) {
		$siteinfo = getcache('sitelist', 'commons');
		return string2array($siteinfo[$siteid]['setting']);
	}
	/**
	 * 读取swfupload配置类型
	 * @param array $args flash上传配置信息
	 */
	function parse_upload_args($args) {
		$siteid = get_siteid();
		$site_setting = get_site_setting($siteid);
		$site_allowext = $site_setting['upload_allowext'];
		$args = explode(',',$args);
		$arr['file_upload_limit'] = intval($args[0]) ? intval($args[0]) : '8';
		$args['1'] = ($args[1]!='') ? $args[1] : $site_allowext;
		$arr_allowext = explode('|', $args[1]);
		foreach($arr_allowext as $k=>$v) {
			$v = '*.'.$v;
			$array[$k] = $v;
		}
		$upload_allowext = implode(';', $array);
		$arr['file_types'] = $upload_allowext;
		$arr['file_types_post'] = $args[1];
		$arr['allowupload'] = intval($args[2]);
		$arr['thumb_width'] = intval($args[3]);
		$arr['thumb_height'] = intval($args[4]);
		$arr['watermark_enable'] = ($args[5]=='') ? 1 : intval($args[5]);
		return $arr;
	}	
	/**
	 * 判断是否为图片
	 */
	function is_image($file) {
		$ext_arr = array('jpg','gif','png','bmp','jpeg','tiff');
		$ext = fileext($file);
		return in_array($ext,$ext_arr) ? $ext_arr :false;
	}
	
	/**
	 * 判断是否为视频
	 */
	function is_video($file) {
		$ext_arr = array('rm','mpg','avi','mpeg','wmv','flv','asf','rmvb');
		$ext = fileext($file);
		return in_array($ext,$ext_arr) ? $ext_arr :false;
	}
