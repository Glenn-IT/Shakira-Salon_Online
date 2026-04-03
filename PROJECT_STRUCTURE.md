# 📂 Project Structure — Shakira Salon Online Appointment System

A full breakdown of every file and folder in the project.

---

## 🗂️ Root Directory

```
Shakira_Salon/
│
├── 📄 index.php                    # Public homepage — shows announcements, promos & services
├── 📄 login.php                    # User login with brute-force attempt logging & security alerts
├── 📄 register.php                 # New user registration with email confirmation (PHPMailer)
├── 📄 logout.php                   # Destroys session and redirects to login
├── 📄 forgot_password.php          # Password recovery via security question
│
├── 📄 dashboard.php                # Logged-in client dashboard (overview)
├── 📄 book_appointment.php         # Appointment booking form (service, stylist, date, time)
├── 📄 booking_history.php          # Client's booking history with status indicators
├── 📄 gcash_payment.php            # GCash payment proof upload for bookings
│
├── 📄 gallery.php                  # Public before/after transformation gallery
├── 📄 hairstyle.php                # Hairstyle listings/showcase page
├── 📄 services.php                 # Public services listing page
├── 📄 service.php                  # Individual service detail page
├── 📄 contact.php                  # Contact form for clients to message the salon
├── 📄 announcement.php             # Public announcements page
├── 📄 business_hours_client.php    # Displays salon business hours to clients
├── 📄 view_hairstylist.php         # Public hairstylist profile viewer
│
├── 📄 admin_home.php               # Admin landing / home page
├── 📄 admin_dashboard.php          # Admin dashboard with stats (users, bookings, revenue)
├── 📄 manage_bookings.php          # Admin: approve/cancel appointments + email notifications
├── 📄 manage_users.php             # Admin: view, edit, deactivate, delete user accounts
├── 📄 manage_announcements.php     # Admin: create/edit/delete announcements and promos
├── 📄 gallery_admin.php            # Admin: upload and manage gallery before/after photos
├── 📄 admin_messages.php           # Admin: view messages submitted via contact form
├── 📄 edit_user.php                # Admin: edit user profile details
├── 📄 delete_user.php              # Admin: delete a user account
├── 📄 insert.php                   # Admin: insert/seed data handler
│
├── 📄 config.php                   # Global PDO database connection configuration
├── 📄 email_security.php           # Login attempt logging & admin security alert emails
├── 📄 dashboard_data.php           # AJAX endpoint — feeds data to admin dashboard widgets
├── 📄 get_chart_data.php           # AJAX endpoint — returns chart data (bookings/revenue)
├── 📄 sales_data.php               # AJAX endpoint — sales/revenue statistics
│
├── 📄 check_email.php              # AJAX: checks if an email is already registered
├── 📄 check_fullname.php           # AJAX: checks if a full name is already registered
├── 📄 check_setup.php              # Utility: checks/validates DB setup requirements
├── 📄 check_user_columns.php       # Utility: verifies required columns exist in users table
├── 📄 fix_user_columns.php         # Utility: auto-migrates legacy columns (cp_number → contact_number)
├── 📄 debug_user_data.php          # Dev utility: outputs raw user data for debugging
├── 📄 test_security.php            # Dev utility: tests security/email alert configuration
│
├── 📄 shakira_salon.sql            # Database SQL dump (root-level copy)
├── 📄 composer.json                # Composer config — declares PHPMailer dependency
├── 📄 composer.lock                # Composer lock file (exact package versions)
├── 📄 gcash.jpg                    # GCash QR code image for payment instructions
│
├── 📄 README.md                    # Project overview, setup guide, and feature list
├── 📄 PROJECT_STRUCTURE.md         # This file — full project file/folder documentation
├── 📄 SECURITY_SETUP_README.md     # Security configuration notes and setup guide
├── 📄 ANNOUNCEMENTS_PROMOS_README.md  # Guide for managing announcements and promotions
├── 📄 USER_DATA_FIX_README.md      # Instructions for fixing legacy user data issues
```

---

## 📁 `assets/` — Shared Static Assets

```
assets/
├── css/
│   └── shared.css          # Global stylesheet (CSS variables, layout, nav, shared components)
└── images/
    ├── fade.webp            # Decorative background image
    ├── layered.webp         # Decorative background image
    └── salon-banner.webp    # Hero/banner image for the salon
```

---

## 📁 `images/` — Static Salon Images

```
images/
├── admin_profile.webp      # Default admin profile picture
├── download.jpg            # General-use image
├── salon-bg.jpg            # Salon background image
│
├── before/                 # Before-transformation photos
│   ├── coloring_before.jpg
│   ├── DEN_6915.jpg
│   ├── haircut.jpg
│   ├── makeup_before.jpg
│   ├── rebond_before.jpg
│   └── IMG_*.jpg           # Additional client before photos
│
└── after/                  # After-transformation photos
    ├── coloring_after.jpg
    ├── DEN_6915.jpg
    ├── haircut.jpg
    ├── makeup_after.jpg
    ├── rebond_after.jpg
    └── IMG_*.jpg           # Additional client after photos
```

---

## 📁 `uploads/` — User-Uploaded Files

```
uploads/
├── payment_proofs/         # GCash payment proof images uploaded by clients
├── promos/                 # Promotional banner images uploaded by admin
└── services/               # Service thumbnail images uploaded by admin
```

---

## 📁 `db/` — Database Files

```
db/
└── shakira_salon.sql       # Primary MySQL/MariaDB database dump
```

---

## 📁 `vendor/` — Composer Dependencies

```
vendor/
├── autoload.php            # Composer autoloader entry point
├── composer/               # Composer internal metadata
└── phpmailer/              # PHPMailer library (email sending via Gmail SMTP)
    └── phpmailer/
        ├── src/
        │   ├── PHPMailer.php
        │   ├── SMTP.php
        │   └── Exception.php
        └── ...
```

---

## 🗃️ Database Tables Overview

| Table            | Description                                          |
| ---------------- | ---------------------------------------------------- |
| `users`          | Registered users (customers & admins)                |
| `appointments`   | All booked appointments with status & payment info   |
| `bookings`       | Legacy/alternative bookings table                    |
| `services`       | Salon services with names and prices                 |
| `hairstylists`   | Stylist profiles (name, phone, email, role)          |
| `gallery`        | Before/after gallery entries with image paths        |
| `announcements`  | Active salon announcements displayed on homepage     |
| `promos`         | Active promotions with validity date ranges          |
| `login_attempts` | Logs all login attempts (for brute-force protection) |
| `messages`       | Contact form submissions from clients                |

---

## 🔄 Application Flow

```
Client Visit
    │
    ├──► index.php          (Homepage — view services, promos, announcements)
    ├──► register.php       (Create account → welcome email sent)
    ├──► login.php          (Authenticate → redirect to dashboard or admin panel)
    │
    ├── [Client Logged In]
    │       ├──► dashboard.php
    │       ├──► book_appointment.php  → stores to appointments table
    │       ├──► booking_history.php   → reads from appointments table
    │       ├──► gcash_payment.php     → uploads payment proof
    │       └──► logout.php
    │
    └── [Admin Logged In]
            ├──► admin_dashboard.php   → stats & charts
            ├──► manage_bookings.php   → approve/cancel + email client
            ├──► manage_users.php      → user management
            ├──► manage_announcements.php
            ├──► gallery_admin.php
            ├──► admin_messages.php
            └──► logout.php
```

---

## 📦 Dependencies

| Package               | Version | Purpose                       |
| --------------------- | ------- | ----------------------------- |
| `phpmailer/phpmailer` | ^6.10   | Sending emails via Gmail SMTP |

> Install with: `composer install`

---

## 🌐 URL Structure (Local)

| URL                                                  | Page             |
| ---------------------------------------------------- | ---------------- |
| `http://localhost/Shakira_Salon/`                    | Homepage         |
| `http://localhost/Shakira_Salon/login.php`           | Login            |
| `http://localhost/Shakira_Salon/register.php`        | Registration     |
| `http://localhost/Shakira_Salon/dashboard.php`       | Client Dashboard |
| `http://localhost/Shakira_Salon/admin_dashboard.php` | Admin Dashboard  |
| `http://localhost/Shakira_Salon/gallery.php`         | Gallery          |
| `http://localhost/Shakira_Salon/contact.php`         | Contact          |
