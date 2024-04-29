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
  <link rel="stylesheet" type="text/css" href="/assets/css/sweetalert2.min.css">
  <script src="/assets/js/jquery-3.7.1.min.js"></script>
  <script src="/assets/js/sweetalert2.min.js"></script>
  <script src="/assets/js/bootstrap.bundle.min.js"></script>
  <title> <?php echo $title; ?> | MyPhoneBook</title>
</head>

<body>
  <?php $this->load->view($header_view); ?>
  <?php $this->load->view($recently_added_view); ?>
  <?php $this->load->view($contacts_view); ?>



  <?php if ($this->session->flashdata('success')) : ?>
    <script>
      Swal.fire({
        title: 'Success!',
        text: '<?php echo $this->session->flashdata('success'); ?>',
        icon: 'success',
        showConfirmButton: false,
        timer: 1000
      });
    </script>
  <?php endif; ?>

  <?php if ($this->session->flashdata('error')) : ?>
    <script>
      Swal.fire({
        title: 'Error!',
        text: '<?php echo $this->session->flashdata('error'); ?>',
        icon: 'error',
        showConfirmButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#000000',
      });
    </script>
  <?php endif; ?>

  <script>
    document.getElementById('add-new-btn').addEventListener('click', function() {
      Swal.fire({
        title: 'Add New Contact',
        html: `
        <form id="add-contact-form">
        <div class="row mb-3">
    <div class="col-sm-6 col-12 m-auto d-flex align-items-center justify-content-center">
        <div class="fixed-image">
            <img id="add-image-frame" src="/assets/img/empty-profile-picture.webp" class="img-fluid" alt="Contact Image">
        </div>
    </div>
    <div class="col-12">
    <small class="text-muted"><em>(*Supported Format: .webp, .png, .jpeg | Max File Size:10MB)</em></span>
    </div>
</div>
      <div class="row mb-3">
        <div class="col-sm-6 col-12 m-auto ">
        <input class="mb-3" type="file" class="form-control-file" id="image" name="image_location" accept="image/*" onchange="previewImage(this)">
        </div>
      </div>
  <div class="row">
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Name:</span>
          </div>
          <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required autocomplete="true">
        </div>
      </div>
    </div>
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Address:</span>
          </div>
          <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" autocomplete="true">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Email:</span>
          </div>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" autocomplete="true">
        </div>
      </div>
    </div>
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Phone Number:</span>
          </div>
          <input type="tel" class="form-control" id="phone" name="phone_number" placeholder="Enter phone number" required autocomplete="true">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Remark:</span>
          </div>
          <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter remark" autocomplete="true">
        </div>
      </div>
    </div>
  </div>

</form>

        `,
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#000000',
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {

          const formData = new FormData(document.getElementById('add-contact-form'));
          $.ajax({
            url: '<?php echo site_url("api/add_contact"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {


              var responseData = JSON.parse(response);
              var status = responseData.status;
              var message = responseData.message;
              //console.log(`<b>${message}<b>`);
              if (status === 'success') {
                refreshRecentlyAdded();
                window.location.href = window.location.pathname;
                Swal.fire({
                  title: 'Success!',
                  html: `<b> ${message} <b> `,
                  icon: 'success',
                  showConfirmButton: false,

                  timer: 1000
                });
              } else {
                Swal.fire({
                  title: 'Error!',
                  showConfirmButton: false,
                  html: `<div class="bg-danger text-white"> ${message} </div>`,
                  icon: 'error',
                  showConfirmButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#000000',
                });
              }
            },
            error: function(xhr, status, error) {

              console.error(xhr.responseText);
              if (xhr.responseText.includes('Duplicate entry')) {

                const errorMessage = xhr.responseText.match(/Duplicate entry '.*?'/)[0];

                const phoneNumber = errorMessage.match(/'.*?-(.*?)'/)[1];

                Swal.fire({
                  title: 'Error!',
                  text: `Duplicated Phone Number : ${phoneNumber} Detected!`,
                  icon: 'error',
                  showConfirmButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#000000',
                });


              } else {
                Swal.fire({
                  title: 'Error!',
                  text: 'Some Database Error...',
                  icon: 'error',
                  showConfirmButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#000000',
                });
              }

            }
          });
        }
      });
    });

    function refreshRecentlyAdded() {
      $.ajax({
        url: '<?php echo site_url("api/get_last_contact"); ?>',
        type: 'POST',
        processData: false,
        contentType: false,
        success: function(response) {

          var responseData = JSON.parse(response);
          var status = responseData.status;
          var message = responseData.message;
          var data = responseData.data;
          //console.log("Data:", data.name);

          var container = document.getElementById('recentlyContainer');
          container.innerHTML = '';

          var firstPart =
            `
        <div class="card add-shadow-2">
        <div class="card-header bg-success text-white">
            <h3 class="m-0">The Last Added Contacts</h3>
        </div>
        `;

          if (status === 'success') {
            var secondPart =
              `
        <div class="card-body">
                <div class="row">
                    <div class="col-sm-8 col-md-6 col-lg-4 col-xl-4 col-11 m-auto">
                        <div class="card add-shadow-4">
                            <img src="${data.image_location}" class="card-img-top img-fluid custom-card-img" alt="...">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col">
                                        <div class="card-label" for="name">Name:</div>
                                        <p class="card-text truncate" id="name">${data.name}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="card-label" for="address">Address:</div>
                                        <p class="card-text truncate" id="address">${data.address}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="card-label" for="email">Email:</div>
                                        <p class="card-text truncate" id="email">${data.email}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="card-label" for="phone">Phone:</div>
                                        <p class="card-text truncate" id="phone">${data.phone_number}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="card-label" for="remark">Remark:</div>
                                        <p class="card-text truncate" id="remark">${data.remark}</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-sm-4 col-6 d-flex justify-content-center align-items-center m-auto">
                                        <button value="${data.phone_number}" id="recentEditButton" class="btn btn-success btn-lg btn-block border-line-1" onclick="editContact('${data.phone_number}')">Edit</button> 
                                    </div>
                                    <div class="col-sm-4 col-6 d-flex justify-content-center align-items-center m-auto">
                                    <button value="${data.phone_number}" id="recentDeleteButton" class="btn btn-danger btn-lg btn-block border-line-2" onclick="deleteContact('${data.phone_number}')">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `
          } else {
            var secondPart =
              `
        <div class="card-body">
                <div class="row">
                    <div class="col-12 m-auto">
                        <div class="card add-shadow-4">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col text-center">
                                        ${message}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `
          }

          var thirdPart =
            `
         </div>
        </div>
        `;

          container.innerHTML = firstPart + secondPart + thirdPart;

        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    }



    function deleteContact(input_phone_number) {
      event.preventDefault();
      Swal.fire({
        title: 'Are you confirm to delete it?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#000000',
        confirmButtonText: 'Confirm'
      }).then((result) => {


        if (result.isConfirmed) {

          $.ajax({
            url: '<?php echo site_url("api/delete_contact"); ?>',
            type: 'POST',
            contentType: 'application/json',
            data: {
              phone_number: input_phone_number
            },
            success: function(response) {
              

              var responseData = JSON.parse(response);
              var status = responseData.status;
              var message = responseData.message;
              //console.log(`<b>${message}<b>`);
              if (status === 'success') {
                Swal.fire({
                  title: 'Success!',
                  html: `<b> ${message} <b> `,
                  icon: 'success',
                  showConfirmButton: false,
                  timer: 1000
                });

                refreshRecentlyAdded();
                window.location.href = window.location.pathname;
              } else {
                Swal.fire({
                  title: 'Error!',
                  showConfirmButton: false,
                  html: `<div class="bg-danger text-white"> ${message} </div>`,
                  icon: 'error',
                  showConfirmButton: true,
                });
              }
            },
            error: function(xhr, status, error) {

              if (xhr.responseText.includes('Duplicate entry')) {

                const errorMessage = xhr.responseText.match(/Duplicate entry '.*?'/)[0];

                const phoneNumber = errorMessage.match(/'.*?-(.*?)'/)[1];

                Swal.fire({
                  title: 'Error!',
                  text: `Duplicated Phone Number : ${phoneNumber} Detected!`,
                  icon: 'error',
                  showConfirmButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#000000',
                });


              } else {
                Swal.fire({
                  title: 'Error!',
                  text: 'Some Database Error...',
                  icon: 'error',
                  showConfirmButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#000000',
                });
              }

            }
          });

        }
      });
    }

    function editContact(input_phone_number) {
      event.preventDefault();
      $.ajax({
        url: '<?php echo site_url("api/get_contact_for_edit"); ?>',
        type: 'POST',
        contentType: 'application/json',
        data: {
          phone_number: input_phone_number
        },
        success: function(response) {

          var responseData = JSON.parse(response);
          var status = responseData.status;
          var message = responseData.message;
          var data = responseData.data;
          //console.log(`<b>${data}<b>`);

          if (status === 'success') {

            Swal.fire({
              title: 'Edit Contact',
              html: `
        <form id="edit-contact-form">
        <div class="row mb-3">
    <div class="col-sm-6 col-12 m-auto d-flex align-items-center justify-content-center">
        <div class="fixed-image">
            <img id="add-image-frame" src="${data.image_location}" class="img-fluid" alt="Contact Image">
        </div>
    </div>
    <div class="col-12">
    <small class="text-muted"><em>(*Supported Format: .webp, .png, .jpeg | Max File Size:10MB)</em></span>
    </div>
</div>
      <div class="row mb-3">
        <div class="col-sm-6 col-12 m-auto ">
        <input class="mb-3" type="file" class="form-control-file" id="image" name="image_location" accept="image/*" onchange="previewImage(this)">
        </div>
      </div>
  <div class="row">
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Name:</span>
          </div>
          <input value="${data.name}" type="text" class="form-control" id="name" name="name" placeholder="Enter name" required autocomplete="true">
        </div>
      </div>
    </div>
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Address:</span>
          </div>
          <input value="${data.address}" type="text" class="form-control" id="address" name="address" placeholder="Enter address" autocomplete="true">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Email:</span>
          </div>
          <input value="${data.email}" type="email" class="form-control" id="email" name="email" placeholder="Enter email" autocomplete="true">
        </div>
      </div>
    </div>
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Phone Number:</span>
          </div>
          <input value="${data.phone_number}" type="tel" class="form-control" id="phone" name="phone_number" placeholder="Enter phone number" required autocomplete="true">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-3">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text fixed-width-label bg-dark text-light">Remark:</span>
          </div>
          <input value="${data.remark}" type="text" class="form-control" id="remark" name="remark" placeholder="Enter remark" autocomplete="true">
        </div>
      </div>
    </div>
  </div>

</form>

        `,
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#000000',
              confirmButtonText: 'Confirm',
              cancelButtonText: 'Cancel',
              showLoaderOnConfirm: true,
              preConfirm: () => {

                const formData = new FormData(document.getElementById('edit-contact-form'));
                $.ajax({
                  url: '<?php echo site_url("api/update_contact"); ?>',
                  type: 'POST',
                  data: formData,
                  processData: false,
                  contentType: false,
                  success: function(response) {


                    var responseData = JSON.parse(response);
                    var status = responseData.status;
                    var message = responseData.message;
                    //console.log(`<b>${message}<b>`);
                    if (status === 'success') {
                      window.location.href = window.location.pathname;
                      refreshRecentlyAdded();
                      Swal.fire({
                        title: 'Success!',
                        html: `<b> ${message} <b> `,
                        icon: 'success',
                        showConfirmButton: false,

                        timer: 1000
                      });
                    } else {
                      Swal.fire({
                        title: 'Error!',
                        showConfirmButton: false,
                        html: `<div class="bg-danger text-white"> ${message} </div>`,
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#000000',
                      });
                    }
                  },
                  error: function(xhr, status, error) {

                    console.error(xhr.responseText);
                    if (xhr.responseText.includes('Duplicate entry')) {

                      const errorMessage = xhr.responseText.match(/Duplicate entry '.*?'/)[0];

                      const phoneNumber = errorMessage.match(/'.*?-(.*?)'/)[1];

                      Swal.fire({
                        title: 'Error!',
                        text: `Duplicated Phone Number : ${phoneNumber} Detected!`,
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#000000',
                      });


                    } else {
                      Swal.fire({
                        title: 'Error!',
                        text: 'Some Database Error...',
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#000000',
                      });
                    }

                  }
                });
              }
            });

          } else {
            Swal.fire({
              title: 'Error!',
              showConfirmButton: false,
              html: `<div class="bg-danger text-white"> ${message} </div>`,
              icon: 'error',
              showConfirmButton: true,
            });
          }

        },
        error: function(xhr, status, error) {

          //console.error(xhr.responseText);
          if (xhr.responseText.includes('Duplicate entry')) {

            const errorMessage = xhr.responseText.match(/Duplicate entry '.*?'/)[0];

            const phoneNumber = errorMessage.match(/'.*?-(.*?)'/)[1];

            Swal.fire({
              title: 'Error!',
              text: `Duplicated Phone Number : ${phoneNumber} Detected!`,
              icon: 'error',
              showConfirmButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#000000',
            });


          } else {
            Swal.fire({
              title: 'Error!',
              text: 'Some Database Error...',
              icon: 'error',
              showConfirmButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#000000',
            });
          }

        }
      });
    }
  </script>

  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('#add-image-frame').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
      }
    }

    function goToPage() {

      var pageNumber = document.getElementById("pageInput").value;

      if (!isNaN(pageNumber)) {

        switch (true) {
          case (pageNumber === 0 || pageNumber === 1):
            pageNumber = 1;
            break;
          default:
            if (pageNumber > 1) {
              pageNumber -= 1;
              pageNumber *= 8;
            }
            break;
        }

        window.location.href = "<?php echo site_url('dashboard/index') ?>" + "/" + pageNumber;

      } else {

        Swal.fire({
          title: 'Error!',
          text: `Please enter a valid page number.`,
          icon: 'error',
          showConfirmButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#000000',
        });

      }

    }
  </script>

</body>

</html>