<?php
class user_model extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    //insert into user table
    function insertUser($data)
    {
        return $this->db->insert('students', $data);
    }
    
    //send verification email to user's email id
    function sendEmail($to_email)
    {
        $from_email = 'mrxavir@gmail.com'; //change this to yours
        $subject = 'Verify Your Email Address';
        $message = 'Dear User,<br /><br />Please click on the below activation link to verify your email address.<br /><br /> '.base_url().'user/verify/' . md5($to_email) . '<br /><br /><br />Thanks<br />Studentportal Team';
        
        //configure email settings
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com'; //smtp host name
        $config['smtp_port'] = '465'; //smtp port number
        $config['smtp_user'] = $from_email;
        $config['smtp_pass'] = 'pornforever'; //$from_email password
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
        $data = array('acc_status' => 1);
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
		$this->dp->where('roll_no', $roll_no);
		return $this->db->update('students', $new_data);
	}
}
?>