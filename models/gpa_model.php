<?php
class Gpa_model extends CI_Model {
    
    public function __construct()
    {
        $this->load->database();
    }
    
    public function get_table($sem, $dept, $type)
    {
        if ($type == 'gpa')
        {
            $this->db->select('subject_code, subject_name, credits');
            $this->db->where(array('semester' => $sem, 'department' => $dept));
            $query = $this->db->get('subjects'); //equivalent to FROM clause
        }
        elseif ($type == 'cgpa')
        {
            $this->db->select('subject_code, subject_name, credits');
            $this->db->where(array('semester <=' => $sem, 'department' => $dept));
            $query = $this->db->get('subjects'); //equivalent to FROM clause
        }
        return $query->result();
        //return $query->result_array(); //returns a matrix
    }
}
?>