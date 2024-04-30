<?php
class Contact_model extends CI_Model
{
    public function __construct() {
        $this->load->library('pagination');
    }

    public function load_database()
    {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "myphonebook_db";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            return false;
        } else {

            $this->load->database();
            return true;
        }
    }

    // Function to insert a new contact into the database
    public function insert_contact($data)
    {
         return $this->db->insert('contacts', $data);
    }

    // Function to retrieve all contacts from the database
    public function get_recently_added_contact($user_id) 
    {
        $this->db->where('user_id', $user_id); 
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get('contacts')->row_array();

        unset($result['id']);
        unset($result['user_id']);
        unset($result['created_at']);
        unset($result['updated_at']);

        return $result;
    }

    public function get_image_location($contact_id){

        $this->db->select('image_location')->where('id', $contact_id);
        $image_query = $this->db->get('contacts');
        $image_row = $image_query->row();
        return $image_row->image_location;

    }

    public function get_all_contacts($user_id)
    {
        $result = $this->db->where('user_id', $user_id)->order_by('created_at', 'ASC')->get('contacts')->result_array();

        if (empty($result)) {
            return null;
        }

        foreach ($result as &$contact) {
            unset($contact['id']);
            unset($contact['user_id']);
        }

        return $result;
    }

    public function count_total_row($user_id){

       return $this->db->where('user_id', $user_id)->count_all_results('contacts');

    }

    public function get_total_number_of_page($user_id)
    {
        $total = count($this->db->where('user_id', $user_id)->order_by('created_at', 'ASC')->get('contacts')->result_array());

        if (empty($total) ) {
            return 0;
        }

        $result = ($total % 8 > 0) ? (int)($total / 8) + 1 : (int)($total / 8);

        return $result;
    }



    public function get_all_contacts_with_pagination($user_id)
    {
        $config['base_url'] = site_url('dashboard/index');
        $config['per_page'] = 8;
        $config['num_links'] = 1;
    
        $total_rows = count($this->db->where('user_id', $user_id)->get('contacts')->result_array());
    
        if ($total_rows == 0) {
            return null;
        }
    
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $pageNumber=0;
        $total_pages = (($total_rows % 8) > 0 ) ? (int)($total_rows / 8) + 1 : (int)($total_rows / 8);
        $InputFromUrl = $this->uri->segment(3,0);
        if($InputFromUrl > $total_rows){
            $pageNumber = ($total_pages-1) * 8;
        }elseif ($InputFromUrl % 8 > 0 )
        {
            $pageNumber = floor($InputFromUrl / 8) * 8;

        }elseif($InputFromUrl == $total_rows){
            $pageNumber = (($InputFromUrl/8)-1) * 8;
        }else{
            $pageNumber = $InputFromUrl;
        }
        

    
        $result = $this->db
            ->where('user_id', $user_id)
            ->order_by('created_at', 'ASC')
            ->limit(8,$pageNumber)
            ->get('contacts')
            ->result_array();
    
        foreach ($result as &$contact) {
            unset($contact['id']);
            unset($contact['user_id']);
        }
    
        return $result;
    }

    public function generatePagination($totalPages, $currentPage) {
        $totalPages = intval($totalPages);
        $currentPage = intval($currentPage);
    
        $paginationHTML = '';
    
        if ($totalPages > 1) {
            if ($currentPage > 1) {
                $paginationHTML .= '<button onclick="gotoPage(' . ($currentPage - 1) . ')">&lt;</button>';
            }
            if ($currentPage > 1) {
                $paginationHTML .= '<button onclick="gotoPage(' . ($currentPage - 1) . ')">' . ($currentPage - 1) . '</button>';
            }
            $paginationHTML .= '<strong>' . $currentPage . '</strong>';
            if ($currentPage < $totalPages) {
                $paginationHTML .= '<button onclick="gotoPage(' . ($currentPage + 1) . ')">' . ($currentPage + 1) . '</button>';
            }
            if ($currentPage < $totalPages) {
                $paginationHTML .= '<button onclick="gotoPage(' . ($currentPage + 1) . ')">&gt;</button>';
            }
        }
    
        return $paginationHTML;
    }

    public function count_max_page($user_id)
    {

        $total_rows = $this->count_total_row($user_id);

        if ($this->count_total_row($user_id) == 0) {
            return 1;
        }

        if ($total_rows < 8) {
            return 1;
        } else {

            return ($total_rows % 8) > 0 ? (int)($total_rows / 8) + 1 : $total_rows / 8;
        }
    }

    public function compare_page_number($user_id,$current_page)
    {
        $newPage = 0;

        if($current_page > $this->count_max_page($user_id)){
            $newPage = $this->count_max_page($user_id);
        }else{
            $newPage = $current_page;
        }

        return $newPage;
    }

    public function get_all_contacts_with_offset($user_id, $current_page)
    {
        
        $total_rows = count($this->db->where('user_id', $user_id)->get('contacts')->result_array());
     
        if ($total_rows == 0) {
            return null;
        }

        $max_page_number=0;

        if($total_rows < 8){

            $max_page_number=1;

        }else{

            $max_page_number = ($total_rows % 8 ) > 0 ? (int)($total_rows/8) + 1 : $total_rows/8;

        }

        $start_from = 0;

        switch($current_page)
        {
            case 0 :
                $start_from = 0;
                break;
            case 1 :
                $start_from = 0;
                break;
            case 2 : 
                $start_from = 8;
                break;
            default:
            $start_from = ($current_page-1) * 8;
            break;
        }

        if($start_from >= $total_rows){
            $start_from = ($max_page_number-1) * 8;
        }

        $result = $this->db
            ->where('user_id', $user_id)
            ->order_by('created_at', 'ASC')
            ->limit(8, $start_from)
            ->get('contacts')
            ->result_array();

        foreach ($result as &$contact) {
            unset($contact['id']);
            unset($contact['user_id']);
        }

        return $result;
    }

    public function get_contact_by_id($contact_id)
    {
        return $this->db->get_where('contacts', array('id' => $contact_id))->row_array();
    }

    public function update_contact($contact_id, $data)
    {
        $this->db->select('image_location')->where('id', $contact_id);
        $image_query = $this->db->get('contacts');
        $image_row = $image_query->row();
        $image_location = $image_row->image_location; 

        $this->db->where('id', $contact_id);
        $result = $this->db->update('contacts', $data);

        if($image_location !== $data['image_location'] && ($image_location !== '/assets/img/empty-profile-picture.webp')){
            unlink(FCPATH.$image_location);
        }

        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    // Function to delete a contact from the database
    public function delete_contact_by_id($contact_id)
    {
        $this->db->where('id', $contact_id);
        return $this->db->delete('contacts');
    }

    public function delete_contact_by_phone_with_user_id($phone_number,$user_id)
    {
        $this->db->select('image_location')->where('user_id', $user_id)->where('phone_number',$phone_number);
        $image_query = $this->db->get('contacts');
        $image_row = $image_query->row();
        $image_location = $image_row->image_location;

        $this->db->where('user_id', $user_id)->where('phone_number',$phone_number);
        $this->db->delete('contacts');

        if ($this->db->affected_rows() > 0) {

            if($image_location !== '/assets/img/empty-profile-picture.webp'){
            unlink(FCPATH . $image_location);
            }

            return TRUE;

        } else {
           
            return FALSE;
        }
    }

    public function get_contact_by_phone_with_user_id($phone_number, $user_id)
    {

        $this->db->where('phone_number', $phone_number)
            ->where('user_id', $user_id)
            ->limit(1);

        $result = $this->db->get('contacts')->row_array();

        // Remove unwanted fields from the result
        unset($result['id']);
        unset($result['user_id']);
        unset($result['created_at']);
        unset($result['updated_at']);

        return $result;
    }

    public function get_contact_id($phone_number, $user_id)
    {
        $this->db->select('id')
        ->where('user_id', $user_id)
        ->where('phone_number', $phone_number)
        ->limit(1);

        $query = $this->db->get('contacts');

        if ($query->num_rows() > 0) {

            $result = $query->row_array();
            return $result['id'];

        }
        else
        {

            return null;

        }

    }

}
