<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

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
    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              Select On Any Product To Make An Order
            </div>
            <div class="card-body">
              <?php
              // Fetch distinct categories
              $categoryQuery = "SELECT DISTINCT prod_category FROM rpos_products ORDER BY prod_category";
              $categoryStmt = $mysqli->prepare($categoryQuery);
              $categoryStmt->execute();
              $categoryRes = $categoryStmt->get_result();

              while ($category = $categoryRes->fetch_object()) {
                $categoryName = $category->prod_category;
                echo "<h3 class='my-4'>$categoryName</h3>";

                // Fetch products for the current category
                $productQuery = "SELECT * FROM rpos_products WHERE prod_category = ? ORDER BY created_at DESC";
                $productStmt = $mysqli->prepare($productQuery);
                $productStmt->bind_param('s', $categoryName);
                $productStmt->execute();
                $productRes = $productStmt->get_result();

                if ($productRes->num_rows > 0) {
                  echo "<div class='table-responsive'>
                          <table class='table align-items-center table-flush'>
                            <thead class='thead-light'>
                              <tr>
                                <th scope='col'>Image</th>
                                <th scope='col'>Product Code</th>
                                <th scope='col'>Name</th>
                                <th scope='col'>Offer</th>
                                <th scope='col'>Price</th>
                                <th scope='col'>Action</th>
                              </tr>
                            </thead>
                            <tbody>";
                  
                  while ($prod = $productRes->fetch_object()) {
                    echo "<tr>
                            <td>";
                    if ($prod->prod_img) {
                      echo "<img src='../admin/assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                    } else {
                      echo "<img src='../admin/assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                    }
                    echo "</td>
                            <td>$prod->prod_code</td>
                            <td>$prod->prod_name</td>
                            <td>₹$prod->prod_offer</td>
                            <td>₹$prod->prod_price</td>
                            <td>
                              <a href='make_oder.php?prod_id=$prod->prod_id&prod_name=$prod->prod_name&prod_price=$prod->prod_price'>
                                <button class='btn btn-sm btn-warning'>
                                  <i class='fas fa-cart-plus'></i>
                                  Place Order
                                </button>
                              </a>
                            </td>
                          </tr>";
                  }

                  echo "  </tbody>
                        </table>
                      </div>";
                } else {
                  echo "<p>No products available in this category.</p>";
                }
              }
              ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
