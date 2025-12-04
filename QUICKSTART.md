# Quick Start Guide

## 30-Second Setup

### 1. Create Database

```sql
CREATE DATABASE sports_shop_db;
```

### 2. Update db.php

Edit `sports_shop/db.php` and set your credentials:

```php
$host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'sports_shop_db';
```

### 3. Create Tables

Visit: `http://localhost/sports_shop/db.php?create_tables=1`

Then comment out the table creation code in `db.php` (for security).

### 4. Start Using!

- Home: `http://localhost/sports_shop/`
- Register: `http://localhost/sports_shop/register.php`
- Login: `http://localhost/sports_shop/login.php`

## File Checklist

Verify you have all these files:

```
sports_shop/
├── db.php                 ✓
├── register.php           ✓
├── login.php              ✓
├── logout.php             ✓
├── index.php              ✓
├── cart.php               ✓
├── add_to_cart.php        ✓
├── remove_from_cart.php   ✓
├── checkout.php           ✓
└── style.css              ✓
```

## Features Quick Reference

| Feature  | File         | Route         |
| -------- | ------------ | ------------- |
| Register | register.php | /register.php |
| Login    | login.php    | /login.php    |
| Products | index.php    | /index.php    |
| Cart     | cart.php     | /cart.php     |
| Checkout | checkout.php | /checkout.php |

## System Requirements

- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Modern Browser

## Security Features Included

✓ Password hashing (bcrypt)
✓ SQL injection prevention (prepared statements)
✓ Session management
✓ Input validation
✓ CSRF-safe forms

## Deployment Steps

1. Choose hosting (000webhost, Heroku, etc.)
2. Create MySQL database
3. Upload files via FTP
4. Update `db.php` credentials
5. Run `db.php?create_tables=1`
6. Submit URL to Brightspace

See `GUIDE.md` for detailed instructions!
