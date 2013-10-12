<?php

class TmpModel extends CI_Model {

	private $_table = "tmp";

    function __construct()
    {
        parent::__construct();
    }

    public function getAll(){
    	$query = $this->db->get($this->_table);
    	return $query->result_array();
    }

    public function getTmpById($folder_id){
        $this->db->where('id',$folder_id);
        $query = $this->db->get($this->_table);
        return $query->row_array();
    }

    public function insert($data){

    	$this->db->insert($this->_table,$data);
    	return $this->db->insert_id();
    }

    public function updateById($id,$data){

        $this->db->where('id',$id);
        return $this->db->update($this->_table,$data);
    }

    public function deleteById($id){

        $this->db->where('id',$id);
        return $this->db->delete($this->_table);
    }
}