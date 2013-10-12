<?php

class UserModel extends CI_Model {

	private $_table = "user";

    function __construct()
    {
        parent::__construct();
    }

    public function getUserIdByName($name){
        $sql = "select id from " .$this->_table . " where username=? limit 1";
        $query = $this->db->query($sql , array($name));
        
        if($query->num_rows() > 0){
            $row = $query->row_array();
            return $row['id'];
        }else{
            return -1;
        }
    }

    public function getUserNameById($id){

    	$sql = "select username from ". $this->_table . " where id =?";
    	$query = $this->db->query($sql , array($id));
    	$row = $query->row_array();
    	return $row['username'];
    }

    public function getUserIdByNameAndPassword($username , $password){

    	$sql = "select id from " .$this->_table . " where username=? and password=? limit 1";
    	$query = $this->db->query($sql , array($username, $password));
        
    	if($query->num_rows() > 0){
			$row = $query->row_array();
			return $row['id'];
		}else{
			return -1;
		}
    }

    public function getPasswordByName($name){
        $this->db->where('username' , $name);
        $query = $this->db->get($this->_table);
        $row = $query->row_array();
        return $row['password'];
    }

    public function getNormalUsers(){
        $this->db->where('weight' , '0');
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    public function addUser($data){
    	$this->db->insert($this->_table,$data);
    }

    public function updatePasswordByName($data){
        $this->db->where('username' , $data['username']);
        $this->db->update($this->_table , array('password' => $data['password']));
    }

    public function updatePasswordById($id,$password){
        $this->db->where('id' , $id);
        $this->db->update($this->_table , array('password' => $password));
    }

    public function deleteUserById($id){
        $this->db->where('id' , $id);
        $this->db->delete($this->_table);
    }
}