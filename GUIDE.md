# Sports Shop E-Commerce Website - Complete Setup Guide

## Project Overview

This is a fully functional e-commerce website for selling sports equipment, built with PHP, HTML, CSS, and MySQL. It includes user registration/login, product display, shopping cart, and order processing with email confirmation.

## Features

✅ **User Registration & Login** - Create accounts with secure password hashing
✅ **Product Display** - View all products with images, descriptions, and prices
✅ **Out of Stock Labels** - Clear "SOLD OUT" indicators for unavailable items
✅ **Shopping Cart** - Add/remove items from cart
✅ **Inventory Management** - Automatic stock updates upon purchase
✅ **Order Confirmation** - Email sent with customer name, items, and total
✅ **Responsive Design** - Works on desktop and mobile devices

## Project Structure

```
sports_shop/
├── db.php                 # Database connection and configuration
├── register.php           # User registration form
├── login.php              # User login form
├── logout.php             # Logout handler
├── index.php              # Home page with product listing
├── cart.php               # Shopping cart display
├── add_to_cart.php        # Add item to cart logic
├── remove_from_cart.php   # Remove item from cart logic
├── checkout.php           # Checkout and order processing
├── style.css              # All styling (responsive design)
└── GUIDE.md               # This file
```

## Database Setup

### Prerequisites

- PHP 7.4+ with MySQLi support
- MySQL Server (local or remote)
- Web server (Apache, Nginx, etc.)

### Step 1: Create Database

Open MySQL and run:

```sql
CREATE DATABASE sports_shop_db;
USE sports_shop_db;
```

### Step 2: Create Tables

Open `db.php` and uncomment the table creation code section:

```php
/*
if (isset($_GET['create_tables'])) {
    create_database_tables();
}
...
*/
```

Remove the `/*` at the beginning and `*/` at the end, then:

1. Access: `http://localhost/sports_shop/db.php?create_tables=1`
2. You should see "Database tables created successfully!"
3. Comment the code back out (for security)

### Step 3: Configure Database Connection

Edit `db.php` and update these variables with your database credentials:

```php
$host = 'localhost';         // Your database host
$db_user = 'root';           // Your MySQL username
$db_password = '';           // Your MySQL password
$db_name = 'sports_shop_db'; // Database name
```

## Database Schema

### Users Table

Stores customer account information with secure password hashing:

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

### Products Table

Catalog of sports equipment:

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

### Orders Table

Customer purchase records:

```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)
```

### Order Items Table

Individual items in each order:

```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)
```

### Cart Table

Temporary shopping cart storage:

```sql
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)
```

## Sample Data

The setup script inserts 6 sample products:

1. **Basketball** - $29.99 (50 in stock)
2. **Tennis Racket** - $89.99 (30 in stock)
3. **Soccer Ball** - $39.99 (SOLD OUT)
4. **Running Shoes** - $119.99 (25 in stock)
5. **Yoga Mat** - $24.99 (100 in stock)
6. **Dumbbells Set** - $199.99 (15 in stock)

## Usage Guide

### 1. User Registration

**Route:** `/register.php`

- Click "Register" link
- Enter unique username (min 3 characters)
- Enter valid email address
- Enter password (min 6 characters)
- Confirm password must match
- Submit to create account

**Security Features:**

- Passwords hashed with PHP's `password_hash()` (bcrypt)
- Unique username and email validation
- Email format validation

### 2. User Login

**Route:** `/login.php`

- Click "Login" link
- Enter username and password
- Sessions maintained during shopping

**Security Features:**

- Session-based authentication
- Password verification with `password_verify()`
- Redirect to login if trying to access protected pages

### 3. Browse Products

**Route:** `/index.php` (Home)

- View all available products
- Each product displays:
  - Product image (placeholder images)
  - Product name and description
  - Price in USD
  - Stock quantity or "SOLD OUT" label
  - "Add to Cart" button (if logged in)

**Features:**

- Grid layout (3-4 products per row)
- Responsive design adapts to screen size
- Out-of-stock items clearly marked

### 4. Shopping Cart

**Route:** `/cart.php`

**How to Add Items:**

1. Browse products on home page
2. Select quantity desired
3. Click "Add to Cart"
4. Redirected back to products
5. Click "Cart" link to view cart

**Cart Features:**

- View all items with prices
- See subtotal for each item
- View total order amount
- Remove individual items
- Continue shopping option
- Proceed to checkout button

### 5. Checkout & Payment

**Route:** `/checkout.php`

**Checkout Process:**

1. Review order summary
2. Confirm customer information
3. Check "I agree to terms and conditions"
4. Click "Complete Purchase"

**What Happens:**

- ✅ Order created in database
- ✅ Each product inventory reduced
- ✅ Cart emptied
- ✅ Confirmation email sent
- ✅ Order details displayed

**Email Confirmation Format:**

```
Subject: Order Confirmation - Sports Shop

Dear [Customer Name],

Thank you for your order!

Order Details:
Order ID: [###]
Order Date: [2025-12-04 14:30:00]

Items Ordered:
─────────────────────────────────────────────
Basketball x1 @ $29.99 = $29.99
Tennis Racket x2 @ $89.99 = $179.98
─────────────────────────────────────────────
Total Amount: $209.97

We will process your order shortly...

Best regards,
Sports Shop Team
```

## Deployment to Free Hosting

### Option 1: 000webhost

1. Go to https://www.000webhost.com
2. Sign up for free account
3. Create new website
4. Download FileZilla FTP client
5. Connect using provided FTP credentials
6. Upload all `sports_shop` files to `public_html` folder
7. Create MySQL database through hosting control panel
8. Update `db.php` with new database credentials
9. Run `db.php?create_tables=1` to create tables
10. Your site is live at: `https://yoursite.000webhostapp.com/sports_shop/`

### Option 2: Heroku

1. Install Heroku CLI
2. Create `Procfile`: `web: php -S localhost:${PORT}`
3. Push code: `git push heroku main`
4. Add MySQL database addon
5. Update `db.php` with database URL from addon

### Option 3: GitHub Pages (Static files only)

Can only host HTML/CSS/JS. PHP requires server-side hosting.

## Important Security Notes

⚠️ **For Production:**

1. **Email Configuration**: Update `checkout.php` line ~100:

   ```php
   // Change this to your business email
   $headers = "From: your-email@yourdomain.com\r\n";
   ```

2. **HTTPS**: Ensure hosting provider supports SSL/TLS

3. **Database Credentials**: Never hardcode sensitive data

   - Use environment variables instead:

   ```php
   $db_user = getenv('DB_USER');
   $db_password = getenv('DB_PASS');
   ```

4. **Input Sanitization**: Already implemented with prepared statements

5. **CSRF Protection**: Add token validation for checkout (in production)

6. **Admin Panel**: Create admin.php to manage products (not included)

## Testing Checklist

- [ ] Database connection works
- [ ] User registration with valid/invalid data
- [ ] Login with correct/incorrect credentials
- [ ] Product display with images loads
- [ ] "SOLD OUT" shows for out-of-stock items
- [ ] Add products to cart
- [ ] Remove items from cart
- [ ] Quantity validation (can't exceed stock)
- [ ] Checkout completes successfully
- [ ] Inventory decreases after purchase
- [ ] Email confirmation received
- [ ] Cart clears after checkout
- [ ] Logout redirects to home
- [ ] Responsive design on mobile

## Troubleshooting

### "Connection failed" error

**Solution:** Check database credentials in `db.php`

```php
$host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'sports_shop_db';
```

### "Table doesn't exist" error

**Solution:** Run `db.php?create_tables=1` or manually create tables using SQL

### Sessions not working

**Solution:** Ensure `session_start()` is at top of every PHP file

### Emails not sending

**Solutions:**

- Check hosting provider's mail settings
- Use third-party service (SendGrid, Mailgun)
- Configure SMTP in php.ini

### Product images not loading

**Solution:** Images use placeholder URLs from `https://via.placeholder.com`. Replace with actual URLs:

```php
// In db.php, update the INSERT statement
'image_url' => 'https://yoursite.com/images/basketball.jpg'
```

## Adding New Products

To add products after initial setup, insert into database:

```sql
INSERT INTO products (name, description, price, quantity, image_url)
VALUES (
    'Soccer Cleats',
    'Professional soccer cleats with studs',
    149.99,
    20,
    'https://via.placeholder.com/300?text=Soccer+Cleats'
);
```

## Extending the Project

### Admin Panel Ideas:

- Product management (add/edit/delete)
- View all orders
- Manage inventory
- User management

### Feature Ideas:

- Product categories/filtering
- Search functionality
- User account dashboard
- Order history
- Wishlist
- Product reviews
- Coupon codes
- Multiple payment methods

## File Descriptions

| File                   | Purpose                                                 |
| ---------------------- | ------------------------------------------------------- |
| `db.php`               | Database connection, table creation script, sample data |
| `register.php`         | User registration form with validation                  |
| `login.php`            | User login with session management                      |
| `logout.php`           | Session destruction and redirect                        |
| `index.php`            | Product listing page with filters                       |
| `cart.php`             | Shopping cart display and management                    |
| `add_to_cart.php`      | Handle add-to-cart requests                             |
| `remove_from_cart.php` | Handle item removal from cart                           |
| `checkout.php`         | Order processing and email confirmation                 |
| `style.css`            | All responsive styling                                  |

## Support & Contact

For issues or questions:

1. Check Brightspace course materials
2. Review this GUIDE.md
3. Check hosting provider's help section
4. Verify database credentials
5. Check server error logs

## Final Submission

1. Deploy all files to hosting server
2. Test all functionality thoroughly
3. Copy live URL (e.g., `https://yourdomain.com/sports_shop/`)
4. Submit URL to Brightspace Final Project submission

**Example URL Format:**

```
https://yourname.000webhostapp.com/sports_shop/
```

---

**Created:** December 4, 2025
**Version:** 1.0
**Status:** Production Ready (with recommended security updates)
