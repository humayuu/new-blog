<?php require 'header.php'; ?>
<!-- register -->
<section class="wrap__section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- register -->
                <!-- Form Register -->

                <div class="card mx-auto" style="max-width:520px;">
                    <article class="card-body">
                        <header class="mb-4">
                            <h4 class="card-title">Sign up</h4>
                        </header>
                        <form action="#">
                            <div class="form-row">
                                <div class="col form-group">
                                    <label>First name</label>
                                    <input type="text" name="fname" class="form-control" placeholder="" autofocus>
                                </div> <!-- form-group end.// -->
                                <div class="col form-group">
                                    <label>Last name</label>
                                    <input type="text" name="lname" class="form-control" placeholder="">
                                </div> <!-- form-group end.// -->
                            </div> <!-- form-row end.// -->
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="">
                                <small class="form-text text-muted">We'll never share your email with anyone
                                    else.</small>
                            </div> <!-- form-group end.// -->
                            <div class="form-group">
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" checked="" type="radio" name="gender"
                                        value="male">
                                    <span class="custom-control-label"> Male </span>
                                </label>
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="gender" value="female">
                                    <span class="custom-control-label"> Female </span>
                                </label>
                            </div> <!-- form-group end.// -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city">
                                </div> <!-- form-group end.// -->
                                <div class="form-group col-md-6">
                                    <label>Country</label>
                                    <select id="inputState" name="country" class="form-control">
                                        <option selected disabled> Choose...</option>
                                        <option>Uzbekistan</option>
                                        <option>Russia</option>
                                        <option>United States</option>
                                        <option>India</option>
                                        <option>Pakistan</option>
                                        <option>Afganistan</option>
                                    </select>
                                </div> <!-- form-group end.// -->
                            </div> <!-- form-row.// -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Create password</label>
                                    <input class="form-control" type="password">
                                </div> <!-- form-group end.// -->
                                <div class="form-group col-md-6">
                                    <label>Repeat password</label>
                                    <input class="form-control" type="password">
                                </div> <!-- form-group end.// -->
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block"> Register </button>
                            </div> <!-- form-group// -->
                        </form>
                    </article><!-- card-body.// -->
                </div>
                <!-- end register -->
            </div>
        </div>
    </div>
</section>
<!-- end register -->

<?php require 'footer.php'; ?>