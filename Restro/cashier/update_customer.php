<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

//Add Customer
if (isset($_POST['updateCustomer'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["customer_phoneno"]) || empty($_POST["customer_name"]) || empty($_POST['customer_email']) || empty($_POST['customer_password']) || empty($_POST['customer_address'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $customer_name = $_POST['customer_name'];
    $customer_phoneno = $_POST['customer_phoneno'];
    $customer_email = $_POST['customer_email'];
    $customer_password = sha1(md5($_POST['customer_password'])); //Hash This 
    $customer_address = $_POST['customer_address'];
    $update = $_GET['update'];

    //Update Captured information to the database table
    $postQuery = "UPDATE rpos_customers SET customer_name =?, customer_phoneno =?, customer_email =?, customer_password =?, customer_address =? WHERE customer_id =?";
    $postStmt = $mysqli->prepare($postQuery);
    //bind parameters
    $rc = $postStmt->bind_param('ssssss', $customer_name, $customer_phoneno, $customer_email, $customer_password, $customer_address, $update);
    $postStmt->execute();
    //declare a variable which will be passed to alert function
    if ($postStmt) {
      $success = "Customer Updated" && header("refresh:1; url=customes.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    $update = $_GET['update'];
    $ret = "SELECT * FROM  rpos_customers WHERE customer_id = '$update' ";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($cust = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body">
          </div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <!-- Table -->
        <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3>Please Fill All Fields</h3>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Customer Name</label>
                      <input type="text" name="customer_name" value="<?php echo $cust->customer_name; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Customer Phone Number</label>
                      <input type="text" name="customer_phoneno" value="<?php echo $cust->customer_phoneno; ?>" class="form-control">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Customer Email</label>
                      <input type="email" name="customer_email" value="<?php echo $cust->customer_email; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Customer Password</label>
                      <input type="password" name="customer_password" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-12">
                      <label>Customer Address</label>
                      <input type="text" name="customer_address" value="<?php echo $cust->customer_address; ?>" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="updateCustomer" value="Update Customer" class="btn btn-success">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
      <?php
      require_once('partials/_footer.php');
    }
      ?>
      </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>

</html>
