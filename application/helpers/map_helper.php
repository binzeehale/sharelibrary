<?php
if ( ! function_exists('map_doc_type'))
{
	function map_doc_type($type)
	{

		$map = array(
				ZB_FOLDER => '目录',
				ZB_FILE => '文件'
			);
		return isset($map[$type])?$map[$type]:"";
	}
}