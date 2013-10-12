<div class="container">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?=base_url('/doclib/search')?>">查找资料</a>
			</li>
			<li class="active">
				<a href="<?=base_url('/doclib/index').'/'.$folder['parent_id']?>">资料一览</a>
			</li>
			<li>
				<a href="<?=base_url('/doclib/upload').'/'.$folder['parent_id']?>">上传资料</a>
			</li>
		</ul>
	</div>
	<div class="row-fluid">

		<form id="folderForm" class="form-horizontal" action="<?=base_url('/doclib/saveFolder')?>" method="POST">
			<fieldset>
				<div id="legend" class="">
					<legend class="">修改目录信息</legend>
				</div>
				<div class="control-group">
					<label class="control-label" for="folderName">名称</label>
					<div class="controls">
						<input id="folderName" name="folderName" type="text" value="<?=$folder['name']?>">
						<input name="folderId" type="hidden" value="<?=$folder['id']?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="folderName">标记</label>
					<div class="controls">
						<?php if($folder['parent_id'] == 0):?>
						<select name="mark">
							<option value="0">无</option>
							<?php foreach($marks as $mark):?>
							<?php if($mark['id'] == $folder['mark_id']):?>
								<option value="<?=$mark['id']?>" selected><?=$mark['name']?></option>
							<?php else: ?>
								<option value="<?=$mark['id']?>"><?=$mark['name']?></option>
							<?php endif; ?>
							<?php endforeach;?>
						</select>
						<?php else:?>
						<span><?=$folder['mark_name']?></span>
						<?php endif;?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">所属目录</label>
					<div class="controls">
						<select name="parentId">
						<?php foreach($folders as $f ): ?>
						<?php if($f['id'] == $folder['parent_id']): ?>
							<option selected value="<?=$f['id']?>"><?=$f['name']?></option>
						<?php else: ?>
							<option value="<?=$f['id']?>"><?=$f['name']?></option>
						<?php endif; ?>
						<?php endforeach;?>
						</select>
						<p class="help-block">只能移动到非当前子目录的目录</p>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button class="btn btn-success">保存</button>
						<a class="btn" href="<?=base_url('/doclib/index').'/'.$folder['parent_id']?>">返回</a>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
<script type="text/javascript">

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
            parentId: "<?=$folder['parent_id']?>",
            folderId: "<?=$folder['id']?>"
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
$('#folderForm').validate(validateOptions);

</script>