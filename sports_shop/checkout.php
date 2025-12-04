<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user info
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get cart items
$sql = "SELECT c.id, c.product_id, c.quantity, p.name, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$stmt->close();

// If cart is empty, redirect
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt->close();
        
        // Add order items and update inventory
        $order_items = [];
        foreach ($cart_items as $item) {
            // Insert order item
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
            
            // Update product inventory
            $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
            $stmt->close();
            
            $order_items[] = $item;
        }
        
        // Clear cart
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        // Send email
        $to = $user['email'];
        $subject = "Order Confirmation - Sports Shop";
        
        $message = "Dear " . htmlspecialchars($user['username']) . ",\n\n";
        $message .= "Thank you for your order!\n\n";
        $message .= "Order Details:\n";
        $message .= "Order ID: " . $order_id . "\n";
        $message .= "Order Date: " . date('Y-m-d H:i:s') . "\n\n";
        $message .= "Items Ordered:\n";
        $message .= str_repeat("-", 50) . "\n";
        
        foreach ($order_items as $item) {
            $item_subtotal = $item['price'] * $item['quantity'];
            $message .= $item['name'] . " x" . $item['quantity'] . " @ $" . number_format($item['price'], 2) . " = $" . number_format($item_subtotal, 2) . "\n";
        }
        
        $message .= str_repeat("-", 50) . "\n";
        $message .= "Total Amount: $" . number_format($total, 2) . "\n\n";
        $message .= "We will process your order shortly and send you tracking information.\n\n";
        $message .= "Best regards,\n";
        $message .= "Sports Shop Team\n";
        
        // Use PHP's mail function
        $headers = "From: noreply@sportsshop.com\r\n";
        $headers .= "Reply-To: support@sportsshop.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        mail($to, $subject, $message, $headers);
        
        $success = "Order placed successfully! A confirmation email has been sent to " . htmlspecialchars($user['email']);
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $error = "Error processing order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sports Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>Sports Shop</h1>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <h2>Checkout</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <p style="margin-top: 20px;">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </p>
        <?php else: ?>
            <div class="checkout-container">
                <div class="checkout-left">
                    <h3>Order Summary</h3>
                    <table class="checkout-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="checkout-total">
                        <h4>Total: $<?php echo number_format($total, 2); ?></h4>
                    </div>
                </div>

                <div class="checkout-right">
                    <h3>Customer Information</h3>
                    <div class="customer-info">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><em>A confirmation email will be sent to your email address.</em></p>
                    </div>

                    <h3>Shipping Address</h3>
                    <p style="color: #666; font-size: 14px;">
                        Please ensure your address is correct before confirming the order.<br>
                        (In a production environment, you would have a full address form here)
                    </p>

                    <form method="POST" action="" class="checkout-form">
                        <label>
                            <input type="checkbox" name="agree_terms" required>
                            I agree to the terms and conditions
                        </label>
                        <button type="submit" class="btn btn-primary btn-large">Complete Purchase</button>
                        <a href="cart.php" class="btn btn-secondary btn-large">Back to Cart</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Sports Shop. All rights reserved.</p>
    </footer>
</body>
</html>
