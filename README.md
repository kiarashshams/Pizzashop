# 🍕 Pizza Shop

A secure Pizza Shop web application developed with **PHP**, **MySQL**, **Bootstrap 5**, and **XAMPP** for the Web Programming & Security course.

---

## 📌 Features

### 👤 User Features

- User Registration
- Secure Login
- Logout
- Profile Page
- Edit Profile
- Change Password
- Password Strength Meter
- Google Authenticator (2FA)
- Order History
- Shopping Cart
- Checkout System

---

### 🍕 Product Features

- Browse Pizza & Drinks
- Product Search
- Add to Cart
- Quantity Management
- Remove Items
- Maximum Order Limits
  - 30 Pizzas
  - 40 Drinks
- Out of Stock Products

---

### 📦 Checkout

- Delivery Address
- Phone Number
- Order Notes
- Order Confirmation
- Order History

---

### 🔐 Security Features

- Password Hashing
- Prepared Statements
- SQL Injection Protection
- XSS Protection
- CSRF Protection
- Google reCAPTCHA v3
- Session Regeneration
- Login Rate Limiting
- Account Lock after Failed Attempts
- Google Authenticator Two-Factor Authentication

---

### 👨‍💼 Admin Panel

- Admin Dashboard
- View Users
- View Orders
- View Order Details
- Product Management
- Add Product
- Edit Product
- Delete Product
- Toggle Product Availability (In Stock / Out of Stock)
- Product Search

---

## 🛠 Technologies

- PHP 8
- MySQL
- Bootstrap 5
- HTML5
- CSS3
- JavaScript
- Google reCAPTCHA v3
- Google Authenticator (PragmaRX Google2FA)

---

## 📂 Project Structure

```
Pizzashop/
│
├── assets/
├── includes/
├── vendor/
│
├── index.php
├── menu.php
├── cart.php
├── checkout.php
├── profile.php
├── edit_profile.php
├── change_password.php
├── my_orders.php
│
├── login.php
├── register.php
├── logout.php
│
├── admin_dashboard.php
├── admin_products.php
├── admin_orders.php
├── admin_users.php
│
├── add_product.php
├── edit_product.php
├── delete_product.php
├── toggle_stock.php
│
├── enable_2fa.php
├── disable_2fa.php
├── verify_2fa.php
├── verify_password_2fa.php
│
├── csrf.php
├── security.php
├── recaptcha.php
└── config.php
```

---

## 🚀 Installation

1. Clone the repository.

```bash
git clone https://github.com/yourusername/Pizzashop.git
```

2. Import the SQL database into MySQL.

3. Configure `config.php`.

4. Install dependencies.

```bash
composer install
```

5. Start Apache and MySQL using XAMPP.

6. Open:

```
http://localhost/Pizzashop
```

---

## 🔑 Default Admin

Create a normal user and set the following field manually in the database:

```
is_admin = 1
```

---

## 📷 Screenshots

- Home
- Menu
- Shopping Cart
- Checkout
- User Profile
- Admin Dashboard
- Product Management
- Orders Management

---

## 👨‍💻 Author

Mohammad Hossein Shams Yousefi

University of Messina

Web Programming & Security Project