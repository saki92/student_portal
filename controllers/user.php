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
        $this->form_validation->set_rules('roll_no', 'Roll Number', 'trim|required');
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email|is_unique[students.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|matches[cpassword]|md5');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required');
        
        //validate form input
        if ($this->form_validation->run() == FALSE)
        {
            // fails
			$this->load->view('template/style');
			$this->load->view('template/header');
            $this->load->view('user/user_registration_view');
			$this->load->view('template/navigation');
			$this->load->view('template/info');
			$this->load->view('template/footer');
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
				$to_email = $this->input->post('email');
				$mess = 'Dear User,<br /><br />Please click on the below activation link to verify your email address.<br /><br /> '.base_url().'user/verify/' . md5($to_email) . '<br /><br /><br />Thanks<br />Studentportal Team';
                if ($this->user_model->sendEmail($to_email, 'Verify Your Email Address', $mess))
                {
                    // successfully sent mail
                    $this->session->set_flashdata('msg','<div class="alert alert-success text-center">You are Successfully Registered! Please confirm the mail sent to your Email-ID!!!</div>');
                    redirect('user/message');
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
            redirect('user/message');
        }
        else
        {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-danger text-center">Sorry! There is an error verifying your Email Address!</div>');
            redirect('user/register/message');
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
					$session_data = array('Department'=>$user_data['department'], 'Roll number'=>$user_data['roll_no']);
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
				$this->load->view('template/style');
				$this->load->view('template/header');
				$this->load->view('user/login', $check); //give the $check['sum'] in the a hidden form with name="check_sum"
				$this->load->view('template/navigation');
				$this->load->view('template/info');
				$this->load->view('template/footer');
			}
		}
	}
	
	function forgot_password()
	{
		if (!empty($this->session->userdata('Roll number')))
		{
			redirect('user/home');
		}
		else
		{
			$data = $this->input->post();
			$this->form_validation->set_rules('email', 'Email ID', 'trim|required|callback_email_check');
			
			if (!empty($data))
			{
				if ($this->form_validation->run() == FALSE)
				{
					goto page_load;
				}
				elseif ($data['check_sum'] == md5($data['check_entered']))
				{
					$to_email = $this->input->post('email');
					$mess = 'Dear User,<br /><br />Please click on the below link to reset your password.<br /><br /> '.base_url().'user/reset_password/' . md5($to_email) . '<br /><br /><br />Thanks<br />Studentportal Team';
					if ($this->user_model->sendEmail($to_email, 'Reset Password', $mess))
					{
						// successfully sent mail
						$this->session->set_flashdata('msg','<div class="alert alert-success text-center">Follow the link sent to your mail ID !</div>');
						redirect('user/message');
					}
					else
					{
						// error
						$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again later!!!</div>');
						redirect('user/forgot_password');
					}
				}
			}
			else page_load:
			{
				$num1 = rand(0, 9);
				$num2 = rand(0, 9);
				$check = array('num1'=>$num1, 'num2'=>$num2, 'sum'=>md5($num1+$num2)); //to check bot or human
				$this->load->view('template/style');
				$this->load->view('template/header');
				$this->load->view('user/forgot_password', $check); //give the $check['sum'] in the a hidden form with name="check_sum"
				$this->load->view('template/navigation');
				$this->load->view('template/info');
				$this->load->view('template/footer');
			}
		}
	}
	
	function email_check($email)
	{
		$query = $this->user_model->email_exists($email);
		if ($query->num_rows() > 0)
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('email_check', 'Email ID not registered with us !');
			return false;
		}
	}
	
	function reset_password($hash = NULL)
	{
		$this->form_validation->set_rules('new_password', 'Password', 'trim|required|matches[cpassword]|md5');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required');
		
		if (!empty($this->input->post()) && !empty($this->session->userdata('email_r_pass')))
		{
			if ($this->form_validation->run() == TRUE)
			{
				if ($this->user_model->changePassword($this->session->userdata('email_r_pass'), $this->input->post()))
				{
					$this->session->set_flashdata('verify_msg','<div class="alert alert-success text-center">Your password is changed successfully! Please login to access your account!</div>');
					$this->session->unset_userdata('email_r_pass');
					redirect('user/message');
				}
				else
				{
					$this->session->set_flashdata('verify_msg','<div class="alert alert-danger text-center">Sorry! There is an error in changing your password!</div>');
					redirect('user/message');
				}
			}
			else
			{
				$this->load->view('template/style');
				$this->load->view('template/header');
				$this->load->view('user/reset_password');
				$this->load->view('template/navigation');
				$this->load->view('template/info');
				$this->load->view('template/footer');;
			}
		}
		else
		{
			$session_data = array('email_r_pass'=>$hash);
			$this->session->set_userdata($session_data);
			$this->load->view('template/style');
			$this->load->view('template/header');
			$this->load->view('user/reset_password');
			$this->load->view('template/navigation');
			$this->load->view('template/info');
			$this->load->view('template/footer');
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
			$this->form_validation->set_rules('college', 'College', 'trim|required');
			$this->form_validation->set_rules('start_year', 'Year of intake', 'trim|required|numeric|exact_length[4]|greater_than[2011]|callback_year_check');
			//dept, regulation should be given as dropdwon
			
			if ($this->form_validation->run() == FALSE || empty($this->input->post()))
			{
				$exist_data = $this->user_model->retUserData($this->session->userdata('Roll number'));
				$this->load->view('template/style');
				$this->load->view('template/header');
				$this->load->view('user/user_load_data', $exist_data); //load same page
				$this->load->view('template/navigation');
				$this->load->view('template/info');
				$this->load->view('template/footer');
			}
			else
			{
				$load_data = $this->input->post();
				if ($this->user_model->updateUserData($load_data, $this->session->userdata('Roll number')))
				{
					$this->session->set_userdata('Department', $load_data['department']);
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
			$this->load->view('template/style');
			$this->load->view('template/header');
			$this->load->view('user/user_home');
			$this->load->view('template/navigation');
			$this->load->view('template/info');
			$this->load->view('template/footer'); 
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
	
	function updateMarksList()
	{
		if (empty($this->session->userdata('Roll number')))
		{
			redirect('user/login');
		}
		else
		{
			if (empty($this->input->post()))
			{
				$mark_list = $this->user_model->retMarkTable($this->session->userdata('Roll number'), $this->session->userdata('Department'));
				$data['data'] = $mark_list;
				//display the marks up to current sem in table format, with GPA below each table and CGPA at the bottom of page
				$this->load->view('template/style');
				$this->load->view('template/header');
				$this->load->view('user/update_marks', $data);
				$this->load->view('template/navigation');
				$this->load->view('template/info');
				$this->load->view('template/footer');
			}
			else
			{
				if ($this->user_model->updateMarkTable($this->session->userdata('Roll number'), $this->session->userdata('Department'), $this->input->post()))
				{
					$this->session->set_flashdata('load_status','Successfully updated');
					redirect('user/updateMarksList');
				}
				else
				{
					$this->session->set_flashdata('load_status','Failed to update in database');
					redirect('user/updateMarksList');
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
	
	function message()
	{
		$this->load->view('template/style');
		$this->load->view('template/header');
		$this->load->view('user/messages');
		$this->load->view('template/navigation');
		$this->load->view('template/info');
		$this->load->view('template/footer');
	}
}
?>