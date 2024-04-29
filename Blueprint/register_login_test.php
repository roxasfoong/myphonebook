<?php
// Load the User_model
$CI =& get_instance();
$CI->load->model('user_model');

// Example usage: register a user
$data = array(
    'nickname' => 'JohnDoe',
    'email' => 'john@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT)
);
$CI->user_model->register($data);

// Example usage: login
$email = 'john@example.com';
$password = 'password123';
$user = $CI->user_model->login($email, $password);
if ($user) {
    echo 'Login successful!';
    print_r($user);
} else {
    echo 'Login failed!';
}
?>