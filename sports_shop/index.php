<?php
session_start();
include 'db.php';

// Get all products
$sql = "SELECT id, name, description, price, quantity, image_url FROM products ORDER BY id ASC";
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Shop - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>Sports Shop</h1>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="cart.php">Cart</a></li>
                    <li><span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="hero">
            <h2>Welcome to Sports Shop</h2>
            <p>Find the best sports equipment at great prices</p>
        </div>

        <?php if (!$is_logged_in): ?>
            <div class="alert alert-info">
                Please <a href="login.php">login</a> or <a href="register.php">register</a> to purchase items.
            </div>
        <?php endif; ?>

        <div class="products-section">
            <h3>Our Products</h3>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                        
                        <?php if ($product['quantity'] <= 0): ?>
                            <div class="sold-out">SOLD OUT</div>
                        <?php else: ?>
                            <p class="stock-info">In Stock: <?php echo $product['quantity']; ?> items</p>
                            
                            <?php if ($is_logged_in): ?>
                                <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" 
                                           max="<?php echo $product['quantity']; ?>" class="qty-input">
                                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Sports Shop. All rights reserved.</p>
    </footer>
</body>
</html>
