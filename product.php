<?php
include 'config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Set a message to display on the login page
    $_SESSION['message'] = "You need to log in to access this page.";

    // Redirect to loginview.php
    header("Location: loginview.php");
    exit();
}

// Number of products per page
$products_per_page = 8;

// Get the current page number
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
  $current_page = (int) $_GET['page'];
} else {
  $current_page = 1;
}

// Ensure the current page is within valid range
if ($current_page < 1) {
  $current_page = 1;
}

// Calculate the offset
$offset = ($current_page - 1) * $products_per_page;

// Initialize search query variable
$search_query = '';
$where_clause = '';

// Check if a search query is provided
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
  $search_query = mysqli_real_escape_string($conn, $_GET['search_query']);
  $where_clause = "WHERE `name` LIKE '%$search_query%'";
}

// Fetch total number of products
$total_products_sql = "SELECT COUNT(*) AS total FROM `products` $where_clause";
$total_products_result = mysqli_query($conn, $total_products_sql);
$total_products_row = mysqli_fetch_assoc($total_products_result);
$total_products = $total_products_row['total'];

// Calculate total pages
$total_pages = ceil($total_products / $products_per_page);

// Ensure the current page isn't beyond the total pages
if ($current_page > $total_pages && $total_pages > 0) {
  $current_page = $total_pages;
  $offset = ($current_page - 1) * $products_per_page;
}

// Fetch products for the current page
$sql = "SELECT `id`, `image_path`, `name`, `stock`, `price`, `category`, `description`, `created_at` FROM `products` $where_clause LIMIT $products_per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);

$success_message = '';
if (isset($_SESSION['success_message'])) {
  $success_message = $_SESSION['success_message'];
  unset($_SESSION['success_message']); // Clear the message after use
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Box icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
  <!-- Custom StyleSheet -->
  <link rel="stylesheet" href="./css/styles.css" />
  <title>Boy’s T-Shirt</title>
</head>

<body>
  <!-- Navigation -->
  <?php include 'visitorheader.php'; ?>

  <?php if (!empty($success_message)): ?>
    <div id="success-message" class="success-message">
      <?php echo htmlspecialchars($success_message); ?>
    </div>
  <?php endif; ?>
  <!-- All Products -->
  <section class="section all-products" id="products">
    <div class="top container">
      <?php if (!empty($search_query)): ?>
        <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
      <?php else: ?>
        <h1>All Products</h1>
      <?php endif; ?>
      <form>
        <select>
          <option value="1">Default Sorting</option>
          <option value="2">Sort By Price</option>
          <option value="3">Sort By Popularity</option>
          <option value="4">Sort By Sale</option>
          <option value="5">Sort By Rating</option>
        </select>
        <span><i class="bx bx-chevron-down"></i></span>
      </form>
    </div>
    <div class="product-center container">
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <div class="product-item">
            <div class="overlay">
              <a href="productDetails.php?id=<?php echo $row['id']; ?>" class="product-thumb">
                <img src="<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" />
              </a>
              <?php if ($row['stock'] == 0) { ?>
                <span class="out-of-stock">Out of Stock</span>
              <?php } ?>
            </div>
            <div class="product-info">
              <span><?php echo htmlspecialchars($row['category']); ?></span>
              <a href="productDetails.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a>
              <h4>$<?php echo number_format($row['price'], 2); ?></h4>
            </div>
            <ul class="icons">
              <li><a href="find_similar.php?id=<?php echo $row['id']; ?>" class="find-similar" title="Find Similar">
                  <i class="bx bx-search-alt"></i>
                </a></li>
              <li>
                <a href="productDetails.php?id=<?php echo $row['id']; ?>">
                  <i class="bx bx-show"></i> <!-- Changed the icon to "view" (eye icon) -->
                </a>
              </li>

              <li>
                <a href="add_to_cart.php?product_id=<?php echo $row['id']; ?>">
                  <i class="bx bx-cart"></i>
                </a>
              </li>
            </ul>
          </div>
          <?php
        }
      } else {
        echo "<p>No products found.</p>";
      }
      ?>
    </div>
  </section>

  <!-- Pagination Section -->
  <section class="pagination">
    <div class="container">
      <?php
      // Build the base URL with the search query if present
      $base_url = 'product.php?';
      if (!empty($search_query)) {
        $base_url .= 'search_query=' . urlencode($search_query) . '&';
      }
      ?>

      <?php if ($current_page > 1): ?>
        <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>">
          <i class="bx bx-left-arrow-alt"></i>
        </a>
      <?php endif; ?>

      <?php
      // Define maximum number of page links to display
      $max_links = 4;

      // Calculate the start and end page numbers
      $start = max($current_page - floor($max_links / 2), 1);
      $end = min($start + $max_links - 1, $total_pages);

      // Adjust start if at the end
      if ($end - $start < $max_links - 1) {
        $start = max($end - $max_links + 1, 1);
      }

      // Display the page numbers
      for ($page = $start; $page <= $end; $page++):
        if ($page == $current_page):
          ?>
          <span><?php echo $page; ?></span>
        <?php else: ?>
          <a href="<?php echo $base_url; ?>page=<?php echo $page; ?>"><?php echo $page; ?></a>
        <?php endif; endfor; ?>

      <?php if ($current_page < $total_pages): ?>
        <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>">
          <i class="bx bx-right-arrow-alt"></i>
        </a>
      <?php endif; ?>
    </div>
  </section>

  <?php include 'visitorfooter.php'; ?>
  <!-- Custom Script -->
  <script src="./js/index.js"></script>
</body>

</html>