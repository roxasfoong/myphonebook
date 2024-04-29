<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

        public function __construct()
        {
                parent::__construct();
                $this->load->model('contact_model');
        }

        public function index()
        {
                if (!$this->session->has_userdata('user_id')) {
                        redirect('login');
                }
                $data['title'] = 'Dashboard Page';
                $data['contacts_view'] = 'contacts_view';
                $data['header_view'] = 'header_view';
                $data['recently_added_view'] = 'recently_added_view';
                $data['utility_view'] = 'utility_view';
                if ($this->contact_model->load_database()) {
                        $data['recently_added_data'] = $this->contact_model->get_recently_added_contact($this->session->has_userdata('user_id'));

                        if(!$data['recently_added_data'])
                        {
                                $data['recently_added_data'] = array(
                                        'name' => 1,
                                        'error' => 'You Have Not Added Any Contact',
                                    );
                        }

                } else {
                        $data['recently_added_data'] = array(
                                'name' => 1,
                                'error' => 'Unable to Communicate with Database...',
                            );
                        
                }

                $this->load->view('/layouts/dashboard_layout', $data);
        }
}
