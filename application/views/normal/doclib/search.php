<div class="container">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="<?=base_url('/doclib/search')?>">查找资料</a>
			</li>
			<li>
				<a href="<?=base_url('/doclib/index')?>">资料一览</a>
			</li>
			<li>
				<a href="<?=base_url('/doclib/upload').'/0'?>">上传资料</a>
			</li>
		</ul>
	</div>
	<div class="row-fluid">
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			根据文件或文件夹名称查找资料
		</div>
		<form  id="searchForm" action="<?=base_url('/doclib/search')?>" method="POST">
			<div class="controls">
				<input type="text" name="context" class="input-block-level" value="<?=$context?>"/>
				<button type="submit" class="btn btn-success pull-right" style="position:relative;top:-40px;">查询</button>
			</div>
		</form>
	</div>
	<div class="row-fluid">
		<table id="docTable" class="table">
			<thead>
				<th width="50%">名称</th>
				<th width="10%">类型</th>
				<th width="40%">标记</th>
			</thead>
			<tbody>
				<?php foreach($docs as $doc): ?>
				<?php if($doc['type'] == ZB_FOLDER):?>
				<tr>
					<td><a href="<?=base_url('/doclib/index').'/'.$doc['id']?>"><i class="icon-folder-close"></i><?=$doc['name']?></a></td>
					<td><?=map_doc_type($doc['type'])?></td>
					<td><?=$doc['mark_name']?$doc['mark_name']:'--'?></td>
				</tr>
				<?php elseif($doc['type'] == ZB_FILE):?>
				<tr>
					<td><a href="javascript:void(0);"  onclick='scan_file(<?=$doc['id']?>)'><i class="icon-file"></i><?=$doc['name']?></a></td>
					<td><?=map_doc_type($doc['type'])?></td>
					<td><?=$doc['mark_name']?$doc['mark_name']:'--'?></td>
				</tr>
				<?php endif;?>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">

function scan_file(id){
	window.location.href = "<?=base_url('/doclib/paper')?>" + '/' + id;
}

$('#docTable').dataTable({
	//"sDom": "<'row-fluid'<'span6'T><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    "sDom": "<'row-fluid'<'span12'l>r>t<'row-fluid'<'span6'i><'span6'p>>",
    "sPaginationType": "bootstrap",
    "oLanguage": {
      "sLengthMenu": "_MENU_ records per page",
      "sUrl": "<?=base_url('/resources/language/chinese.lag')?>"
    },
    "bFilter": true,
    "bSort": false,
    "iDisplayLength":50
});
</script>