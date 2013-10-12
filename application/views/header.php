<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>资料管理系统</title>

  <!-- Le styles -->
  <link href="<?=base_url('/resources/public/bootstrap/css/bootstrap.css')?>" rel="stylesheet">
  <link href="<?=base_url('/resources/css/dataTable.bootstrap.css')?>" rel="stylesheet">
  <link href="<?=base_url('/resources/css/global.css')?>" rel="stylesheet">
  <link href="<?=base_url('/resources/public/bootstrap-timepicker/css/datetimepicker.css')?>" rel="stylesheet">
  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="<?=base_url('/resources/js/html5shiv.js')?>"></script>
  <![endif]-->
</head>
<body>
	<script src="<?=base_url('/resources/public/bootstrap/assets/js/jquery.js')?>"></script>
	<script src="<?=base_url('/resources/public/bootstrap/assets/js/jquery.validate.js')?>"></script>
	<script src="<?=base_url('/resources/js/bootstrap.jquery.validate.js')?>"></script>
	<script src="<?=base_url('/resources/language/jquery.validate.chinese.js')?>"></script>
	<script src="<?=base_url('/resources/public/bootstrap/js/bootstrap.min.js')?>"></script>
	<script src="<?=base_url('/resources/public/bootstrap/assets/js/bootbox.min.js')?>"></script>
	<script src="<?=base_url('/resources/public/jquery.dataTable.js')?>"></script>
	<script src="<?=base_url('/resources/public/dataTables.bootstrap.js')?>"></script>

	<script type="text/javascript" charset="utf-8" src="<?=base_url('/resources/public/tableTool/media/js/ZeroClipboard.js')?>"></script>
	<script type="text/javascript" charset="utf-8" src="<?=base_url('/resources/public/tableTool/media/js/TableTools.js')?>"></script>
	
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="brand" href="#">资料管理系统</a>
				<div class="nav-collapse collapse">
					<ul class="nav pull-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?=$username?>	
								,您好 <b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li>
									<a id='changePws' href="#">修改密码</a>
								</li>
								<?php if($userId == 1): ?>	
								<li>
									<a id='manageUser' href="#">用户管理</a>
								</li>
								<?php endif; ?>	
								<li>
									<a href="<?=base_url('/main/backup')?>">备份数据</a>
								</li>
								<li class="divider"></li>
								<li>
									<a id="logout" href="#">注销</a>
								</li>
							</ul>
						</li>
					</ul>
					<ul class="nav">
		<!-- 				<li id="welcome-page">
							<a href="<?=base_url('/welcome')?>">欢迎</a>
						</li> -->
						<li id="lib-page">
							<a href="<?=base_url('/doclib')?>">资料库</a>
						</li>
						<li id="map-page">
							<a href="<?=base_url('/docmap')?>">资料地图</a>
						</li>
					</ul>
				</div>
				<script type="text/javascript">
		          (function(){
		              var menuTitle = $('#<?=$pageName?>');
		              $(menuTitle).addClass('active');
		          })();
		        </script>
			</div>
		</div>
	</div>