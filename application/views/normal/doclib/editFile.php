<div class="container">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?=base_url('/doclib/search')?>">查找资料</a>
			</li>
			<li class="active">
				<a href="<?=base_url('/doclib/index').'/'.$file['parent_id']?>">资料一览</a>
			</li>
			<li>
				<a href="<?=base_url('/doclib/upload').'/'.$file['parent_id']?>">上传资料</a>
			</li>
		</ul>
	</div>
	<div class="row-fluid">

		<form id="folderForm" class="form-horizontal" action="<?=base_url('/doclib/saveFile')?>" method="POST">
			<fieldset>
				<div id="legend" class="">
					<legend class="">修改文件信息</legend>
				</div>
				<div class="control-group">
					<label class="control-label" for="folderName">名称</label>
					<div class="controls">
						<span><?=$file['name']?></span>
						<input name="fileId" type="hidden" value="<?=$file['id']?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="folderName">标记</label>
					<div class="controls">
						<?php if($file['parent_id'] == 0):?>
						<select name="mark">
							<option value="0">无</option>
							<?php foreach($marks as $mark):?>
							<?php if($mark['id'] == $file['mark_id']):?>
								<option value="<?=$mark['id']?>" selected><?=$mark['name']?></option>
							<?php else: ?>
								<option value="<?=$mark['id']?>"><?=$mark['name']?></option>
							<?php endif; ?>
							<?php endforeach;?>
						</select>
						<?php else:?>
						<span><?=$file['mark_name']?></span>
						<?php endif;?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">所属目录</label>
					<div class="controls">
						<select name="parentId">
						<?php foreach($folders as $f ): ?>
						<?php if($f['id'] == $file['parent_id']): ?>
							<option selected value="<?=$f['id']?>"><?=$f['name']?></option>
						<?php else: ?>
							<option value="<?=$f['id']?>"><?=$f['name']?></option>
						<?php endif; ?>
						<?php endforeach;?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button class="btn btn-success">保存</button>
						<a class="btn" href="<?=base_url('/doclib/index').'/'.$file['parent_id']?>">返回</a>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
<script type="text/javascript">

</script>