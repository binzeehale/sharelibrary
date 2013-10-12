<?php
if ( ! function_exists('json_response'))
{
	function json_response($data , $type = true )
	{
		$response = array(
				'action' => $type?'success':'error',
				'data' => $data
			);
		return json_encode($response);
	}
}