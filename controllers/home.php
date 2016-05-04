<?php
class Home extends CI_Controller {
	
	public function __construct()
    {
        parent::__construct();
		$this->load->helper(array('url'));
		$this->load->library('session');
    }
    
    public function index() 
    {
        $this->load->view('template/style');
		$this->load->view('template/header');
        $this->load->view('home');
		$this->load->view('template/navigation');
		$this->load->view('template/info');
		$this->load->view('template/footer');
    }
}