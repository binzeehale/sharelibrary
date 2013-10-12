<div class="container">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?=base_url('/doclib/search')?>">查找资料</a>
			</li>
			<li  class="active">
				<a href="<?=base_url('/doclib/index').'/'.$file['parent_id']?>">资料一览</a>
			</li>
			<li>
				<a href="<?=base_url('/doclib/upload').'/'.$file['parent_id']?>">上传资料</a>
			</li>
		</ul>
	</div>
	<div class="row-fluid">
		<ul class="breadcrumb">
			<?php foreach($parents as $parent):?>
				<li>
					<a href="<?=base_url('/doclib/index').'/'.$parent['id']?>"><?=$parent['name']?></a>
					<span class="divider">/</span>
				</li>
			<?php endforeach;?>
			<li class="active"><?=$file['name']?></li>
		</ul>
		<div id="documentViewer" class="flexpaper_viewer" style="height:600px"></div>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="<?=base_url("resources/public/flexpaper/css/flexpaper.css")?>" />
<script type="text/javascript" src="<?=base_url("resources/public/flexpaper/js/flexpaper.js")?>"></script>
<script type="text/javascript" src="<?=base_url("resources/public/flexpaper/js/flexpaper_handlers.js")?>"></script>

<script type="text/javascript">

    $('#documentViewer').FlexPaperViewer(
            { 
            	config : {
	                SWFFile : "<?=base_url('/'.$file['path'])?>",
	                Scale : 0.6,
	                ZoomTransition : 'easeOut',
	                ZoomTime : 0.5,
	                ZoomInterval : 0.2,
	                FitPageOnLoad : false,
	                FitWidthOnLoad : true,
	                FullScreenAsMaxWindow : false,
	                ProgressiveLoading : false,
	                MinZoomSize : 0.2,
	                MaxZoomSize : 5,
	                SearchMatchAll : false,
	                InitViewMode : 'Portrait',
	                RenderingOrder : 'flash',
	                StartAtPage : '',
	                // ViewModeToolsVisible : true,
	                // ZoomToolsVisible : true,
	                // NavToolsVisible : true,
	                CursorToolsVisible : false,
	                // SearchToolsVisible : true,
	                WMode : 'window',
	                localeChain: 'zh_CN',
	            	jsDirectory: '<?=base_url("resources/public/flexpaper/js")?>'+'/'
	            }
        	}
    );
</script>