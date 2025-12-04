<?php
// Database connection file
// Configure these with your actual database credentials

// Use environment variables if available (for Railway deployment)
$host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'sports_shop_db';

// Create connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Uncomment this section to create tables on first run
// You can run this once, then comment it back out
/*
if (isset($_GET['create_tables'])) {
    create_database_tables();
}

function create_database_tables() {
    global $conn;
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        quantity INT NOT NULL DEFAULT 0,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->query($sql);
    
    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    $conn->query($sql);
    
    // Create cart table (for temporary storage)
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    $conn->query($sql);
    
    // Insert sample products
    $sql = "INSERT INTO products (name, description, price, quantity, image_url) VALUES
    ('Basketball', 'Official size basketball for indoor and outdoor play', 29.99, 50, 'https://via.placeholder.com/300?text=Basketball'),
    ('Tennis Racket', 'Professional grade tennis racket with carbon fiber frame', 89.99, 30, 'https://via.placeholder.com/300?text=Tennis+Racket'),
    ('Soccer Ball', 'FIFA approved soccer ball for matches and training', 39.99, 0, 'https://via.placeholder.com/300?text=Soccer+Ball'),
    ('Running Shoes', 'Comfortable and durable running shoes with cushioning', 119.99, 25, 'https://via.placeholder.com/300?text=Running+Shoes'),
    ('Yoga Mat', 'Non-slip yoga mat for exercises and meditation', 24.99, 100, 'https://via.placeholder.com/300?text=Yoga+Mat'),
    ('Dumbbells Set', 'Adjustable dumbbells set (5-25 lbs)', 199.99, 15, 'https://via.placeholder.com/300?text=Dumbbells')";
    $conn->query($sql);
    
    echo "Database tables created successfully!";
}
*/
?>
