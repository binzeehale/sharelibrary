<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {

	const NORMAL_USER = 0;
	const ADMIN = 1;

	public function index(){

		$redirect = $this->input->get_post("redirect");
		if($redirect == false){
			$this->load->view("login");
		}else{
			$this->load->view("login",array('redirect' => $redirect));
		}
	}

	public function verifyAccount(){

		$username = $this->input->get_post("username");
		$password = $this->input->get_post("password");
		$userId = $this->UserModel->getUserIdByNameAndPassword($username , md5($password));
		if($userId === -1){
			echo 'false';
		}else{
			echo 'true';
		}
	}

	public function checkUserName(){

		$username = $this->input->get_post('signusername');

		$result = $this->UserModel->getUserIdByName($username);
		if($result == -1){
			echo 'true';
		}else{
			echo 'false';
		}
		
	}

	public function signIn(){

		$username = $this->input->get_post("username");
		$password = $this->input->get_post("password");
		$keepLive  = $this->input->get_post("remember");
		$redirect = $this->input->get_post("redirect");

		$password = md5($password);

		$userId = $this->UserModel->getUserIdByNameAndPassword($username , $password);
		if($userId === -1){
			return false;
		}

		if(!$keepLive){
			$this->session->set_userdata('keep_user_id', $userId);
			delete_cookie('keep_user_id');
		}else{
			$cookie = array(
					'name' => 'keep_user_id',
					'value' => $userId,
					'expire' => 3600 * 24 * 365,
					'path' => '/'
				);
			$this->input->set_cookie($cookie); 
		}
		if(!$redirect){
			redirect('/');
		}else{
			header('Location: '.$redirect);
		}
	}

	public function signUp(){

		$username = $this->input->get_post("signusername");
		$password = $this->input->get_post("signpassword");

		$data = array(
				'username' => $username,
				'password' => md5($password),
				'weight' => self::NORMAL_USER
 			);

		$this->UserModel->addUser($data);
		//echo json_encode(array('result' => true));
		redirect('/login');
	}

}