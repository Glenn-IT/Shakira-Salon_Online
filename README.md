# 💇 Shakira Salon — Online Appointment Management System

An **Online Appointment Management System** for Shakira Salon, located at Tuao West, Cagayan. This web-based application allows clients to book hairstyling appointments online, view services and promotions, and manage their bookings — while giving admins full control over scheduling, users, and salon operations.

---

## 📌 Features

### 👤 Client-Side

- User **Registration & Login** with email verification
- **Book Appointments** — select service, stylist, date, and time slot
- **Booking History** — view past and upcoming appointments
- **GCash Payment** — upload payment proof for bookings
- **Forgot Password** — recovery via security question
- **Announcements & Promos** — view active deals on the homepage
- **Gallery** — browse before & after hairstyle photos
- **Services Page** — view all available salon services with pricing
- **Contact Page** — send messages to the salon
- **Business Hours** — view salon operating schedule

### 🛠️ Admin-Side

- **Admin Dashboard** — overview of total users, pending/approved bookings, revenue charts
- **Manage Bookings** — approve, cancel bookings; send email notifications automatically
- **Manage Users** — view, edit, deactivate/activate, or delete customer accounts
- **Manage Announcements & Promos** — create, edit, and remove announcements/promotions
- **Gallery Admin** — upload and manage before/after transformation photos
- **Sales Data** — view revenue trends and booking statistics
- **Admin Messages** — receive and view contact form messages
- **Security Alerts** — email notification on multiple failed admin login attempts

---

## 🛠️ Tech Stack

| Layer      | Technology                           |
| ---------- | ------------------------------------ |
| Backend    | PHP 8.x (PDO + MySQLi)               |
| Database   | MySQL / MariaDB (via XAMPP)          |
| Frontend   | HTML5, CSS3, Bootstrap 5.3           |
| Icons      | Font Awesome 6                       |
| Animations | AOS (Animate On Scroll), Animate.css |
| Charts     | Chart.js                             |
| Email      | PHPMailer 6.x (Gmail SMTP)           |
| Server     | Apache (XAMPP)                       |

---

## ⚙️ Requirements

- [XAMPP](https://www.apachefriends.org/) (PHP 8.0+ & MySQL/MariaDB)
- [Composer](https://getcomposer.org/) (for PHP dependencies)
- A Gmail account with an **App Password** enabled (for email sending)

---

## 🚀 Installation & Setup

### 1. Clone or Copy the Project

Place the project folder inside your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\Shakira_Salon\
```

### 2. Import the Database

1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Create a new database named `shakira_salon`
3. Import the SQL file:
   - Use `db/shakira_salon.sql` (preferred) **or** `shakira_salon.sql` in the root

### 3. Configure Database Connection

Edit `config.php` and set your credentials:

```php
$host = 'localhost';
$db   = 'shakira_salon';
$user = 'root';
$pass = ''; // Set your MySQL password if applicable
```

### 4. Install PHP Dependencies

Open a terminal in the project root and run:

```bash
composer install
```

This installs **PHPMailer** (defined in `composer.json`).

### 5. Configure Email (PHPMailer)

In `manage_bookings.php`, `register.php`, and other files that send emails, update:

```php
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password'; // Gmail App Password
$mail->setFrom('your_email@gmail.com', 'Shakira Salon');
```

> **Important:** Use a [Gmail App Password](https://myaccount.google.com/apppasswords), not your regular password.

### 6. Start the Application

1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Visit: `http://localhost/Shakira_Salon/`

---

## 👥 Default Roles

| Role       | Access                                      |
| ---------- | ------------------------------------------- |
| `admin`    | Full access to admin panel and all features |
| `customer` | Can book appointments, view history, pay    |

> To create an admin account, manually set the `role` column to `admin` in the `users` table via phpMyAdmin after registering.

---

## 📁 Key Directory Overview

```
Shakira_Salon/
├── config.php              # Database connection (PDO)
├── index.php               # Homepage (announcements, promos, services)
├── login.php               # Login with brute-force protection
├── register.php            # Registration with email confirmation
├── dashboard.php           # Client dashboard
├── book_appointment.php    # Appointment booking form
├── booking_history.php     # Client booking history
├── admin_dashboard.php     # Admin overview dashboard
├── manage_bookings.php     # Admin booking management
├── manage_users.php        # Admin user management
├── manage_announcements.php# Admin announcements & promos
├── gallery.php             # Public before/after gallery
├── gallery_admin.php       # Admin gallery upload management
├── assets/                 # CSS and shared assets
├── uploads/                # Uploaded images (promos, services, payments)
├── images/                 # Static salon images
├── vendor/                 # Composer dependencies (PHPMailer)
└── db/                     # Database SQL file
```

---

## 🔒 Security Features

- Passwords hashed with `password_hash()` (bcrypt)
- Login brute-force protection with attempt logging
- Admin email security alerts on repeated failed logins
- Prepared statements (PDO) to prevent SQL injection
- Session-based authentication with role checks
- Account deactivation support by admin

---

## 📧 Email Notifications

The system sends emails for:

- ✅ Successful registration (welcome email)
- ✅ Booking **approved** or **cancelled** by admin
- ✅ Admin **security alert** on multiple failed login attempts
- ✅ Password reset support

---

## 📜 License

This project is developed for academic/local business use. All rights reserved by the Shakira Salon development team.

---

## 🙋 Support

For issues or inquiries, contact the admin via the [Contact Page](contact.php) of the application.
