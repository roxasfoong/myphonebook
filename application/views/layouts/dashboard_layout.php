
<!-- 
    This layout required title & content_view
    data[tittle,content_view,header_view] 
-->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/assets/css/main.css">

<title> <?php echo $title; ?> | MyPhoneBook</title>
</head>

<body>
<?php $this->load->view($header_view); ?>
<?php $this->load->view($recently_added_view); ?>
<?php $this->load->view($utility_view); ?>
<?php $this->load->view($contacts_view); ?>


<script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>







