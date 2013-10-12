<?php

	function word2pdf($base,$path,$swftools,&$output){

		$output =  time() . rand(0, 99) . '.pdf';

		$run = $swftools . '/' . 'word2pdf.py';
		$run .= ' ' . $base . '/' . $path;
		$run .= ' ' . $base . '/' . $output;
		$run = preg_replace('/\//', '\\', $run);
		$run = iconv('UTF-8','gb2312', $run);
		return system($run);
	}

	function pdf2swf($base,$path,$swftools,&$output){

		$output = time() . rand(0, 99) . '.swf';
		$run = $swftools . '/' . 'pdf2swf.exe';
		$run .= ' ' . $base . '/' . $path;

		$run = preg_replace('/\//', '\\', $run);
		$run .= ' -o' . $output;
		$run .= ' -f -T 9 -t -s storeallcharacters';
		$run = iconv('UTF-8','gb2312', $run);
		ob_start();
		$ret = system($run);
		ob_clean();
		return $ret;
	}