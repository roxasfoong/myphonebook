<?php
class Contact_model extends CI_Model
{
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

    public function get_all_contacts()
    {
        return $this->db->get('contacts')->result_array();
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

        if($image_location !== $data['image_location']){
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
