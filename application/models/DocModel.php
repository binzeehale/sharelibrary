<?php

class DocModel extends CI_Model {

	private $_table = "docs";

    function __construct()
    {
        parent::__construct();
    }

    public function getAll(){
		$this->db->order_by('last_update_time');
    	$query = $this->db->get($this->_table);
    	return $query->result_array();
    }

    public function getDocById($folder_id){
        $this->db->where('id',$folder_id);
        $query = $this->db->get($this->_table);
        return $query->row_array();
    }

    public function getDocsByType($type){
		
        $this->db->where('type',$type);
		$this->db->order_by('last_update_time');
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    public function getDocsByParentId($pid){
    	
    	$this->db->where('parent_id',$pid);
		$this->db->order_by('last_update_time');
    	$query = $this->db->get($this->_table);
    	return $query->result_array();
    }

    public function getDocsByParentIdAndMarkId($parent_id,$mark_id){

        $this->db->where('mark_id',$mark_id);
        $this->db->where('parent_id',$parent_id);
		$this->db->order_by('last_update_time');
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    public function getFilesByMarkId($mark_id){

        $this->db->where('mark_id',$mark_id);
        $this->db->where('type',ZB_FILE);
		$this->db->order_by('last_update_time');
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    public function searchByName($name){
        
        $this->db->like('name',$name);
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    public function insert($data){

    	$this->db->insert($this->_table,$data);
    	return $this->db->insert_id();
    }

    public function updateById($id,$data){

        $this->db->where('id',$id);
        return $this->db->update($this->_table,$data);
    }

    public function updateByMarkId($id, $data){
        $this->db->where('mark_id',$id);
        return $this->db->update($this->_table,$data);
    }

    public function deleteById($id){

        $this->db->where('id',$id);
        return $this->db->delete($this->_table);
    }
}