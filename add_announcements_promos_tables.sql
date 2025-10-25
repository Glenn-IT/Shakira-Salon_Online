-- Create announcements table
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create promos table
CREATE TABLE IF NOT EXISTS `promos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for announcements
INSERT INTO `announcements` (`title`, `content`, `type`, `status`) VALUES
('Welcome to Shakira Salon!', 'Book your appointment online and get the best service in town!', 'info', 'active'),
('New Services Available', 'We now offer hair coloring and rebonding services. Check out our services page!', 'success', 'active');

-- Insert sample data for promos
INSERT INTO `promos` (`title`, `description`, `discount_percentage`, `discount_amount`, `valid_from`, `valid_until`, `promo_code`, `image`, `status`) VALUES
('Grand Opening Sale', 'Enjoy 20% off on all services during our grand opening month!', 20, NULL, '2025-10-01', '2025-10-31', 'GRAND20', NULL, 'active'),
('Weekend Special', 'Get 15% discount on haircuts every Saturday and Sunday!', 15, NULL, '2025-10-01', '2025-12-31', 'WEEKEND15', NULL, 'active');
