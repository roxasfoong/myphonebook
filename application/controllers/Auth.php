<?php
class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function register() {
        // Set validation rules for registration form
        if($this->user_model->load_database()){
        $this->form_validation->set_rules('nickname', 'Nickname', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
            
        if ($this->form_validation->run() === false) {
            // If validation fails, reload the registration form with validation errors
            $this->session->set_flashdata('validation_errors', validation_errors());
            redirect('register');
        } else {
            // Retrieve input data
            $data = array(
                'nickname' => $this->input->post('nickname'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
            );
    
            // Attempt registration
            

            $result = $this->user_model->register($data);
            if ($result) {

                // Registration successful, redirect to login page
                $this->session->set_flashdata('success', 'Registration successful!');
                redirect('login');

            } else {

                // Registration failed due to database error, display error message
                $this->session->set_flashdata('some_errors', 'Error: Registration failed due to a database error.');
                redirect('register');

            }

            }
        }
        else{
            $this->session->set_flashdata('db_errors', 'Unable to communicate with Database...');
            redirect('register');
        }
    }

    public function login() {
        // Set validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === false) {
            // If validation fails, reload the login view
            $this->session->set_flashdata('validation_errors', validation_errors());
            redirect('login');

        } else {
            // Retrieve input data
            $email = $this->input->post('email');
            $password = $this->input->post('password');

           
            // Attempt login
            if($this->user_model->load_database()){
                try {
                    $user = $this->user_model->login($email, $password);
                    if ($user) {
                        // Set user session and redirect to dashboard
                        $this->session->set_userdata('user_id', $user['id']);
                        $this->session->set_userdata('user_nickname', $user['nickname']);
                        redirect('dashboard');
                    } else {
                        // Display error message
                        $this->session->set_flashdata('login_errors', 'Invalid email or password. Please try again.');
                        redirect('login');
                    }
                    }catch (Exception $e) {
                        // Handle database connection errors
                        $this->session->set_flashdata('db_errors', 'Unable to communicate with Database...');
                        redirect('login');
                    }
            } else{
                $this->session->set_flashdata('db_errors', 'Unable to communicate with Database...');
                redirect('login');
            }
           
        }
    }

    public function logout() {
        // Destroy session and redirect to login page
        $this->session->unset_userdata('user_id');
        redirect('login');
    }
}