-- Add contact_number column if it doesn't exist (for backward compatibility)
-- This ensures both old register code and new code work properly

ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `contact_number` VARCHAR(15) NULL AFTER `cp_number`;

-- Copy data from cp_number to contact_number for existing users
UPDATE `users` 
SET `contact_number` = `cp_number` 
WHERE `cp_number` IS NOT NULL AND (`contact_number` IS NULL OR `contact_number` = '');
