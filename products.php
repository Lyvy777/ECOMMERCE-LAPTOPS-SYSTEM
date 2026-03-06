<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Laptops - Windsor Laptops Sellers</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
  <h1>Windsor Laptops Sellers</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="cart.php">
      Cart
      <span id="cartCount" class="cart-badge">
        <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?>
      </span>
    </a>
  </nav>
</header>

<div class="container">
  <h2>Available Laptops</h2>

  <div class="products-grid">
    <?php
      $query = "SELECT * FROM laptops";
      $result = $conn->query($query);

      if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
      <div class="product-card">
        <img src="images/<?php echo htmlspecialchars($row['image_url']); ?>" 
             alt="<?php echo htmlspecialchars($row['name']); ?>">

        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p>Brand: <?php echo htmlspecialchars($row['brand']); ?></p>
        <p>Processor: <?php echo htmlspecialchars($row['processor']); ?></p>
        <p>RAM: <?php echo htmlspecialchars($row['ram']); ?></p>
        <p>Storage: <?php echo htmlspecialchars($row['storage']); ?></p>

        <p class="price">Ksh <?php echo number_format($row['price'], 2); ?></p>

        <form class="add-to-cart-form" method="post" action="add_to_cart.php">
          <input type="hidden" name="laptop_id" value="<?php echo $row['id']; ?>">
          <label>Qty:</label>
          <input type="number" name="quantity" value="1" min="1">
          <button class="btn" type="submit">Add to Cart</button>
        </form>

        <a class="btn btn-secondary" href="product.php?id=<?php echo $row['id']; ?>">
          View Details
        </a>
      </div>
    <?php
        endwhile;
      else:
        echo "<p>No laptops available at the moment.</p>";
      endif;
    ?>
  </div>
</div>

<script src="script.js"></script>
</body>
</html>
