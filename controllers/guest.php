<?php
class Guest extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('gpa_model');
		$this->load->helper(array('form', 'url'));
    }
    
    public function index() 
    {
        $this->load->view('guest/index');
    }
    
    public function calculator($type)
    {
        //type is 'gpa' or 'cgpa'
        $data['dept'] = 'Choose your department';
        $data['sem_gpa'] = 'Choose the semester for finding the GPA';
        $data['sem_cgpa'] = 'Choose the end semester for finding CGPA';
		$data['type'] = $type;
        
        //have to load header template here
        $this->load->view('guest/'.$type, $data);
        //have to load footer template here 
    }
    
    public function table($type)
    {
        //type is 'gpa' or 'cgpa', pass the $type parameter through url
        $sem = $this->input->post('sem');
        $dept = $this->input->post('dept');
        
        $sub_rows = $this->gpa_model->get_table($sem, $dept, $type); //this should retrive list of subjects form the database
        $this->load->library('table');
        $this->table->set_heading('Subject code', 'Subject name', 'Credits', 'Grade(enter them)');
        $x = 0;
        foreach ($sub_rows as $row)
        {
            $x = $x + 1;
            $grade_form = "<input type='text' name='grade_".$x."'><input type='hidden' name='credit_".$x."' value='".$row->credits."'>";
            //<form></form> to be included in views
            $this->table->add_row($row->subject_code, $row->subject_name, $row->credits, $grade_form);
        }
        $data['sub_table'] = $this->table->generate();
        $data['title'] = 'List of subjects.';
        $data['type'] = $type;
        $this->load->view('guest/table', $data); //table.php is common for gpa and cgpa
        //use a hidden form in table.php which can store the credit list so that it can
        //be used for gpa calculation.
    }
    
    public function result()
    {
        $grade_eq = array('s'=>10, 'a'=>9, 'b'=>8, 'c'=>7, 'd'=>6, 'e'=>5, 'u'=>0);
		$all_values = $this->input->post(); //have to add XSS filter later
        //the all_values array will contain the grades entered in previous page *and*
        //the credits to be stored in hidden form in name=>value format
        $arr_size = sizeof($all_values);
        $sum_nr = 0;
        $sum_dr = 0;
        for ($i = 1; $i <= ($arr_size - 1) / 2; $i++)
        {
            $sum_nr = $sum_nr + ($all_values['credit_'.$i] * $grade_eq[strtolower($all_values['grade_'.$i])]);
            $sum_dr = $sum_dr + $all_values['credit_'.$i];
        }
        $data['result'] = $sum_nr / $sum_dr;
        $data['type'] = $all_values['type'];
        $this->load->view('guest/result', $data);
    }
    
}
?>