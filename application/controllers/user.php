<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

	public function changePassword(){
		$username = $this->input->get_post('username');
		$pwd = $this->input->get_post('password');

		$data = array(
				'username' => $username,
				'password' => md5($pwd)
			);
		$this->UserModel->updatePasswordByName($data);
		echo json_encode(array('result' => true));
	}

	public function compareUserPassword(){
		$username = $this->input->get_post('username');
		$pwd = $this->input->get_post('password');

		$originPwd =$this->UserModel->getPasswordByName($username);
		
		if(md5($pwd) != $originPwd){
			echo json_encode(array('result' => false));
		}else{
			echo json_encode(array('result' => true));
		}
	}

	public function logout(){

		$this->session->unset_userdata('keep_user_id');
		delete_cookie('keep_user_id');
		echo json_encode(array('result' => true));
	}

	public function resetPassword(){
		$userId = $this->input->get_post('id');
		$password = md5('12345678');
		$this->UserModel->updatePasswordById($userId,$password);
		echo json_encode(array('result' => true));
	}

	public function deleteUser(){
		$userId = $this->input->get_post('id');
		$this->UserModel->deleteUserById($userId);
		echo json_encode(array('result' => true));
	}
}