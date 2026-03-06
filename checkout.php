<?php
session_start();
require 'db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Your cart is empty. <a href='products.php'>Go to products</a>");
}

$cart = $_SESSION['cart'];
$total = 0;

$stmt = $conn->prepare("SELECT price FROM laptops WHERE id = ?");
foreach ($cart as $item) {
    $laptopId = intval($item['id']);
    $qty = intval($item['quantity']);

    $stmt->bind_param("i", $laptopId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $total += ($row['price'] * $qty);
    }
}
$stmt->close();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === "" || $email === "" || $phone === "") {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (1, ?)");
        $stmt->bind_param("d", $total);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();

        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, laptop_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $priceStmt = $conn->prepare("SELECT price FROM laptops WHERE id = ?");
        
        foreach ($cart as $item) {
            $laptopId = intval($item['id']);
            $qty = intval($item['quantity']);

            $priceStmt->bind_param("i", $laptopId);
            $priceStmt->execute();
            $pRes = $priceStmt->get_result();
            if ($row = $pRes->fetch_assoc()) {
                $price = $row['price'];
                $itemStmt->bind_param("iiid", $orderId, $laptopId, $qty, $price);
                $itemStmt->execute();
            }
        }
        $priceStmt->close();
        $itemStmt->close();

        $_SESSION['cart'] = []; 
        $success = "Order placed successfully! Order ID: #" . $orderId;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Windsor Laptops Sellers</title>
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
  <h2>Checkout</h2>

  <p><strong>Total Amount:</strong>  
    <span style="color:#2563eb; font-size:18px;">
      Ksh <?php echo number_format($total, 2); ?>
    </span>
  </p>

  <?php if ($success): ?>
      <p class="success"><?php echo htmlspecialchars($success); ?></p>
      <a href="products.php" class="btn">Continue Shopping</a>
  <?php else: ?>

    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div id="checkoutError" class="error" style="display:none;"></div>

    <form id="checkoutForm" method="post" style="max-width:400px; margin-top:20px;">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" id="name" name="name" required>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" id="phone" name="phone" required>
      </div>

      <button type="submit" class="btn">Place Order</button>
    </form>

  <?php endif; ?>

</div>

<script src="script.js"></script>
</body>
</html>
