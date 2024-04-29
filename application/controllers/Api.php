<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('contact_model');
        $this->load->helper('string');
    }

    public function c_seeder($number)
    {
        if (!$this->session->has_userdata('user_id')) {
            redirect('login');
        }

        $number = intval($number);
        if ($this->contact_model->load_database()) {
            for ($x = 0; $x < $number; $x++) {

                // Example usage: register a user
                $data = array(
                    'user_id' => $this->session->has_userdata('user_id'),
                    'name' => random_string('alpha', 10),
                    'address' => random_string('alnum', 50),
                    'email' => random_string('alnum', 20),
                    'phone_number' => random_string('numeric', 20),
                    'image_location' => '/assets/img/empty-profile-picture.webp',
                    'remark' => random_string('alpha', 30)
                );


                if ($this->contact_model->insert_contact($data)) {
                    echo "Successfully Insert to Database <br>";
                } else {
                    echo "Fail to Insert to Database <br>";
                }
            }
        }
    }

    public function add_contact()
    {
        if (!$this->session->has_userdata('user_id')) {
            $this->session->set_flashdata('error', 'Session Expired. Please Login Again');
            redirect('login');
        }

        if ($this->contact_model->load_database()) {
            $unsupportedMessage ='';
            if ($this->input->is_ajax_request()) {
                $this->form_validation->set_rules('name', 'Name', 'trim|required');
                $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
                $this->form_validation->set_rules('address', 'Address', 'trim');
                $this->form_validation->set_rules('phone_number', 'Phone', 'numeric|trim|required');
                $this->form_validation->set_rules('remark', 'Remark', 'trim');
                $this->form_validation->set_rules('image_location', 'Image', 'callback_validate_image_size');

                if ($this->form_validation->run() === false) {
                    $response['status'] = 'error';
                    $response['message'] = validation_errors();
                    echo json_encode($response);
                } else {
                   
                    $raw_data = $this->input->post();
                    $raw_data['user_id'] = $this->session->has_userdata('user_id');
                    
                    if (!empty($_FILES['image_location']['name'])) {

                        $image_location = $_FILES['image_location']['tmp_name'];
                        $image_type = $_FILES['image_location']['type'];


                        if (file_exists($image_location)) {
                            // Get the filesize in bytes
                            //$filesize = filesize($image_location);

                            // Convert filesize to a human-readable format (e.g., KB or MB)
                            //$filesize_kb = $filesize / 1024;
                        
                          
                                
                                $random_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.webp';
                                $img_path = '/assets/contact_image/' .$random_filename;
                                $file_path = FCPATH . $img_path;


                                switch ($image_type) {
                                    case 'image/jpeg':
                                        $original_image = imagecreatefromjpeg($image_location);
                                        break;
                                    case 'image/png':
                                        $original_image = imagecreatefrompng($image_location);
                                        break;
                                    case 'image/gif':
                                        $original_image = imagecreatefromgif($image_location);
                                        break;
                                    case 'image/bmp':
                                        $original_image = imagecreatefrombmp($image_location); // Custom function to create image from BMP (not directly supported by GD)
                                        break;
                                    case 'image/xbm':
                                        $original_image = imagecreatefromxbm($image_location); // Create image from XBM
                                        break;
                                    case 'image/xpm':
                                        $original_image = imagecreatefromxpm($image_location); // Create image from XPM
                                        break;
                                    case 'image/webp':
                                        $original_image = imagecreatefromwebp($image_location); // Create image from WebP
                                        break;
                                    case 'image/tiff':
                                    case 'image/tif':
                                        $original_image = imagecreatefromtiff($image_location); // Create image from TIFF
                                        break;
                                    case 'image/pcx':
                                        $original_image = imagecreatefrompcx($image_location); // Create image from PCX
                                        break;
                                    case 'image/vnd.wap.wbmp':
                                        $original_image = imagecreatefromwbmp($image_location); // Create image from WBMP
                                        break;
                                    case 'image/ico':
                                    case 'image/icon':
                                        $original_image = imagecreatefromico($image_location); // Create image from ICO
                                        break;
                                   
                                    default:
                                        $raw_data['image_location'] = '/assets/img/empty-profile-picture.webp';
                                        $unsupportedMessage = 'File Type: ' .  $image_type . 'is not supported.';
                                        break;
                                }

                                $new_width = 200;
                                $new_height = 250;
                                $resized_image = imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($resized_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, imagesx($original_image), imagesy($original_image));
                                $quality = 10;
                                imagewebp($resized_image, $file_path, $quality);
                                imagedestroy($original_image);
                                imagedestroy($resized_image);

                            $raw_data['image_location'] = $img_path;

                        } else {

                            $raw_data['image_location'] = '/assets/img/empty-profile-picture.webp';

                        }
                        
                    } 
                    else 
                    {
                        $raw_data['image_location'] = '/assets/img/empty-profile-picture.webp';
                    }

                    $sanitized_data = $this->security->xss_clean($raw_data);


                    // Call the model method to insert the contact into the database
                    try {
                        $result = $this->contact_model->insert_contact($sanitized_data);

                        // Check if the contact was successfully inserted
                        if ($result) {
                            // Send success response
                            $response['status'] = 'success';
                            $response['message'] = 'Contact added successfully. <br>'. $unsupportedMessage;
                        } else {
                            // Send error response
                            $response['status'] = 'error';
                            $response['message'] = 'Unable to add due to duplicated phone number : ' . $sanitized_data['phone_number'];
                        }
                        echo json_encode($response);
                    } catch (Exception $e) {

                        echo json_encode($response);
                        // Check if the error is due to duplicate entry
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            // Handle duplicate entry error
                            http_response_code(400); // or any other appropriate code

                            // Prepare the error response
                            $response = array(
                                'status' => 'error',
                                'message' => 'Unable to add due to some error',
                            );

                            echo json_encode($response);
                        } else {
                            // Handle other types of errors
                            // Set the HTTP response code
                            http_response_code(400); // or any other appropriate code

                            // Prepare the error response
                            $response = array(
                                'status' => 'error',
                                'message' => 'Unable to add due to some error',
                            );

                            echo json_encode($response);
                        }
                    }
                }
            } else {
                // If it's not an AJAX request, show an error message
                $this->session->set_flashdata('error', 'Direct Access Not Allowed.');
                redirect('dashboard');
            }
        } else {
            $this->session->set_flashdata('error', 'Unable to Communicate with Database...');
            redirect('dashboard');
        }
    }

    public function validate_image($image)
    {
        $emptyImage = false;
        if (empty($_FILES['image_location']['name'])) {
            $this->form_validation->set_message('validate_image', 'Please select an image to upload.');
            $emptyImage = TRUE;
        }

        if ($emptyImage) {
            return TRUE;
        } else {
            // Check if the file is an image
            if (!preg_match('/^image\//', $_FILES['image_location']['type'])) {
                $this->form_validation->set_message('validate_image', 'The uploaded file is not an image.');
                return FALSE;
            }
            return TRUE;
        }
    }

    public function validate_image_size($image_location)
{
    if (empty($_FILES['image_location']['name'])) {
        return true;
    }
    $uploaded_file_info = $_FILES['image_location'];
    $file_size = $uploaded_file_info['size']; 

    $max_size_bytes = 10 * 1024 * 1024; // 1 MB


    if (!preg_match('/^image\//', $_FILES['image_location']['type'])) {
        $this->form_validation->set_message('validate_image_size', 'The uploaded file is not an image.');
        return FALSE;
    }

    if ($file_size > $max_size_bytes) {
        $this->form_validation->set_message('validate_image_size', 'The {field} size must not exceed 10 MB.');
        return false;
    }

    return true;
}

    public function get_last_contact()
    {
        if (!$this->session->has_userdata('user_id')) {
            $this->session->set_flashdata('error', 'Session Expired. Please Login Again');
            redirect('login');
        }

        if ($this->contact_model->load_database()) {

            if ($this->input->is_ajax_request()) 
            {

                $result = $this->contact_model->get_recently_added_contact($this->session->has_userdata('user_id'));

                if (!empty($result)) {
                   
                    $response = array(
                        'status' => 'success',
                        'message' => 'Successfully Retrieved From Database',
                        'data' => $result
                    );
                    

                } else {

                    $response = array(
                        'status' => 'error',
                        'message' => 'You Have Not Added Any Contact',
                        'data' => null
                    );

                }

                echo json_encode($response);

            }
            else {
                // If it's not an AJAX request, show an error message
                $this->session->set_flashdata('error', 'Direct Access Not Allowed.');
                redirect('dashboard');
            }

        }
        else {
            $this->session->set_flashdata('error', 'Unable to Communicate with Database...');
            redirect('dashboard');
        }

    }

    public function delete_contact()
    {
        if (!$this->session->has_userdata('user_id')) {
            $this->session->set_flashdata('error', 'Session Expired. Please Login Again');
            redirect('login');
        }

        if ($this->contact_model->load_database()) {

            if ($this->input->is_ajax_request()) 
            {

                $phone_number = $this->input->input_stream('phone_number');
                $sanitized_phone_number = $this->security->xss_clean( $phone_number);
                $user_id = $this->session->userdata('user_id');
                $result = $this->contact_model->delete_contact_by_phone_with_user_id($sanitized_phone_number, $user_id);

                if ($result) {
           
                    $response['status'] = 'success';
                    $response['message'] = 'Contact Delete Successfully. ' . $sanitized_phone_number;

                } else {
               
                    $response['status'] = 'error';
                    $response['message'] = 'Unable to Delete <br> Because Phone Number :'. $sanitized_phone_number . ' <br>Cannot be Found';
                }
                echo json_encode($response);  

            }
            else {
                // If it's not an AJAX request, show an error message
                $this->session->set_flashdata('error', 'Direct Access Not Allowed.');
                redirect('dashboard');
            }

        }
        else {
            $this->session->set_flashdata('error', 'Unable to Communicate with Database...');
            redirect('dashboard');
        }

    }

    public function get_contact_for_edit()
    {

        if (!$this->session->has_userdata('user_id')) {

            $this->session->set_flashdata('error', 'Session Expired. Please Login Again');
            redirect('login');

        }

        if ($this->contact_model->load_database()) {

            if ($this->input->is_ajax_request()) 
            {

                $phone_number = $this->input->input_stream('phone_number');
                $sanitized_phone_number = $this->security->xss_clean( $phone_number);
                $user_id = $this->session->userdata('user_id');
                $result = $this->contact_model->get_contact_by_phone_with_user_id($sanitized_phone_number, $user_id);

                if (!empty($result)) {
                   
                    $response = array(
                        'status' => 'success',
                        'message' => 'Successfully Retrieved From Database',
                        'data' => $result
                    );
                    

                } else {

                    $response = array(
                        'status' => 'error',
                        'message' => 'Unable to Find <br> Contact with Phone number : ' . $sanitized_phone_number,
                        'data' => null
                    );

                }

                echo json_encode($response);

            }
            else {
               
                $this->session->set_flashdata('error', 'Direct Access Not Allowed.');
                redirect('dashboard');
            }

        }
        else {

            $this->session->set_flashdata('error', 'Unable to Communicate with Database...');
            redirect('dashboard');

        }

    }

    public function update_contact()
    {
        if (!$this->session->has_userdata('user_id')) {
            $this->session->set_flashdata('error', 'Session Expired. Please Login Again');
            redirect('login');
        }

        if ($this->contact_model->load_database()) {

            if ($this->input->is_ajax_request()) {

                $this->form_validation->set_rules('name', 'Name', 'trim|required');
                $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
                $this->form_validation->set_rules('address', 'Address', 'trim');
                $this->form_validation->set_rules('phone_number', 'Phone', 'numeric|trim|required');
                $this->form_validation->set_rules('remark', 'Remark', 'trim');
                $this->form_validation->set_rules('image_location', 'Image', 'callback_validate_image_size');

                if ($this->form_validation->run() === false) {
                    $response['status'] = 'error';
                    $response['message'] = validation_errors();
                    echo json_encode($response);
                } else {



                    $raw_data = $this->input->post();
                    $raw_data['user_id'] = $this->session->has_userdata('user_id');
                    $sanitized_data = $this->security->xss_clean($raw_data);
                    $contactID = $this->contact_model->get_contact_id($sanitized_data['phone_number'],$sanitized_data['user_id']);
                    if (empty($_FILES['image_location']['name'])) {
                        $sanitized_data['image_location'] = $this->contact_model->get_image_location($contactID);
                    }

                    if (!empty($_FILES['image_location']['name'])) {

                        $image_location = $_FILES['image_location']['tmp_name'];
                        $image_type = $_FILES['image_location']['type'];


                        if (file_exists($image_location)) {

                            //$filesize = filesize($image_location);
                            //$filesize_kb = $filesize / 1024;
                        
                                $random_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.webp';
                                $img_path = '/assets/contact_image/' .$random_filename;
                                $file_path = FCPATH . $img_path;

                                switch ($image_type) {
                                    case 'image/jpeg':
                                        $original_image = imagecreatefromjpeg($image_location);
                                        break;
                                    case 'image/png':
                                        $original_image = imagecreatefrompng($image_location);
                                        break;
                                    case 'image/gif':
                                        $original_image = imagecreatefromgif($image_location);
                                        break;
                                    case 'image/bmp':
                                        $original_image = imagecreatefrombmp($image_location); // Custom function to create image from BMP (not directly supported by GD)
                                        break;
                                    case 'image/xbm':
                                        $original_image = imagecreatefromxbm($image_location); // Create image from XBM
                                        break;
                                    case 'image/xpm':
                                        $original_image = imagecreatefromxpm($image_location); // Create image from XPM
                                        break;
                                    case 'image/webp':
                                        $original_image = imagecreatefromwebp($image_location); // Create image from WebP
                                        break;
                                    case 'image/tiff':
                                    case 'image/tif':
                                        $original_image = imagecreatefromtiff($image_location); // Create image from TIFF
                                        break;
                                    case 'image/pcx':
                                        $original_image = imagecreatefrompcx($image_location); // Create image from PCX
                                        break;
                                    case 'image/vnd.wap.wbmp':
                                        $original_image = imagecreatefromwbmp($image_location); // Create image from WBMP
                                        break;
                                    case 'image/ico':
                                    case 'image/icon':
                                        $original_image = imagecreatefromico($image_location); // Create image from ICO
                                        break;
                                   
                                    default:
                                        $sanitized_data['image_location'] = '/assets/img/empty-profile-picture.webp';
                                        $unsupportedMessage = 'File Type: ' .  $image_type . 'is not supported.';
                                        break;
                                }

                                $new_width = 200;
                                $new_height = 250;
                                $resized_image = imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($resized_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, imagesx($original_image), imagesy($original_image));
                                $quality = 100;
                                imagewebp($resized_image, $file_path, $quality);
                                imagedestroy($original_image);
                                imagedestroy($resized_image);

                            $sanitized_data['image_location'] = $img_path;

                        } else {

                            $sanitized_data['image_location'] = '/assets/img/empty-profile-picture.webp';

                        }
                        
                    }



                        if (!empty($contactID)) {

/*                             if (empty($_FILES['image_location']['name'])) {
                                $response['status'] = 'success';
                                $response['message'] = $sanitized_data['image_location'];
                                echo json_encode($response);
                                return;
                            } */
                            $result = $this->contact_model->update_contact( $contactID, $sanitized_data);

                            if ($result === TRUE) {

                                $response['status'] = 'success';
                                $response['message'] = 'Successfully Updated the Contact';
                                echo json_encode($response);

                            } else {

                                $response['status'] = 'error';
                                $response['message'] = 'Unable to Update Contact Due to Database Related Error...';
                                echo json_encode($response);

                            }

                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Unable to Find the Contact In The Database...';
                            echo json_encode($response);
                        }
     
                }
            } else {
                // If it's not an AJAX request, show an error message
                $this->session->set_flashdata('error', 'Direct Access Not Allowed.');
                redirect('dashboard');
            }
        } else {
            $this->session->set_flashdata('error', 'Unable to Communicate with Database...');
            redirect('dashboard');
        }
    }
}
