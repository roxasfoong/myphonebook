<!-- 

  HTML ID: contactView
  Usage  : Container that contains contact 

-->

<div class="container" id="contactView">
    <div class="row text-center add-shadow-4">
        <div class="col-12 bg-primary text-white">
            <h2>Contacts</h3>
        </div>
        <div class="col-12 bg-info text-white add-text-shadow-1">
            <h3>Number of Contacts : 0 </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3 col-xl-3 col-11 m-auto">
            <div class="card add-shadow-4">
                <img src="/assets/img/empty-profile-picture.webp" class="card-img-top img-fluid custom-card-img" alt="...">
                <div class="card-body ">
                    <div class="row">
                        <div class="col">
                            <div class="card-label" for="name">Name:</div>
                            <p class="card-text truncate" id="name">John Doe</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card-label" for="address">Address:</div>
                            <p class="card-text truncate" id="address">123 Main St, City, Country</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card-label" for="email">Email:</div>
                            <p class="card-text truncate" id="email">johndoe@example.com</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card-label" for="phone">Phone:</div>
                            <p class="card-text truncate" id="phone">+1234567890</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4 col-6 d-flex justify-content-center align-items-center m-auto">
                            <a href="#" class="btn btn-success btn-lg btn-block border-line-1">Edit</a>
                        </div>
                        <div class="col-sm-4 col-6 d-flex justify-content-center align-items-center m-auto">
                            <a href="#" class="btn btn-danger btn-lg btn-block border-line-2">Delete</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row text-center add-shadow-4">
        <div class="col bg-primary text-white">&lt;</div>
        <div class="col bg-primary text-white">1</div>
        <div class="col bg-primary text-white">2</div>
        <div class="col bg-primary text-white">3</div>
        <div class="col bg-primary text-white">4</div>
        <div class="col bg-primary text-white">5</div>
        <div class="col bg-primary text-white">&gt;</div>
    </div>
    <div class="row">
        <div class="col-sm-3 col-lg-2 col-xl-2 col-4 m-auto">
            <div class="input-group add-shadow-4 ">
                <input type="text" class="form-control text-center" id="pageInput" placeholder="Page...">
                <button class="btn btn-danger" type="button">Go</button>
            </div>
        </div>
    </div>
</div>