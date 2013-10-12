<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doclib extends MY_Controller {

	public function index($folder_id = 0){

		$docs = $this->_get_folder($folder_id);
		$this->load->library('ArraySorter', array('array'=>$docs,'key'=>'type','asc'=>false));
		$docs = $this->arraysorter->sort();

		$folder = $this->DocModel->getDocById($folder_id);
		$parent_id = 0;
		if($folder){
			$parent_id = $folder['parent_id'];
		}else{
			$folder = array('id'=> 0 , 'name' => '根目录','mark_id' => '0');
		}
		$folder_mark = $this->MarkModel->getMarkById($folder['mark_id']);
		$folder['mark_name'] = isset($folder_mark['name'])?$folder_mark['name']:'无';

		$parents = $this->_generateParents($parent_id);
		$parents = array_reverse($parents);
		foreach($docs as &$doc){
			$mark = $this->MarkModel->getMarkById($doc['mark_id']);

			if(empty($mark)){
				$doc['mark_name'] = '--';
			}else{
				$doc['mark_name'] = $mark['name'];
			}
		}

		$data = array(
			'docs'=>$docs , 
			'folder' => $folder , 
			'marks' => $this->MarkModel->getAll(),
			'parent_id' => $parent_id , 
			'parents' => $parents
		);
		$this->setHeader(array("pageName" => "lib-page"));
		$this->showView('doclib/index',$data);
	}

	public function search(){

		$context = $this->input->get_post('context');
		$data = array('docs'=>array(),'context'=>'');

		if($context){
			$docs = $this->DocModel->searchByName($context);
			$this->load->library('ArraySorter', array('array'=>$docs,'key'=>'type','asc'=>false));
			$docs = $this->arraysorter->sort();
			foreach($docs as &$doc){
				$mark = $this->MarkModel->getMarkById($doc['mark_id']);

				if(empty($mark)){
					$doc['mark_name'] = '--';
				}else{
					$doc['mark_name'] = $mark['name'];
				}
			}
			$data = array('docs'=>$docs,'context'=>$context);
		}

		$this->setHeader(array("pageName" => "lib-page"));
		$this->showView('doclib/search',$data);
	}

	public function createFolder(){

		$folder_name = $this->input->get_post('folderName');
		$parent_id = $this->input->get_post('parentId');
		$mark_id = $this->input->get_post('mark');

		$data = array(
				'name' => $folder_name,
				'mark_id' => $mark_id,
				'type' => ZB_FOLDER,
				'parent_id' => $parent_id,
				'user_id' => $this->getUserId()
			);
		$folder_id = $this->DocModel->insert($data);
		redirect('/doclib/index/'.$parent_id);
	}

	public function validateFolderName(){

		$folder_id = $this->input->get_post('folderId');
		$folder = $this->DocModel->getDocById($folder_id);

		$folder_name = $this->input->get_post('folderName');
		$parent_id = $this->input->get_post('parentId');
		if($folder_name && is_string($parent_id) ){
			$docs = $this->_get_folder($parent_id);
			foreach($docs as $doc){
				if($doc == $folder){
					continue;
				}
				if($doc['name'] == $folder_name && $doc['type'] == ZB_FOLDER ){
					echo 'false';
					return;
				}
			}
			echo 'true';
		}else{
			echo 'false';
		}
	}

	public function editFolder($folder_id = null ){
		if( !preg_match("/\d+/",$folder_id )){
			show_404();
		}

		$folder = $this->DocModel->getDocById($folder_id);
		if(!$folder){
			show_404();
		}
		$mark = $this->MarkModel->getMarkById($folder['mark_id']);
		$folder['mark_name'] = isset($mark['name'])?$mark['name']:'无';

		$folders = $this->DocModel->getDocsByType(ZB_FOLDER);
		
		//自身和自身的子目录是不可移动到的
		$unavliableFolders = $this->_generateChildren($folder_id);
		$unavliableFolders[] = $folder;
		
		$unavliableFolderIds = array();
		foreach($unavliableFolders as $f){
			if(!empty($f))
				$unavliableFolderIds[] = $f['id'];
		}

		$aviliableFolders = array();
		foreach($folders as $f){
			if(!in_array($f['id'], $unavliableFolderIds)){

				$f['name'] = $this->_generateFullPath($f['id']);
				$aviliableFolders[] = $f;
			}
		}

		array_unshift($aviliableFolders , array('id'=> 0 , 'name' => '根目录'));

		$data = array( 
			'folder'=>$folder,
			'folders'=>$aviliableFolders, 
			'marks' => $this->MarkModel->getAll()
		);
		$this->setHeader(array("pageName" => "lib-page"));
		$this->showView('doclib/editFolder',$data);
	}

	public function editFile( $file_id = null ){
		if( !preg_match("/\d+/",$file_id )){
			show_404();
		}

		$file = $this->DocModel->getDocById($file_id);
		if(!$file){
			show_404();
		}
		$mark = $this->MarkModel->getMarkById($file['mark_id']);
		$file['mark_name'] = isset($mark['name'])?$mark['name']:'无';

		$folders = $this->DocModel->getDocsByType(ZB_FOLDER);
		
		$aviliableFolders = array();
		foreach($folders as $f){
			$f['name'] = $this->_generateFullPath($f['id']);
			$aviliableFolders[] = $f;
		}

		array_unshift($aviliableFolders , array('id'=> 0 , 'name' => '根目录'));

		$data = array( 
			'file'=>$file,
			'folders'=>$aviliableFolders, 
			'marks' => $this->MarkModel->getAll()
		);
		$this->setHeader(array("pageName" => "lib-page"));
		$this->showView('doclib/editFile',$data);	
	}

	public function saveFile(){
		
		$file_id = $this->input->get_post('fileId');
		$file = $this->DocModel->getDocById($file_id);
		if(!$file){
			show_404();
		}
		$file_parent_id = $this->input->get_post('parentId');
		$file_mark_id = $this->input->get_post('mark');

		$file['mark_id'] = $file_mark_id;
		$file['parent_id'] = $file_parent_id;

		$parent = $this->DocModel->getDocById($file_parent_id);
		if(!empty($parent)){
			$file['mark_id'] = $parent['mark_id'];
		}

		unset($file['id']);
		unset($file['name']);
		unset($file['tmp_id']);
		unset($file['last_update_time']);
		$this->DocModel->updateById($file_id,$file);

		redirect('/doclib/index/'.$file['parent_id']);
	}

	public function saveFolder(){

		$folder_id = $this->input->get_post('folderId');
		$folder = $this->DocModel->getDocById($folder_id);
		if(!$folder){
			show_404();
		}
		$folder_name = $this->input->get_post('folderName');
		$folder_parent_id = $this->input->get_post('parentId');
		$folder_mark_id = $this->input->get_post('mark');

		$folder['mark_id'] = $folder_mark_id;
		$folder['name'] = $folder_name;
		$folder['parent_id'] = $folder_parent_id;

		$parent = $this->DocModel->getDocById($folder_parent_id);
		if(!empty($parent)){
			$folder['mark_id'] = $parent['mark_id'];
		}

		$childrens = $this->_generateChildren($folder_id);
		foreach($childrens as $children){
			$this->DocModel->updateById($children['id'],array('mark_id'=>$folder['mark_id']));
		}

		unset($folder['id']);
		unset($folder['last_update_time']);
		$this->DocModel->updateById($folder_id,$folder);

		redirect('/doclib/index/'.$folder['parent_id']);
	}

	public function deleteFolder(){

		$folder_id = $this->input->get_post('folderId');
		$childrens = $this->_generateChildren($folder_id);
		foreach($childrens as $child){
			if(!empty($child)){
				$this->DocModel->deleteById($child['id']);
			}
		}
		$this->DocModel->deleteById($folder_id);
		echo json_response('');
	}

	public function deleteFile(){
		$file_id = $this->input->get_post('fileId');
		if(!$file_id){
			echo json_response('unavliable id',false);
		}else{
			$file = $this->DocModel->getDocById($file_id);
			if(empty($file)){
				echo json_response('can\'t find file',false);
			}else{
				$tmp_id = $file['tmp_id'];
				if($tmp_id){
					$tmp = $this->TmpModel->getTmpById($tmp_id);
					$base = $_SERVER['DOCUMENT_ROOT'] . '/sharelibrary';
					$path = preg_replace('/\//', '\\',$base . '/' . $tmp['path']);
					$path = iconv('UTF-8', 'gb2312', $path);
					@unlink($path);
					$this->TmpModel->deleteById($tmp['id']);
				}
				$this->DocModel->deleteById($file['id']);
				echo json_response('');
			}
		}
	}

	public function paper($file_id = -1){

		if($file_id < 0){
			show_404();
		}

		$file = $this->DocModel->getDocById($file_id);
		if(empty($file)){
			show_404();
		}
		$parents = array_reverse($this->_generateParents($file['parent_id']));

		$file['path'] = str_replace('\\','/',$file['path']);

		$data = array(
				'parents' => $parents,
				'file' => $file
			);
		$this->setHeader(array("pageName" => "lib-page"));
		$this->showView('doclib/paperView',$data);
	}

	public function upload($folder_id = -1){
		
		$folder = array();
		if($folder_id < 0){
			show_404();
		}else if ($folder_id == 0){
			$folder = array('id'=> 0 , 'name'=>'根目录','mark_id'=>'0');
		}else{
			$folder = $this->DocModel->getDocById($folder_id);
		}

		$folder['fullpath'] = $this->_generateFullPath($folder_id);

		if(empty($folder)){
			show_404();
		}
		$data = array(
				'folder' => $folder
			);

		$this->setHeader(array("pageName" => "lib-page"));
		$this->showView('doclib/upload',$data);
	}

	public function upload2(){
		/**
		 * upload.php
		 *
		 * Copyright 2009, Moxiecode Systems AB
		 * Released under GPL License.
		 *
		 * License: http://www.plupload.com/license
		 * Contributing: http://www.plupload.com/contributing
		 */
		
		// HTTP headers for no cache etc
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Settings
		//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		$targetDir = 'uploads' . DIRECTORY_SEPARATOR . 'files';
		
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds
		
		// 60 minutes execution time
		@set_time_limit(60 * 60);
		
		// Uncomment this one to fake upload time
		// usleep(5000);
		
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		// Clean the fileName for security reasons
		$fileName = iconv('UTF-8','gb2312',$fileName); 
		//$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

		$ext =  $this->_extend($fileName);
		// $dir = mapExtToDir(strtolower($ext));
		// if(!empty($dir)){
		// 	$targetDir .= DIRECTORY_SEPARATOR . $dir;
		// }
	
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);
		
			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;
		
			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}
		
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		
		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);
		
		// Remove old temp files	
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
		
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}
		
			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	
		
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");
		
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");
		
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		
				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off 
			rename("{$filePath}.part", $filePath);
			
			//save to db
			//$pathinfo = pathinfo(realpath($filePath));
			//$type = mapExtToType(strtolower($pathinfo['extension']));
			$userId = $this->getUserId();
			
			 $arr = array(
			 	'name' => iconv('gb2312','UTF-8',$fileName),
			 	//'type' => ZB_FILE,
			 	'path' =>  iconv('gb2312','UTF-8',preg_replace('/\\\/', '/', $filePath)),
			 	//'last_upload_date' => date('Y-m-d H:i:s')
			);
			//$id = 0;
			$id = $this->TmpModel->insert($arr);
			echo '{"jsonrpc" : "2.0", "result" : "'.iconv('gb2312','UTF-8',$fileName).'", "id" : "'.$id.'"}';
		}
		// Return JSON-RPC response
	}

	public function covert( $file_id = -1 ){

		$this->load->helper('convert');

		if($file_id < 0){
			echo json_response('unavliable id',false);
			return;
		}

		$tmp = $this->TmpModel->getTmpById($file_id);
		if(empty($tmp)){
			echo json_response('unavliable id',false);
			return;
		}

		$mark_id = $this->input->get_post('markId');
		$parent_id = $this->input->get_post('parentId');
		
		$base = $_SERVER['DOCUMENT_ROOT'] . '/sharelibrary';
		$swftools = $base . '/uploads/tools';
		$pdf = '';
		$swf = '';

		$ext = $this->_extend($tmp['path']);
		if( $ext == 'doc' || $ext == 'docx'){
			$ret = word2pdf($base,$tmp['path'],$swftools,$pdf);
			if($ret === false ){
				echo json_response('convert to pdf error',false);
				return;
			}
			$ret = pdf2swf($base, $pdf ,$swftools, $swf);

			if($ret === false ){
				echo json_response('convert to swf error',fasle);
				return;
			}
			$base = preg_replace('/\//', '\\', $base);
			rename($base . '\\' . $swf, $base . '\\' . 'uploads\\swf\\' . $swf);
			unlink($base . '\\' . $pdf);
		}else{

			$ret = pdf2swf($base, $tmp['path'] ,$swftools, $swf);
			if($ret === false ){
				echo json_response('convert to swf error',fasle);
				return;
			}
			$base = preg_replace('/\//', '\\', $base);
			rename($base . '\\' . $swf, $base . '\\' . 'uploads\\swf\\' . $swf);
		}

		$data = array(
			'name' => $tmp['name'],
			'type' => ZB_FILE,
			'path' => 'uploads\\swf\\'.$swf,
			'mark_id' => $mark_id,
			'parent_id' => $parent_id,//'last_update_time' => date('Y-m-d H:i:s')
			'tmp_id' => $tmp['id'],
			'user_id' => $this->getUserId()
		);
		$this->DocModel->insert($data);
		echo json_response('');
	}
	
	private function _extend($file_name) 
	{ 
		$extend =explode("." , $file_name); 
		$va=count($extend)-1; 
		return $extend[$va]; 
	}

	private function _get_folder($fid){
		return $this->DocModel->getDocsByParentId($fid);
	}

	private function _generateParents($parent_id){

		$parents = array();
		if($parent_id == 0){
			return array(array('id'=> 0 , 'name' => '根目录'));
		}else{
			$folder = $this->DocModel->getDocById($parent_id);
			$parents[] = $folder;
			$_parents = $this->_generateParents($folder['parent_id']);
			foreach($_parents as $f){
				$parents[] = $f;
			}
			return $parents;
		}
	}

	private function _generateChildren($folder_id){
		
		$children = array();
		$children = $this->DocModel->getDocsByParentId($folder_id);
		if(empty($children)){
			return array(array());
		}else{
			foreach($children as $child){
				$children[] = $child;
				$children = array_merge($children , $this->_generateChildren($child['id']));
				return $children;
			}
		}
	}

	private function _generateFullPath($folder_id){

		$name = array();
		$parents = $this->_generateParents($folder_id);
		$parents = array_reverse($parents);
		foreach($parents as $parent){
			$name[] = $parent['name'];
		}
		return implode($name, '/');
	}
}