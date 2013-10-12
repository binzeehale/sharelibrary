<div class="container">
	<ul class="nav nav-tabs">
		<li>
			<a href="<?=base_url('/doclib/search')?>">查找资料</a>
		</li>
		<li>
			<a href="<?=base_url('/doclib/index').'/'.$folder['id']?>">资料一览</a>
		</li>
		<li class="active">
			<a href="<?=base_url('/doclib/upload').'/'.$folder['id']?>">上传资料</a>
		</li>
	</ul>
	<div class="well">
		<span>当前目录：<?=$folder['fullpath']?></span>
	</div>
	
	<div id="processFiles" style="display:none;margin-top:20px;margin-bottom:20px;">
		<div class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>注意！</strong>请不要在文件未转换成功前跳转页面！否则文件将不会保存。
		</div>
		<table class="table">
			<thead>
				<th width="30%">文件名</th>
				<th>转换进度</th>
			</thead>
			<tbody>
				
			</tbody>
		</table>	
	</div>
	<div id="flash_uploader">浏览器不支持Flash.</div>
</div>
<style type="text/css">
	@import url("<?=base_url('/resources/public/jquery.plupload.queue/css/jquery.plupload.queue.css')?>");
</style>
<script type="text/javascript" src="<?=base_url('/resources/public/jquery.plupload.queue/plupload.full.js')?>"></script>
<script type="text/javascript" src="<?=base_url('/resources/public/jquery.plupload.queue/jquery.plupload.queue.js')?>"></script>
<script type="text/javascript">

function uploadQueue(obj){

	objList.push(obj);
	if(objList.length == fileCount){
		for(var i in objList){
			addFile(objList[i]);	
		}
		objList = [];
	}
}

function addFile(obj){

	var id = obj.id;
	var name = obj.result;
	//console.log(id ,name);
	$('#processFiles').show();

	var tpl = [
		'<tr id="file_$id$">',
			'<td>$file$</td>',
			'<td class="progress-box">',
				'<div class="progress progress-striped active">',
				  '<div class="bar" style="width: 100%;"></div>',
				'</div>',
			'</td>',
		'</tr>'
	].join('');

	var html = tpl.replace(/\$id\$/,obj.id)
				   .replace(/\$file\$/,obj.result);
	$('#processFiles tbody').append(html);
	setTimeout(function(){
		$.ajax({
			url: '<?=base_url("/doclib/covert")."/"?>' + obj.id,
			type: 'POST',
			async: false,
			data: { markId : "<?=$folder['mark_id']?>", parentId: "<?=$folder['id']?>" },
			dataType: 'json',
			success :function(response){
				if(response.action == 'success'){
					$('#file_'+obj.id).children('.progress-box').html('<p>已完成</center>');
				}else{
					$('#file_'+obj.id).children('.progress-box').html('<p>转换失败'+ '<span>' + response.data + '</span>' +'</p>');
				}
			}
		});
	},500);
}

function check(){
	if (fileCount != uploadFileCount){
		//console.log(fileCount , uploadFileCount ) ;
		return false;
	}
	return true;
}

var fileCount = 0;
var uploadFileCount = 0;
var objList = [];

$(document).ready(function(){
	
	$("#flash_uploader").pluploadQueue({
			// General settings
			runtimes : 'flash',
			url : '<?=base_url("/doclib/upload2")?>',
			max_file_size : '1500mb',
			unique_names : false,
			urlstream_upload : true,
			// Resize images on clientside if we can
			//resize : {width : 320, height : 240, quality : 90},
			// Specify what files to browse for
			filters : [
				{title : "WORD(.doc,.docx)" , extensions : "doc,docx"},
				{title : "PDF(.pdf)" , extensions : "pdf"}
			],
			// Flash settings
			flash_swf_url : '<?=base_url("/resources/public/jquery.plupload.queue/plupload.flash.swf")?>',
			init: {
				FileUploaded: function(up, file, info) {
	                // Called when a file has finished uploading
	                //console.log('[FileUploaded] File:', file, "Info:", info);
	                var response = info.response;
	                //console.log(response);
	                var obj = JSON.parse(response);
	                uploadFileCount++;
	                uploadQueue(obj);
	            },
	            FilesAdded: function(up, files) {
	                // Callced when files are added to queue
	            	fileCount += files.length;
	            },
	            FilesRemoved: function(up, files) {
	                // Called when files where removed from queue
	            	fileCount -= files.length;
	            },
				 Error: function(up, args) {		 
               	  // Called when a error has occured
              	   //console.log('[error] ', args);
				}				
			}
	});
});
</script>