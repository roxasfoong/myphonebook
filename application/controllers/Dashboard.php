<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller{

	public function index()
	{
        
        $data['title'] = 'Dashboard Page';
        $data['contacts_view'] = 'contacts_view';
        $data['header_view'] = 'header_view';
        $data['recently_added_view'] = 'recently_added_view';
        $data['utility_view'] = 'utility_view';
        $this->load->view('/layouts/dashboard_layout', $data);

	}


}