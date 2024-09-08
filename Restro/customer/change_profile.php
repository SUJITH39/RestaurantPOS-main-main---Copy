<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['ChangeProfile'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["customer_phoneno"]) || empty($_POST["customer_name"]) || empty($_POST['customer_email']) || empty($_POST['customer_address'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $customer_name = $_POST['customer_name'];
        $customer_phoneno = $_POST['customer_phoneno'];
        $customer_email = $_POST['customer_email'];
        $customer_address = $_POST['customer_address'];
        $customer_id = $_SESSION['customer_id'];

        // Update the customer profile information
        $postQuery = "UPDATE rpos_customers SET customer_name =?, customer_phoneno =?, customer_email =?, customer_address =? WHERE customer_id =?";
        $postStmt = $mysqli->prepare($postQuery);
        // Bind parameters
        $rc = $postStmt->bind_param('sssss', $customer_name, $customer_phoneno, $customer_email, $customer_address, $customer_id);
        $postStmt->execute();
        // Declare a variable to pass to the alert function
        if ($postStmt) {
            $success = "Profile Updated";
            header("refresh:1; url=dashboard.php");
        } else {
            $err = "Please Try Again Or Try Later";
        }
    }
}

if (isset($_POST['changePassword'])) {
    // Change Password logic
    $error = 0;
    if (isset($_POST['old_password']) && !empty($_POST['old_password'])) {
        $old_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['old_password']))));
    } else {
        $error = 1;
        $err = "Old Password Cannot Be Empty";
    }
    if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
        $new_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['new_password']))));
    } else {
        $error = 1;
        $err = "New Password Cannot Be Empty";
    }
    if (isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
        $confirm_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['confirm_password']))));
    } else {
        $error = 1;
        $err = "Confirmation Password Cannot Be Empty";
    }

    if (!$error) {
        $customer_id = $_SESSION['customer_id'];
        $sql = "SELECT * FROM rpos_customers WHERE customer_id = '$customer_id'";
        $res = mysqli_query($mysqli, $sql);
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            if ($old_password != $row['customer_password']) {
                $err = "Please Enter Correct Old Password";
            } elseif ($new_password != $confirm_password) {
                $err = "Confirmation Password Does Not Match";
            } else {
                $new_password = sha1(md5($_POST['new_password']));
                // Update password in the database
                $query = "UPDATE rpos_customers SET customer_password =? WHERE customer_id =?";
                $stmt = $mysqli->prepare($query);
                // Bind parameters
                $rc = $stmt->bind_param('si', $new_password, $customer_id);
                $stmt->execute();

                // Declare a variable to pass to the alert function
                if ($stmt) {
                    $success = "Password Changed";
                    header("refresh:1; url=dashboard.php");
                } else {
                    $err = "Please Try Again Or Try Later";
                }
            }
        }
    }
}
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>

        <!-- Header -->
        <?php
        $customer_id = $_SESSION['customer_id'];
        $ret = "SELECT * FROM rpos_customers WHERE customer_id = '$customer_id'";
        $stmt = $mysqli->prepare($ret);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($customer = $res->fetch_object()) {
        ?>
            <div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center" style="min-height: 600px; background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover; background-position: center top;">
                <span class="mask bg-gradient-default opacity-8"></span>
                <div class="container-fluid d-flex align-items-center">
                    <div class="row">
                        <div class="col-lg-7 col-md-10">
                            <h1 class="display-2 text-white">Hello <?php echo $customer->customer_name; ?></h1>
                            <p class="text-white mt-0 mb-5">This is your profile page. You can customize your profile as you want and also change your password.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <div class="container-fluid mt--8">
                <div class="row">
                    <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
                        <div class="card card-profile shadow">
                            <div class="row justify-content-center">
                                <div class="col-lg-3 order-lg-2">
                                    <div class="card-profile-image">
                                        <a href="#">
                                            <img src="../admin/assets/img/theme/user-a-min.png" class="rounded-circle">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4"></div>
                            <div class="card-body pt-0 pt-md-4">
                                <div class="row">
                                    <div class="col">
                                        <div class="card-profile-stats d-flex justify-content-center mt-md-5"></div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h3><?php echo $customer->customer_name; ?></h3>
                                    <div class="h5 font-weight-300">
                                        <i class="fas fa-envelope mr-2"></i><?php echo $customer->customer_email; ?>
                                    </div>
                                    <div class="h5 font-weight-300">
                                        <i class="fas fa-phone mr-2"></i><?php echo $customer->customer_phoneno; ?>
                                    </div>
                                    <div class="h5 font-weight-300">
                                        <i class="fas fa-home mr-2"></i><?php echo $customer->customer_address; ?> <!-- Display Address -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8 order-xl-1">
                        <div class="card bg-secondary shadow">
                            <div class="card-header bg-white border-0">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0">My account</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <h6 class="heading-small text-muted mb-4">User information</h6>
                                    <div class="pl-lg-4">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-username">Full Name</label>
                                                    <input type="text" name="customer_name" value="<?php echo $customer->customer_name; ?>" id="input-username" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-phone">Phone Number</label>
                                                    <input type="text" id="input-phone" value="<?php echo $customer->customer_phoneno; ?>" name="customer_phoneno" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-email">Email Address</label>
                                                    <input type="email" id="input-email" value="<?php echo $customer->customer_email; ?>" name="customer_email" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-address">Address</label>
                                                    <input type="text" id="input-address" value="<?php echo $customer->customer_address; ?>" name="customer_address" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="ChangeProfile" class="btn btn-primary mt-4">Save Profile</button>
                                        </div>
                                    </div>
                                </form>

                                <hr class="my-4" />

                                <form method="post">
                                    <h6 class="heading-small text-muted mb-4">Change Password</h6>
                                    <div class="pl-lg-4">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-old-password">Old Password</label>
                                                    <input type="password" id="input-old-password" name="old_password" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-new-password">New Password</label>
                                                    <input type="password" id="input-new-password" name="new_password" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-confirm-password">Confirm New Password</label>
                                                    <input type="password" id="input-confirm-password" name="confirm_password" class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="changePassword" class="btn btn-primary mt-4">Change Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } // End while loop
        ?>
    </div>

    <!-- Footer -->
    <?php require_once('partials/_footer.php'); ?>
</body>
</html>

