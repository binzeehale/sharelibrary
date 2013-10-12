<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Docmap extends MY_Controller {

	public function index(){

		$marks = $this->MarkModel->getAll();

		foreach($marks as &$mark){
			$mark['docs'] = $this->DocModel->getDocsByParentIdAndMarkId(0,$mark['id']);
		}

		$data = array(
				'marks' => $marks
			);
		$this->setHeader(array("pageName" => "map-page"));
		$this->showView('docmap/index',$data);
	}

	public function google(){
	
		$marks = $this->MarkModel->getAll();

		foreach($marks as &$mark){
			$mark['docs'] = $this->DocModel->getDocsByParentIdAndMarkId(0,$mark['id']);
		}

		$data = array(
				'marks' => $marks
			);
	
		$this->setHeader(array("pageName" => "map-page"));
		$this->showView('docmap/google',$data);
	}

	public function createMarker(){

		$marker_name = $this->input->get_post('markerName');
		$marker_lat = $this->input->get_post('lat');
		$marker_lng = $this->input->get_post('lng');
		$origin = $this->input->get_post('origin');
		
		if($marker_name){
			$data = array(
				'name' => $marker_name,
				'lat' => $marker_lat,
				'lng' => $marker_lng,
				'user_id' => $this->getUserId()
			);
			$this->MarkModel->insert($data);
		}
		if($origin == 'baidu'){
			redirect('/docmap/index');
		}else{
			redirect('/docmap/google');
		}
	}

	public function validateMarker(){

		$marker_name = $this->input->get_post('markerName');
		$markers = $this->MarkModel->getAll();
		foreach($markers as $marker){
			if($marker['name'] == $marker_name){
				echo 'false';
				return;
			}
		}
		echo 'true';
	}

	public function deleteMarker($markerId = -1){

		if ($markerId < 0){
			echo json_response('invalid Id',false);
		}else{
			$marker = $this->MarkModel->getMarkById($markerId);
			if(empty($marker)){
				echo json_response('invalid Id',false);
				return;
			}
			$this->DocModel->updateByMarkId($marker['id'],array('mark_id'=>0));
			$this->MarkModel->deleteById($marker['id']);
			echo json_response('');
		}
	}
}