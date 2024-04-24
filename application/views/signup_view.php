<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/assets/css/main.css">
<title>Login Page | MyPhoneBook</title>
</head>

<body>

<div class="container p-3">

  <div class="row justify-content-center mt-5">

    <div class="col-md-6 ">

      <div class="card add-shadow">

      <div class="d-flex justify-content-center align-items-center mb-4">
        <img src="/assets/img/logo-medium.png" alt="MyPhoneBook Logo" class="img-fluid">
      </div>

      <div class="d-flex justify-content-center align-items-center mb-4">
        <h1 class="text-center fw-normal">Welcome to MyPhoneBook<h1>
      </div>

      <div class="card-header d-flex justify-content-center align-items-center">Register</div>

        <div class="card-body">
          
          <form>

            <div class="mb-3">
                <label for="nickname" class="form-label">Nickname:</label>
                <input type="text" class="form-control" id="nickname">
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address:</label>
              <input type="email" class="form-control" id="email" aria-describedby="emailHelp">
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password:</label>
              <input type="password" class="form-control" id="password">
            </div>

            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm-password">
            </div>

            <div class="mb-5 d-flex justify-content-center">
              <button type="submit" class="btn btn-primary">Sign Up</button>
            </div>

          </form>

          <div class="col-md-12">
            <p class="text-center mb-0">Have an account? <a href="<?php echo base_url('login'); ?>" class="signup-link">Login</a></p>
          </div>
          
        </div>

      </div>

    </div>


  </div>

</div>


</body>
</html>
