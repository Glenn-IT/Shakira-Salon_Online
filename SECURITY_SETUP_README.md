# ✅ Security Alert System - READY TO USE!

## 🎯 Current Status

- **Database Setup**: ✅ COMPLETED - `login_attempts` table exists
- **PHPMailer**: ✅ INSTALLED - Available in `vendor/phpmailer/`
- **Security Code**: ✅ INTEGRATED - All files updated
- **Testing Script**: ✅ CREATED - Run `test_security.php` to verify

---

## 🚀 Quick Start - Only Email Setup Needed!

### ⚡ STEP 1: Configure Your Gmail (5 minutes)

In `email_security.php`, update these 2 lines:

```php
$mail->Username   = 'your-actual-gmail@gmail.com';    // ← PUT YOUR GMAIL HERE
$mail->Password   = 'your-16-character-app-password'; // ← PUT YOUR APP PASSWORD HERE
```

```php
$mail->setFrom('your-actual-gmail@gmail.com', 'Shakira Salon Security'); // ← SAME GMAIL HERE
```

### ⚡ STEP 2: Get Gmail App Password

1. **Enable 2-Factor Authentication** on your Gmail
2. Go to **Google Account Settings** → **Security** → **App passwords**
3. Generate password for "Mail" → Copy the 16-character code
4. Paste it in the `$mail->Password` line above

### ⚡ STEP 3: Test It!

1. Open: `http://localhost/Shakira Salon_Online/test_security.php`
2. Try wrong admin login 3 times
3. Check your email for security alert!

---

First, you need to add the login attempts tracking table to your database:

1. Open phpMyAdmin or your MySQL management tool
2. Select the `shakira_salon` database
3. Run the SQL script from `add_login_attempts_table.sql` file

OR you can run this command in MySQL:

```sql
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email_time` (`email`, `attempt_time`),
  KEY `idx_ip_time` (`ip_address`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## 2. Email Configuration

You need to configure the email settings in `email_security.php`:

### For Gmail (Recommended):

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate an App Password**:

   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a new app password for "Mail"
   - Copy the 16-character password

3. **Update email_security.php**:

   ```php
   $mail->Username   = 'your-actual-gmail@gmail.com';    // Your Gmail address
   $mail->Password   = 'your-16-char-app-password';      // The app password you generated
   ```

4. **Update the "From" address**:
   ```php
   $mail->setFrom('your-actual-gmail@gmail.com', 'Shakira Salon Security');
   ```

### Alternative Email Providers:

- **Outlook/Hotmail**: Use `smtp-mail.outlook.com`, port 587
- **Yahoo**: Use `smtp.mail.yahoo.com`, port 587
- **Custom SMTP**: Use your hosting provider's SMTP settings

## 3. How It Works

### Features:

- **Tracks all login attempts** (successful and failed)
- **Monitors admin account specifically** for security threats
- **Sends email alert after 3 failed attempts** on admin account
- **Includes detailed information**: IP address, browser, timestamp
- **Auto-cleanup**: Removes old attempt records (24 hours)
- **Dynamic admin email**: Uses the current admin's email from database

### Security Benefits:

- **Real-time alerts** when someone tries to access admin account
- **IP tracking** to identify potential attackers
- **Browser fingerprinting** for additional security context
- **Prevents brute force attacks** by alerting admins
- **Database logging** for security audit trails

### Email Triggers:

- Triggered **only for admin role** login attempts
- Sent after **3 consecutive failed attempts** within 30 minutes
- Email goes to the **admin's current email** in the database
- If admin changes email in database, notifications update automatically

## 4. Testing

1. Try logging into an admin account with wrong password 3 times
2. Check if the email notification is received
3. Verify the email contains correct IP address and timestamp
4. Check the `login_attempts` table in database for logged attempts

## 5. Customization Options

### Change attempt threshold:

In `login.php`, modify this line:

```php
if ($failedAttempts >= 3) {  // Change 3 to your preferred number
```

### Change time window:

In `login.php`, modify this line:

```php
$failedAttempts = getRecentFailedAttempts($pdo, $email, 30); // 30 minutes
```

### Change cleanup frequency:

In `email_security.php`, modify this line:

```php
cleanOldAttempts($pdo, 24); // 24 hours
```

## 6. Troubleshooting

### Email not sending:

1. Check Gmail app password is correct
2. Verify Gmail account has 2FA enabled
3. Check server error logs for PHPMailer errors
4. Test with a simple PHPMailer script first

### Database errors:

1. Ensure the `login_attempts` table exists
2. Check database connection in `config.php`
3. Verify table permissions

### Feature not working:

1. Check if `email_security.php` is included properly
2. Verify admin user exists in database with `role = 'admin'`
3. Test with actual failed login attempts
