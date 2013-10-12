<div class="container">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?=base_url('/doclib/search')?>">查找资料</a>
			</li>
			<li class="active">
				<a href="<?=base_url('/doclib/index').'/'.$folder['id']?>">资料一览</a>
			</li>
			<li>
				<a href="<?=base_url('/doclib/upload').'/'.$folder['id']?>">上传资料</a>
			</li>
		</ul>
		<ul class="breadcrumb">
			<?php if( $folder['id'] === 0):?>
				<li class="active">根目录</li>
			<?php else: ?>
			<?php foreach($parents as $parent):?>
				<li>
					<a href="<?=base_url('/doclib/index').'/'.$parent['id']?>"><?=$parent['name']?></a>
					<span class="divider">/</span>
				</li>
			<?php endforeach;?>
				<li class="active"><?=$folder['name']?></li>
			<?php endif;?>
		</ul>
		<table id="docTable" class="table">
			<thead>
				<th width="40%">名称</th>
				<th width="10%">类型</th>
				<th width="40%">标记</th>
				<th width="10%">操作</th>
			</thead>
			<tbody>
				<tr>
					<td><a href="<?=base_url('/doclib/index').'/'.$parent_id?>">...</a></td>
					<td>--</td>
					<td>--</td>
					<td>
						<a title="新建文件夹" href="javascript:void(0);" onclick="create_folder()"><i class="icon-folder-open"></i></a>
						<a title="新建文件" href="<?=base_url('/doclib/upload').'/'.$folder['id']?>"> <i class="icon-circle-arrow-up"> </i> </a>
					</td>
				</tr>
				<?php foreach($docs as $doc): ?>
				<?php if($doc['type'] == ZB_FOLDER):?>
				<tr>
					<td><a href="<?=base_url('/doclib/index').'/'.$doc['id']?>"><i class="icon-folder-close"></i><?=$doc['name']?></a></td>
					<td><?=map_doc_type($doc['type'])?></td>
					<td><?=$doc['mark_name']?$doc['mark_name']:'--'?></td>
					<td>
						<a href="<?=base_url('/doclib/editFolder').'/'.$doc['id']?>"><i class="icon-edit"></i></a>
						<a href="javascript:void(0);" onclick="delete_folder('<?=$doc['id']?>')"><i class="icon-trash"></i></a>
					</td>
				</tr>
				<?php elseif($doc['type'] == ZB_FILE):?>
				<tr>
					<td><a href="javascript:void(0);"  onclick='scan_file(<?=$doc['id']?>)'><i class="icon-file"></i><?=$doc['name']?></a></td>
					<td><?=map_doc_type($doc['type'])?></td>
					<td><?=$doc['mark_name']?$doc['mark_name']:'--'?></td>
					<td>
						<a href="javascript:void(0);" onclick="edit_file(<?=$doc['id']?>)"><i class="icon-edit"></i></a>
						<a href="javascript:void(0);" onclick="delete_file(<?=$doc['id']?>)"><i class="icon-trash"></i></a>
					</td>
				</tr>
				<?php endif;?>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
</div>
<!-- /container -->	
<div id="creatFolderModal" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>添加目录</h3>
  </div>
  <div class="modal-body">
  	<form id="createFolderForm" class="form-horizontal" action="<?=base_url('/doclib/createFolder')?>" method="POST" >
		<div class="control-group">
			<label class="control-label" for="folderName">名称</label>
			<div class="controls">
				<input type="text" id="folderName" name="folderName" value="" />
				<input type="hidden" name="parentId" value="<?=$folder['id']?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="folderName">标记</label>
			<div class="controls">
				<?php if($folder['id'] == 0): ?>
				<select name="mark">
					<option value="0">无</option>
					<?php foreach($marks as $mark):?>
					<?php if($mark['id'] == $folder['mark_id']):?>
					<option value="<?=$mark['id']?>" selected><?=$mark['name']?></option>
					<?php else:?>
					<option value="<?=$mark['id']?>"><?=$mark['name']?></option>
					<?php endif;?>
					<?php endforeach;?>
				</select>
				<?php else:?>
				<span><?=$folder['mark_name']?></span>
				<input type="hidden" name="mark" value="<?=$folder['mark_id']?>">
				<?php endif;?>
			</div>
		</div>
  	</form> 
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">关闭</a>
    <a href="javascript:void(0);" onclick="$('#createFolderForm').submit();" class="btn btn-primary">确认</a>
  </div>
</div>


<!-- Bootstrap core JavaScript
================================================== -->	
<!-- Placed at the end of the document so the pages load faster -->	
<script type="text/javascript">

function scan_file(id){
	window.location.href = "<?=base_url('/doclib/paper')?>" + '/' + id;
}

function edit_file(id){
	window.location.href = "<?=base_url('/doclib/editFile')?>" + '/' + id;
}

function delete_file(id){

	bootbox.confirm('确认删除该文件?', function(result) {
		if(result){
			$.post("<?=base_url('/doclib/deleteFile')?>" , { fileId:id } ,function(response){
				if(response.action == 'success'){
					window.location.reload();
				}else{
					alert(response.data);
				}
			},'json');
		}		
	});
}

function create_folder(){

	createFolderValidate.resetForm('reset');
	$('#creatFolderModal').modal('show');
}

function delete_folder(folderId){

	bootbox.confirm('确认删除该目录及其子目录和文件?', function(result) {
		if(result){
			$.post("<?=base_url('/doclib/deleteFolder')?>" , { folderId:folderId } ,function(response){
				window.location.reload();
			},'json');
		}		
	});
}

$('#docTable').dataTable({
	//"sDom": "<'row-fluid'<'span6'T><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    "sPaginationType": "bootstrap",
    "oLanguage": {
      "sLengthMenu": "_MENU_ records per page",
      "sUrl": "<?=base_url('/resources/language/chinese.lag')?>"
    },
    "bFilter": true,
    "bSort": false,
    "iDisplayLength":50
});

validateOptions = {
    rules:{
      folderName: {
        required: true,
        legalStringCN: true,
        remote: {
          url: "<?=base_url('/doclib/validateFolderName')?>",
          type: "post",
          data: {
            folderName: function() {
              return $( "#folderName" ).val();
            },
            parentId: "<?=$folder['id']?>"
          }
        }
      }
  	},
    messages: {
      folderName: {
        remote: "目录名称已使用"
      }
    }
};
var createFolderValidate = $('#createFolderForm').validate(validateOptions);
</script>