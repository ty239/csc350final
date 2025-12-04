# Sports Shop E-Commerce Flow Diagram

## User Journey Flowchart

```
┌─────────────────────────────────────────────────────────────────┐
│                   Sports Shop Home Page                          │
│                      (index.php)                                 │
│                                                                   │
│  ├─ View Products (Basketball, Tennis Racket, etc.)             │
│  ├─ See Prices and Stock Status                                 │
│  └─ See "SOLD OUT" label for out-of-stock items                │
└─────────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────┴──────────┐
                    │                    │
           [Not Logged In]      [Logged In]
                    │                    │
                    ▼                    ▼
        ┌──────────────────────┐  ┌─────────────────┐
        │  Register/Login       │  │ Add to Cart      │
        │  (register.php)       │  │ (add_to_cart.php)│
        │  (login.php)          │  └─────────────────┘
        └──────────────────────┘         │
                    │                    ▼
                    └──────────►  ┌─────────────────┐
                                  │ Shopping Cart   │
                                  │ (cart.php)      │
                                  │                 │
                                  │ • View Items    │
                                  │ • Remove Items  │
                                  │ • See Total     │
                                  └─────────────────┘
                                          │
                                          ▼
                                  ┌─────────────────┐
                                  │ Checkout        │
                                  │ (checkout.php)  │
                                  │                 │
                                  │ • Review Order  │
                                  │ • Process Order │
                                  │ • Email Confirm │
                                  │ • Update Stocks │
                                  └─────────────────┘
                                          │
                                    ┌─────┴─────┐
                                    │           │
                            Success │           │ Error
                                    ▼           ▼
                            ┌─────────────┐  Back to
                            │ Order Done! │  Checkout
                            │             │
                            │ Email Sent  │
                            │ to Customer │
                            └─────────────┘
```

## Database Relationships

```
┌──────────────────────┐
│      USERS           │
├──────────────────────┤
│ id (PK)              │
│ username (UNIQUE)    │
│ email (UNIQUE)       │
│ password (hashed)    │
│ created_at           │
└──────┬───────────────┘
       │
       │ (1 user to many orders)
       │
       ▼
┌──────────────────────┐
│      ORDERS          │
├──────────────────────┤
│ id (PK)              │
│ user_id (FK)         │
│ total_amount         │
│ order_date           │
└──────┬───────────────┘
       │
       │ (1 order to many items)
       │
       ▼
┌──────────────────────┐
│   ORDER_ITEMS        │
├──────────────────────┤
│ id (PK)              │
│ order_id (FK)        │
│ product_id (FK)      │
│ quantity             │
│ price                │
└──────┬───────────────┘
       │
       │ (many items from 1 product)
       │
       ▼
┌──────────────────────┐
│    PRODUCTS          │
├──────────────────────┤
│ id (PK)              │
│ name                 │
│ description          │
│ price                │
│ quantity (stock)     │
│ image_url            │
│ created_at           │
└──────────────────────┘

Also uses:
┌──────────────────────┐
│      CART            │
├──────────────────────┤
│ id (PK)              │
│ user_id (FK)         │
│ product_id (FK)      │
│ quantity             │
│ added_at             │
└──────────────────────┘
(Temporary: cleared after checkout)
```

## Authentication Flow

```
╔════════════════════════════════════════════════════════╗
║                    REGISTRATION                         ║
╠════════════════════════════════════════════════════════╣
║                                                         ║
║  1. User fills form (username, email, password)        ║
║  2. Server validates input                             ║
║     ├─ Username ≥ 3 characters?                        ║
║     ├─ Email format valid?                             ║
║     ├─ Password ≥ 6 characters?                        ║
║     ├─ Passwords match?                                ║
║     └─ Username/Email unique?                          ║
║  3. Hash password with bcrypt                          ║
║  4. Insert user into database                          ║
║  5. Redirect to login page                             ║
║                                                         ║
╚════════════════════════════════════════════════════════╝

╔════════════════════════════════════════════════════════╗
║                       LOGIN                             ║
╠════════════════════════════════════════════════════════╣
║                                                         ║
║  1. User enters username & password                    ║
║  2. Query database for user by username                ║
║  3. Use password_verify() to check password            ║
║  4. If valid:                                          ║
║     ├─ Create session                                  ║
║     ├─ Store user_id in $_SESSION                      ║
║     └─ Redirect to home page                           ║
║  5. If invalid: Show error message                     ║
║                                                         ║
╚════════════════════════════════════════════════════════╝

╔════════════════════════════════════════════════════════╗
║                      SESSION                           ║
╠════════════════════════════════════════════════════════╣
║                                                         ║
║  $_SESSION['user_id']   - User's database ID           ║
║  $_SESSION['username']  - User's display name          ║
║                                                         ║
║  Session persists until:                              ║
║  • logout.php destroys session                         ║
║  • Browser closes (depends on settings)                ║
║  • Session timeout (default 24 minutes)                ║
║                                                         ║
╚════════════════════════════════════════════════════════╝
```

## Shopping Cart & Inventory Flow

```
┌─────────────────────────────────────────────────────────┐
│  ADDING TO CART (add_to_cart.php)                       │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  1. Check if user logged in (session_start())          │
│  2. Get product_id and quantity from form               │
│  3. Query products table for stock availability        │
│  4. Check if quantity ≤ available stock                │
│  5. Check if product already in cart:                  │
│     • If YES: Update quantity in cart table            │
│     • If NO: Insert new row in cart table              │
│  6. Redirect back to index.php                          │
│                                                          │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  CHECKOUT PROCESS (checkout.php)                        │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  1. User reviews order summary                         │
│  2. User confirms terms & conditions                   │
│  3. Submit to checkout (POST request)                  │
│  4. Database transaction begins:                       │
│                                                          │
│     ├─ CREATE order                                    │
│     │  └─ Insert into orders table                     │
│     │                                                   │
│     ├─ FOR EACH item in cart:                          │
│     │  ├─ Insert into order_items table                │
│     │  └─ UPDATE products.quantity -= qty             │
│     │                                                   │
│     ├─ CLEAR cart                                      │
│     │  └─ Delete all cart items for user              │
│     │                                                   │
│     └─ SEND EMAIL                                      │
│        ├─ Format order details                         │
│        ├─ Include customer name, items, total         │
│        └─ Use mail() function                          │
│                                                          │
│  5. If all success: Show confirmation                  │
│  6. If error: Rollback transaction                     │
│                                                          │
└─────────────────────────────────────────────────────────┘

Example Inventory Update:
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│  Before      │         │   Checkout   │         │    After     │
│              │         │              │         │              │
│ Basketball:  │         │  Bought: 2   │  ──►   │ Basketball:  │
│ 50 units     │         │  units       │         │ 48 units     │
│              │         │              │         │              │
│ Tennis Racket│         │  Bought: 1   │  ──►   │ Tennis Racket│
│ 30 units     │         │  unit        │         │ 29 units     │
└──────────────┘         └──────────────┘         └──────────────┘
```

## Email Confirmation Flow

```
┌──────────────────────────────────────────────────────────┐
│            EMAIL GENERATION & SENDING                     │
├──────────────────────────────────────────────────────────┤
│                                                            │
│  1. Get user email from database                          │
│                                                            │
│  2. Create email subject:                                │
│     Subject: "Order Confirmation - Sports Shop"          │
│                                                            │
│  3. Build message body with:                             │
│     • Greeting: "Dear [Username],"                       │
│     • Order ID: "Order #12345"                           │
│     • Order Date: "2025-12-04 14:30:00"                  │
│     • Item list with qty, price, subtotal                │
│     • Total amount: "$209.97"                            │
│     • Closing statement                                  │
│                                                            │
│  4. Set email headers:                                   │
│     • From: noreply@sportsshop.com                       │
│     • Reply-To: support@sportsshop.com                   │
│     • Content-Type: text/plain (or HTML)                │
│                                                            │
│  5. Send email using PHP mail() function:                │
│     mail($to, $subject, $message, $headers)             │
│                                                            │
│  6. Email delivered to customer inbox                    │
│                                                            │
└──────────────────────────────────────────────────────────┘

Example Email Structure:
┌────────────────────────────────────────────────────────┐
│ To: customer@example.com                               │
│ Subject: Order Confirmation - Sports Shop              │
│ From: noreply@sportsshop.com                           │
├────────────────────────────────────────────────────────┤
│                                                         │
│ Dear john_doe,                                         │
│                                                         │
│ Thank you for your order!                              │
│                                                         │
│ Order Details:                                         │
│ Order ID: 5                                            │
│ Order Date: 2025-12-04 14:30:00                        │
│                                                         │
│ Items Ordered:                                         │
│ ──────────────────────────────────────────────────     │
│ Basketball x1 @ $29.99 = $29.99                        │
│ Running Shoes x2 @ $119.99 = $239.98                   │
│ ──────────────────────────────────────────────────     │
│ Total Amount: $269.97                                  │
│                                                         │
│ We will process your order shortly...                  │
│                                                         │
│ Best regards,                                          │
│ Sports Shop Team                                       │
│                                                         │
└────────────────────────────────────────────────────────┘
```

## File Communication Map

```
index.php (Home)
├─ Reads from: db.php, style.css
├─ Sends to: add_to_cart.php (form action)
├─ Shows: All products from database
└─ Links to: login.php, register.php, cart.php

register.php
├─ Reads from: db.php, style.css
├─ Writes to: users table in database
├─ Validates: username, email, password
└─ Redirects to: login.php (on success)

login.php
├─ Reads from: db.php, style.css, users table
├─ Sets: $_SESSION['user_id'], $_SESSION['username']
├─ Validates: username, password against database
└─ Redirects to: index.php (on success)

add_to_cart.php
├─ Reads from: db.php, products table
├─ Writes to: cart table
├─ Checks: product stock availability
└─ Redirects to: index.php

cart.php
├─ Reads from: db.php, cart table, products table, style.css
├─ Shows: All items in user's cart
├─ Links to: remove_from_cart.php, checkout.php
└─ Displays: subtotals and total amount

remove_from_cart.php
├─ Reads from: db.php
├─ Deletes from: cart table
└─ Redirects to: cart.php

checkout.php
├─ Reads from: db.php, cart table, users table, style.css
├─ Writes to: orders table, order_items table
├─ Updates: products table (inventory)
├─ Clears: cart table
├─ Sends: Email via mail() function
└─ Shows: Order confirmation

logout.php
├─ Destroys: $_SESSION
└─ Redirects to: index.php

style.css
└─ Used by: ALL PHP files (styling)

db.php
└─ Used by: ALL PHP files (database connection)
```
