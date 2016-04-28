<?php
class user extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form','url'));
        $this->load->library(array('session', 'form_validation', 'email'));
        $this->load->database();
        $this->load->model('user_model');
    }
    
    function index()
    {
        $this->register();
    }

    function register()
    {
        //set validation rules
        $this->form_validation->set_rules('roll_no', 'Roll Number', 'trim|required|alpha|min_length[3]|max_length[30]|xss_clean');
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|matches[cpassword]|md5');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required');
        
        //validate form input
        if ($this->form_validation->run() == FALSE)
        {
            // fails
            $this->load->view('user_registration_view');
        }
        else
        {
            //insert the user registration details into database
            $data = array(
                'roll_no' => $this->input->post('roll_no'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password')
            );
            
            // insert form data into database
            if ($this->user_model->insertUser($data))
            {
                // send email
                if ($this->user_model->sendEmail($this->input->post('email')))
                {
                    // successfully sent mail
                    $this->session->set_flashdata('msg','<div class="alert alert-success text-center">You are Successfully Registered! Please confirm the mail sent to your Email-ID!!!</div>');
                    redirect('user/register');
                }
                else
                {
                    // error
                    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again later!!!</div>');
                    redirect('user/register');
                }
            }
            else
            {
                // error
                $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again later!!!</div>');
                redirect('user/register');
            }
        }
    }
    
    function verify($hash=NULL)
    {
        if ($this->user_model->verifyEmailID($hash))
        {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-success text-center">Your Email Address is successfully verified! Please login to access your account!</div>');
            redirect('user/register');
        }
        else
        {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-danger text-center">Sorry! There is error verifying your Email Address!</div>');
            redirect('user/register');
        }
    }
	
	function login()
	{
		$data = $this->input->post();
		$this->form_validation->set_rules('check_entered', 'Bot check', 'trim|required|numeric|max_length[2]|min_length[1]|xss_clean');
        $this->form_validation->set_rules('uname', 'Roll Number', 'trim|required|numeric|min_length[9]|max_length[13]|xss_clean');
		$this->form_validation->set_rules('pword', 'Password', 'trim|required');
		
		if ($data['check_sum'] == md5($data['check_entered']) && $this->form_validation->run() == TRUE)
		//if ($this->form_validation->run() == TRUE)
		{
			$data = $this->input->post();
			$user_data = $this->user_model->login_query($data['uname'], $data['pword']); //return as $query->row_array()
			if (isset($user_data['error_msg'])
			{
				$this->session->set_flashdata('login_status','Failed to upload to database. Try later');
				redirect('user/login');
			}
			$session_data = array('Name'=>$user_data['name'], 'Roll number']=>$user_data['roll_no']);
			$this->session->set_userdata($session_data);
			$this->load->view('user/home', $user_data); //user_data array has all user info in table 'students'. This can be used in bootstrap generated view
		}
		else
		{
			$check = array('num1'=>rand(0, 9), 'num2'=>rand(0, 9), 'sum'=>md5($check['num1']+$check['num2'])); //to check bot or human
			$this->load->view('user/login' $check); //give the $check['sum'] in the a hidden form with name="check_sum"
		}
	}
	
	function loadUserData()
	{
		if (!isset($this->input->post('first_access')))
		{
			$this->form_validation->set_rules('fname', 'First Name', 'trim|required|alpha|min_length[3]|max_length[30]|xss_clean');
			$this->form_validation->set_rules('lname', 'Last Name', 'trim|required|alpha|min_length[1]|max_length[30]|xss_clean');
			$this->form_validation->set_rules('college', 'College', 'trim|required|alpha|min_length[3]|max_length[30]|xss_clean');
			$this->form_validation->set_rules('year_intake', 'Year of intake', 'trim|required|numeric|exact_length[4]|greater_than[2011]|less_than['. intval(date("Y"))+1 .']|xss_clean');
			//dept, regulation should be given as dropdwon
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('user/load_user_data');
		}
		else
		{
			$load_data = $this->input->post();
			if ($this->user_model->updateUserData(unset($load_data['first_access'])))
			{
				$this->session->set_flashdata('load_status','Successfully updated in database');
				redirect('user/loaduserdata');
			}
			else
			{
				$this->session->set_flashdata('load_status','Failed to upload to database. Try later');
				redirect('user/loaduserdata');
			}
		}
	}
}
?>