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
        $this->form_validation->set_rules('roll_no', 'Roll Number', 'trim|required|numeric|min_length[3]|max_length[30]');
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email');//|is_unique[students.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|matches[cpassword]|md5');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required');
        
        //validate form input
        if ($this->form_validation->run() == FALSE)
        {
            // fails
            $this->load->view('user/user_registration_view');
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
		if (!empty($this->session->userdata('Roll number')))
		{
			redirect('user/home');
		}
		else
		{
			$data = $this->input->post();
			$this->form_validation->set_rules('check_entered', 'Entered Sum', 'trim|required|numeric');
			$this->form_validation->set_rules('uname', 'Roll Number', 'trim|required|numeric');
			$this->form_validation->set_rules('pword', 'Password', 'trim|required');
			
			if (!empty($data))
			//if ($this->form_validation->run() == TRUE)
			{
				if ($this->form_validation->run() == FALSE)
				{
					goto page_load;
				}
				elseif ($data['check_sum'] == md5($data['check_entered']))
				{

					$data = $this->input->post();
					$user_data = $this->user_model->loginQuery($data['uname'], $data['pword']); //return as $query->row_array()
					if (isset($user_data['error_msg']))
					{
						$this->session->set_flashdata('login_status','Username and Password did not match');
						redirect('user/login');
					}
					$session_data = array('Name'=>$user_data['name'], 'Roll number'=>$user_data['roll_no']);
					$this->session->set_userdata($session_data);
					if (empty($user_data['name']))
					{
						redirect('user/loaduserdata');
					}
					else
					{
						redirect('user/home');
					}
				}
				elseif ($data['check_sum'] != md5($data['check_entered']))
				{
					$this->session->set_flashdata('login_status','Incorrect addition');
					redirect('user/login');
				}
			}
			else page_load:
			{
				$num1 = rand(0, 9);
				$num2 = rand(0, 9);
				$check = array('num1'=>$num1, 'num2'=>$num2, 'sum'=>md5($num1+$num2)); //to check bot or human
				$this->load->view('user/login', $check); //give the $check['sum'] in the a hidden form with name="check_sum"
			}
		}
		
	}
	
	function loadUserData()
	{
		if (empty($this->session->userdata('Roll number')))
		{
			redirect('user/login');
		}
		else
		{
			$this->form_validation->set_rules('name', 'Name', 'trim|required|alpha|min_length[3]|max_length[30]');
			$this->form_validation->set_rules('department', 'Department', 'trim|required|alpha|min_length[1]|max_length[30]');
			$this->form_validation->set_rules('start_year', 'Year of intake', 'trim|required|numeric|exact_length[4]|greater_than[2011]|callback_year_check');
			//dept, regulation should be given as dropdwon
			
			if ($this->form_validation->run() == FALSE || empty($this->input->post()))
			{
				$exist_data = $this->user_model->retUserData($this->session->userdata('Roll number'));
				$this->load->view('user/user_load_data', $exist_data); //load same page
			}
			else
			{
				$load_data = $this->input->post();
				if ($this->user_model->updateUserData($load_data, $this->session->userdata('Roll number')))
				{
					$this->session->set_flashdata('load_status','Successfully updated in database');
					redirect('user/home');
				}
				else
				{
					$this->session->set_flashdata('load_status','Failed to upload to database. Try later');
					redirect('user/loaduserdata');
				}
			}
		}
	}
	
	function home()
	{
		if (empty($this->session->userdata('Roll number')))
		{
			redirect('user/login');
		}
		else
		{
			echo "hi user this is your home page"; 
			//retrive user data from DB and display them | college, roll_no, dept, GPA graph, current GPA, current CGPA and "link to calculate GPA, CGPA or update marks DB"
		}
	}
	
	function logout()
	{
		if (!empty($this->session->userdata('Roll number')))
		{
			$this->session->unset_userdata('Roll number');
			redirect('user/login');
		}
		else
		{
			redirect('user/login');
		}
	}
	
	function updateMarksDb()
	{
		if (empty($this->session->userdata('Roll number')))
		{
			redirect('user/login');
		}
		else
		{
			if (empty($this->input->post()))
			{
				$mark_list = $this->user_model->ret_mark_table($this->session->userdata('Roll number'));
				//display the marks up to current sem in table format, with GPA below each table and CGPA at the bottom of page
				$this->load->view('user/update_marks');
			}
			else
			{
				if ($this->user_model->update_mark_table($this->session->userdata('Roll number'), $this->input->post()))
				{
					redirect('user/updatemarksdb');
				}
				else
				{
					$this->session->set_flashdata('load_status','Failed to update in database');
					redirect('user/updatemarksdb');
				}
			}
		}
	}
	
	function year_check($a)
	{
		$b = intval(date("Y"));
		if ($a <= $b)
		{
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('year_check', 'Do you have a time-machine ?');
			return FALSE;
		}
	}
}
?>