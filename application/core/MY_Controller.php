<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {


    function __construct()
    {
        parent::__construct();
    }

    public $header =array();

    public function showView( $file , $data = array() ){

        $userId = $this->getUserId();
        $username = $this->UserModel->getUserNameById($userId);
        $users = $this->UserModel->getNormalUsers();

        $this->header = array_merge(array('userId' => $userId ,'username'=>$username , 'users' => $users),$this->header);

        $this->load->view('header',$this->header);
        $this->load->view('normal/'.$file ,$data);
        $this->load->view('footer');

    }

    public function setHeader($arr = array()){
        $this->header =  $arr;
    }

    public function getUserId(){

        $userId = $this->input->cookie('keep_user_id',true);

        if(!isset($userId) || empty($userId)){
            $userId = $this->session->userdata('keep_user_id');
        }

        if(!isset($userId) || empty($userId)){
            $url = $this->_getCurrentUrl();
            redirect('/login?redirect='.$url);
        }

        return $userId;
    }

    private function _getCurrentUrl(){
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

}