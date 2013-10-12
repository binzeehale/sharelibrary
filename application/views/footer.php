<!-- Modal -->
<div id="pwdModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">修改密码</h3>
	</div>
	<div class="modal-body">
		  <form class="form-horizontal">
		  	<fieldset>
		  		<div class="control-group">

		  			<!-- Text input-->		  
		  			<label class="control-label" for="oldPassword">原密码</label>
		  			<div class="controls">
		  				<input id='oldPassword' type="password" class="input-xlarge">		  
		  				<p class="help-error help-block">原密码错误</p>
		  			</div>
		  		</div>

		  		<div class="control-group">

		  			<!-- Text input-->		  
		  			<label class="control-label" for="newPassword">新密码</label>
		  			<div class="controls">
		  				<input id='newPassword' type="password" class="input-xlarge">
		  			</div>
		  		</div>
		  		<div class="control-group">

		  			<!-- Text input-->		  
		  			<label class="control-label" for="secondPassword">再次输入新密码</label>
		  			<div class="controls">
		  				<input id='secondPassword' type="password" class="input-xlarge">		  
		  				<p class="help-error help-block">两次密码不一致</p>
		  			</div>
		  		</div>
		  	</fieldset>
		  </form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
		<button id='changePasswordBtn' class="btn btn-primary">确认</button>
	</div>
</div>

<div id="userControlModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">用户管理</h3>
  </div>
  <div class="modal-body">
  	<table id='userTable' class='table'>
  		<thead>
  			<tr>
  				<th>用户名</th>
  				<th>操作</th>
  			</tr>
  		</thead>
  		<tbody>
  			<?php foreach($users as $user):?>
  			<tr>
  				<td><?=$user['username']?></td>
  				<td sytle="text-align:center"> 
  					<a class='resetUserPassword' href="javascript:void(0);">重置密码</a>
  					<a class='deleteUser' href="javascript:void(0);">删除</a>
  					<input type="hidden" value="<?=$user['id']?>" />
  				</td>
  			</tr>
  			<?php endforeach;?>
  		</tbody>
  	</table>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    <button class="btn btn-primary">确认</button>
  </div>
</div>

<script>

$('#changePws').click(function(){

	$('#pwdModal').modal('show');
});

$('#changePasswordBtn').click(function(){

	$('.help-block').hide();

	var oldPwd = $("#oldPassword").val();
	var newPwd = $('#newPassword').val();
	var secPwd = $('#secondPassword').val();

	if(oldPwd == ""){
		return false;
	}else{
		if( secPwd != newPwd ){
			$('#secondPassword').siblings('.help-block').show();
			return false;
		}else{
			var username = '<?=$username?>';
			var result = true;
			$.ajax({
					url : '<?=base_url("/user/compareUserPassword")?>',
					type : 'GET',
					async : false,
					data: { username : username , password : oldPwd} , 
					dataType: 'json',
					success: function(data){
						console.log(data);
						if(!data.result){
							$('#oldPassword').siblings('.help-block').show();
							result = false;
						}
					}
				});

			if(!result) return false;

			$.get('<?=base_url("/user/changePassword")?>' , { password: newPwd , username:username } ,function(data){

				$("#oldPassword").val("");
				$('#newPassword').val("");
				$('#secondPassword').val("");

				$('#pwdModal').modal('hide');
			},'json');
			
		}
	}
});
	
$('#logout').click(function(){

	$.get('<?=base_url("user/logout")?>',function(data){
		window.location.href = "<?=base_url('/login')?>";
	},'json');
	return false;
});

$('#manageUser').click(function(){

	$('#userControlModal').modal('show');
	return false;
});

$(document).on('click' , '.resetUserPassword' , function(){

	var result = confirm('确定重置该用户的密码吗？（密码将会被重置为12345678）');
	if(!result) return false;

	var userId = $(this).siblings('input[type="hidden"]').val();
	$.post('<?=base_url("/user/resetPassword")?>',{ id : userId },function(data){
		window.location.reload();
	},'json');
});

$(document).on('click' , '.deleteUser' , function(){

	var result = confirm('确定删除该用户吗？');
	if(!result) return false;

	var userId = $(this).siblings('input[type="hidden"]').val();
	$.post('<?=base_url("/user/deleteUser")?>',{ id : userId },function(data){
		window.location.reload();
	},'json');
});


</script>
</body>
</html>