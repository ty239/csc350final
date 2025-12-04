<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity > 0) {
        // Check if product exists and has stock
        $sql = "SELECT id, quantity FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $product = $result->fetch_assoc();
            
            if ($product['quantity'] >= $quantity) {
                // Check if product already in cart
                $sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update quantity
                    $cart_item = $result->fetch_assoc();
                    $new_quantity = $cart_item['quantity'] + $quantity;
                    
                    if ($product['quantity'] >= $new_quantity) {
                        $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
                        $stmt->execute();
                    }
                } else {
                    // Insert new cart item
                    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
                    $stmt->execute();
                }
            }
        }
        $stmt->close();
    }
}

header("Location: index.php");
exit();
?>
