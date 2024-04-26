<?php
class User_model extends CI_Model {
    public function __construct() {
        $this->load_database();
    }

    public function load_database() {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "myphonebook_db";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            return false;
        }
        else{

        $this->load->database();
        return true;
        
        }
    }

    public function register($data) {
        return $this->db->insert('users', $data);
    }

    public function login($email, $password) {
        $user = $this->db->get_where('users', ['email' => $email])->row_array();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}