//上传弹出
function h5upload(uploadid, name, textareaid, funcName, args, module, catid, authkey) {
	var args = args ? '&args='+args : '';
	var setting = '&module='+module+'&catid='+catid+'&authkey='+authkey;
	window.top.art.dialog({title:name,id:uploadid,iframe:'index.php?m=attachment&c=attachments&a=h5upload'+args+setting,width:'500',height:'420'}, function(){ if(funcName) {funcName.apply(this,[uploadid,textareaid]);}else {submit_ckeditor(uploadid,textareaid);}}, function(){window.top.art.dialog({id:uploadid}).close()});
}

//上传完成
function handle_uploadComplete(file, serverData){
	var rsp = JSON.parse(serverData);
	if(rsp.aid == 0) {
		alert(rsp.msg);
		return false;
	}
	if(rsp.is_image == 1) {
		var img = '<a href="javascript:;" onclick="javascript:att_cancel(this,'+rsp.aid+',\'upload\')" class="on"><div class="icon"></div><img src="'+rsp.url+'" width="80" imgid="'+rsp.aid+'" path="'+rsp.url+'" title="'+rsp.filename+'"/></a>';
	} else {
		var img = '<a href="javascript:;" onclick="javascript:att_cancel(this,'+rsp.aid+',\'upload\')" class="on"><div class="icon"></div><img src="statics/images/ext/'+rsp.fileext+'.png" width="80" imgid="'+rsp.aid+'" path="'+rsp.url+'" title="'+rsp.filename+'"/></a>';
	}
	$.get('index.php?m=attachment&c=attachments&a=upload_json&aid='+rsp.aid+'&src='+rsp.url+'&filename='+rsp.filename);
	$('#h5UploadProgress').append('<li><div id="attachment_'+rsp.aid+'" class="img-wrap"></div></li>');
	$('#attachment_'+rsp.aid).html(img);
	$('#att-status').append('|'+rsp.url);
	$('#att-name').append('|'+rsp.filename);
	file.queueItem.hide();
	console.log('UploadComplete:', file, rsp);
}

//上传进度
function handle_uploadProgress(file, event){
	console.log('Progress:', file, event);
}

//不支持
function handle_uploadFallback(){
	alert('浏览器不支持HTML5,无法上传');
}

//上传错误
function handle_uploadError(err, file){
	console.log('Error:', err, file);
	var errmsg = '';
	switch(err) {
		case 'QUEUE_LIMIT_EXCEEDED':
			errmsg = '任务数量超出队列限制';
			break;
		case 'UPLOAD_LIMIT_EXCEEDED':
			//errmsg = '上传的文件数量已经超出系统限制';
			break;
		case 'FILE_SIZE_LIMIT_EXCEEDED':
			errmsg = '文件大小超出系统限制';
			$(this).data('uploadifive').removeQueueItem(file, 500, 1000);
			break;
		case 'FORBIDDEN_FILE_TYPE':
			errmsg = '文件格式不被允许';
			break;
		case '404_FILE_NOT_FOUND':
			errmsg = '文件未上传成功或服务器存放文件的文件夹不存在';
			break;
	}
	if(errmsg != '') {
		$('#h5UploadError').html(errmsg).show();
		setTimeout(function(){
			$('#h5UploadError').fadeOut(200);
		}, 1000);
	}
}

function att_insert(obj,id)
{
	var uploadfile = $("#attachment_"+id+"> img").attr('path');
	$('#att-status').append('|'+uploadfile);
}

function att_cancel(obj,id,source){
	var src = $(obj).children("img").attr("path");
	var filename = $(obj).children("img").attr("title");
	if($(obj).hasClass('on')){
		$(obj).removeClass("on");
		var imgstr = $("#att-status").html();
		var length = $("a[class='on']").children("img").length;
		var strs = filenames = '';
		for(var i=0;i<length;i++){
			strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
			filenames += '|'+$("a[class='on']").children("img").eq(i).attr('title');
		}
		$('#att-status').html(strs);
		$('#att-name').html(filenames);
		if(source=='upload') $('#att-status-del').append('|'+id);
	} else {
		$(obj).addClass("on");
		$('#att-status').append('|'+src);
		$('#att-name').append('|'+filename);
		var imgstr_del = $("#att-status-del").html();
		var imgstr_del_obj = $("a[class!='on']").children("img")
		var length_del = imgstr_del_obj.length;
		var strs_del='';
		for(var i=0;i<length_del;i++){strs_del += '|'+imgstr_del_obj.eq(i).attr('imgid');}
		if(source=='upload') $('#att-status-del').html(strs_del);
	}
}

function submit_ckeditor(uploadid,textareaid){
	var d = window.top.art.dialog({id:uploadid}).data.iframe;
	var in_content = d.$("#att-status").html();
	var del_content = d.$("#att-status-del").html();
	insert2editor(textareaid,in_content,del_content)
}

function submit_images(uploadid,returnid){
	var d = window.top.art.dialog({id:uploadid}).data.iframe;
	var in_content = d.$("#att-status").html().substring(1);
	var in_content = in_content.split('|');
	IsImg(in_content[0]) ? $('#'+returnid).attr("value",in_content[0]) : alert('选择的类型必须为图片类型');
}

function submit_attachment(uploadid,returnid){
	var d = window.top.art.dialog({id:uploadid}).data.iframe;
	var in_content = d.$("#att-status").html().substring(1);
	var in_content = in_content.split('|');
	$('#'+returnid).attr("value",in_content[0]);
}

function submit_files(uploadid,returnid){
	var d = window.top.art.dialog({id:uploadid}).data.iframe;
	var in_content = d.$("#att-status").html().substring(1);
	var in_content = in_content.split('|');
	var new_filepath = in_content[0].replace(uploadurl,'/');
	$('#'+returnid).attr("value",new_filepath);
}

function insert2editor(id,in_content,del_content) {	
	if(in_content == '') {return false;}
	var data = in_content.substring(1).split('|');
	var img = '';
	for (var n in data) {
		img += IsImg(data[n]) ? '<img src="'+data[n]+'" /><br />' : (IsSwf(data[n]) ? '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"><param name="quality" value="high" /><param name="movie" value="'+data[n]+'" /><embed pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'+data[n]+'" type="application/x-shockwave-flash" width="460"></embed></object>' :'<a href="'+data[n]+'" />'+data[n]+'</a><br />') ;
	}
	$.get("index.php?m=attachment&c=attachments&a=delete",{data: del_content},function(data){});
	CKEDITOR.instances[id].insertHtml(img);
}

function IsImg(url){
	var sTemp;
	var b=false;
	var opt="jpg|gif|png|bmp|jpeg";
	var s=opt.toUpperCase().split("|");
	for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
			b=true;
			break;
		}
	}
	return b;
}

function IsSwf(url){
	var sTemp;
	var b=false;
	var opt="swf";
	var s=opt.toUpperCase().split("|");
	for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
			b=true;
			break;
		}
	}
	return b;
}