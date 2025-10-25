# User Data Auto-Population Fix

## Problem

The booking form was not auto-populating user data because the database had multiple phone column names (`phone_number`, `cp_number`, `phone`, `contact_number`), and the register form was using a different column name than what existed in the table.

## Solution Applied

### 1. **Updated book_appointment.php**

- Now checks ALL possible phone columns: `cp_number`, `contact_number`, `phone_number`, `phone`
- Uses the first non-empty value found
- Auto-populates: Name, Email, and Phone

### 2. **Fixed register.php**

- Changed from `contact_number` to `cp_number` to match existing table structure
- New registrations will now properly save phone numbers

### 3. **Database Column Fix**

- Created `fix_user_columns.php` - Run this file ONCE to add missing column if needed
- Created SQL script `add_contact_number_column.sql` for manual execution

## How to Fix Your Database

### Option 1: Automatic Fix (Recommended)

1. Open your browser
2. Go to: `http://localhost/Shakira%20Salon_Online/fix_user_columns.php`
3. The script will automatically add the missing column if needed
4. You're done!

### Option 2: Manual Fix (Using phpMyAdmin)

1. Open phpMyAdmin
2. Select `shakira_salon` database
3. Click on SQL tab
4. Copy and paste this SQL:

   ```sql
   ALTER TABLE `users`
   ADD COLUMN IF NOT EXISTS `contact_number` VARCHAR(15) NULL AFTER `cp_number`;

   UPDATE `users`
   SET `contact_number` = `cp_number`
   WHERE `cp_number` IS NOT NULL AND (`contact_number` IS NULL OR `contact_number` = '');
   ```

5. Click "Go"

## Testing

1. Login as a customer
2. Go to Book Appointment page
3. Your name, email, and phone should now be auto-filled!

## Files Modified

- ✅ `book_appointment.php` - Updated to fetch all phone columns
- ✅ `register.php` - Fixed to use correct column name
- ✅ `fix_user_columns.php` - Created automatic fix script
- ✅ `add_contact_number_column.sql` - Created SQL migration script
