<?php
class user_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->database();
    }
    
    //insert into user table
    function insertUser($data)
    {
        return $this->db->insert('students', $data);
    }
    
    //send verification email to user's email id
    function sendEmail($to_email)
    {
        $from_email = 'me@sakthive.in'; //change this to yours
        $subject = 'Verify Your Email Address';
        $message = 'Dear User,<br /><br />Please click on the below activation link to verify your email address.<br /><br /> '.base_url().'user/verify/' . md5($to_email) . '<br /><br /><br />Thanks<br />Studentportal Team';
        
        //configure email settings
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://mail.sakthive.in'; //smtp host name
        $config['smtp_port'] = '465'; //smtp port number
        $config['smtp_user'] = $from_email;
        $config['smtp_pass'] = 'handypandy1A'; //$from_email password
        $config['mailtype'] = 'html';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['newline'] = "\r\n"; //use double quotes
        $this->email->initialize($config);
        
        //send mail
        $this->email->from($from_email, 'Studentportal');
        $this->email->to($to_email);
        $this->email->subject($subject);
        $this->email->message($message);
        return $this->email->send();
    }
    
    //activate user account
    function verifyEmailID($key)
    {
        $data = array('acc_status' => 1); //set acc_status as 1 if email matches
        $this->db->where('md5(email)', $key);
        return $this->db->update('students', $data);
    }
	
	function loginQuery($uname, $pword)
	{
		$query = $this->db->get_where('students', array('roll_no' => $uname, 'password' => md5($pword), 'acc_status' => 1));
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		elseif ($query->num_rows() == 0)
		{
			return array('error_msg'=>"Roll number and Password didn't match");
		}
	}
	
	function retUserData($roll_no)
	{
		$query = $this->db->get_where('students', array('roll_no' => $roll_no));
		return $query->row_array();
	}
	
	function updateUserData($new_data, $roll_no)
	{
		//////////retreiving department from student table//////////
		$this->db->select('department');
		$old_dept = $this->db->get_where('students', array('roll_no' => $roll_no))->row_array();
		//////////retreiving department from student table//////////
		$this->db->where('roll_no', $roll_no);
		$a = $this->db->update('students', $new_data); //updating students table with new data
		if ($old_dept['department'] == $new_data['department']) //checking if user is changing department
		{
			return $a;
		}
		else //if user changes department,
		{
			$c = $this->db->delete($old_dept['department'], array('roll_no' => $roll_no)); //delete his record from old department
			$b = $this->db->insert($new_data['department'], array('roll_no' => $roll_no)); //insert a empty record into new department table
			return $a && $b && $c;
		}
	}
	
	function retMarkTable($roll_no, $dept)
	{
		//////////retreiving subject list//////////
		$this->db->select('subject_code, subject_name, credits, semester');
        $this->db->where(array('department' => $dept, 'elective' => 0));
		$query = $this->db->get('subjects');
		$sub_list = $query->result();
		//////////retreiving subject list//////////
		//////////retreiving grades for all subjects//////////
		$query = $this->db->get_where($dept, array('roll_no' => $roll_no));
		$sub_grade = $query->row_array();
		$query = $this->db->get_where('students', array('roll_no' => $roll_no));
		$gpa_arr = $query->row_array();
		//////////retreiving grades for all subjects//////////
		//////////counting no of semesters in this course//////////
		$this->db->select('semester');
		$this->db->distinct();
		$query = $this->db->get('subjects');
		$sem_count = count($query->row_array());
		//////////counting no of semesters in this course//////////
		//////////constructing table//////////
		$i = 1;
		while ($i <= $sem_count) //creating table instances equal to no of semesters
		{
			$tab_name = 'table_'.$i;
			$this->load->library('table', '', $tab_name);
			$this->$tab_name->set_heading('Subject code', 'Subject name', 'Credits', 'Grade(enter them)');
			$i++;
		}
		foreach ($sub_list as $row) //loading each table with data and input form
		{
			$temp_sem = $row->semester;
			$tab_name = 'table_'.$temp_sem;
            $grade_form = "<input type='text' name='".$row->subject_code."' required pattern='[sabcdeuSABCDEU]{1}' value='".$sub_grade[$row->subject_code]."'>";
            //<form></form> to be included in views
            $this->$tab_name->add_row($row->subject_code, $row->subject_name, $row->credits, $grade_form);
		}
		$i = 1;
		while ($i <= $sem_count) //generating all tables and appending to array
		{
			$tab_name = 'table_'.$i;
			$tables['table'.$i] = $this->$tab_name->generate();
			$tables['gpa_'.$i] = $gpa_arr['gpa_'.$i];
			$i++;
		}
		$tables['cgpa'] = $gpa_arr['cgpa'];
		//////////constructing table//////////
		return $tables;
	}
	
	function updateMarkTable($roll_no, $dept, $grades)
	{
		//////////getting credits for calculating GPA and CGPA//////////
		$this->db->select('subject_code, credits, semester');
		$this->db->where(array('department' => $dept, 'elective' => 0));
		$query = $this->db->get('subjects');
		$credits_list = $query->result_array();
		//////////getting credits for calculating GPA and CGPA//////////
		//////////calculating GPA for all semesters//////////
		$grade_eq = array('s'=>10, 'a'=>9, 'b'=>8, 'c'=>7, 'd'=>6, 'e'=>5, 'u'=>0);
		$sum_nr = array_fill(1, 12, 0);
        $sum_dr = array_fill(1, 12, 0);
		$temp_credit = array_fill(1, 12, 0);
		foreach ($grades as $key => $value)
		{
			if (strtolower($value == 'u')) { continue; }
			foreach ($credits_list as $row)
			{
				if ($row['subject_code'] == $key)
				{
					$sem = $row['semester'];
					$temp_credit[$sem] = $row['credits'];
					break;
				}
			}
            $sum_nr[$sem] = $sum_nr[$sem] + ($temp_credit[$sem] * $grade_eq[strtolower($value)]);
            $sum_dr[$sem] = $sum_dr[$sem] + $temp_credit[$sem];
		}
		foreach ($sum_nr as $key => $value)
		{
			$new_data['gpa_'.$key] = $value / $sum_dr[$key];
		}
		//////////calculating GPA for all semesters//////////
		$new_data['cgpa'] = array_sum($sum_nr) / array_sum($sum_dr); //calculating CGPA
		//////////updating database//////////
		$this->db->where('roll_no', $roll_no);
		$a = $this->db->update('students', $new_data); //updating students table with GPA and CGPA
		$this->db->where('roll_no', $roll_no);
		$b = $this->db->update($dept, $grades); //updating department table with grades
		//////////updating database//////////
		return $a && $b;
	}
}
?>