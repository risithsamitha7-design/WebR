-- GlobeTrek Adventures Database Schema and Seed Data

-- 1. Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('customer', 'staff', 'admin') NOT NULL DEFAULT 'customer',
  `contact_number` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default user accounts (Passwords: Admin123, Staff123, Customer123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `contact_number`) VALUES
(1, 'System Administrator', 'admin@globetrek.com', '$2y$10$m6v6tAbLX/EoBekhAVeqT.mR5YJC0uBLVA4lZ26qFx1yjvNrjRZk6', 'admin', '+94 31 222 1111'),
(2, 'Samantha Perera (Staff)', 'staff@globetrek.com', '$2y$10$eS0HNwWm0eQhUlLorKUuPesasqj39m/FdHFKSI/H3Lu2EIZ1ZWPNS', 'staff', '+94 77 123 4567'),
(3, 'John Doe (Customer)', 'customer@globetrek.com', '$2y$10$MKwnv3gjjJc06gl3Cv.SJ.anBP0uWihHAZqUBIxTU311f7zpW6HrG', 'customer', '+94 71 987 6543')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- 2. Table structure for table `packages`
CREATE TABLE IF NOT EXISTS `packages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `destination` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default travel packages
INSERT INTO `packages` (`id`, `title`, `description`, `destination`, `price`, `duration`, `image_url`) VALUES
(2, 'Sigiriya Fortress & Dambulla Cave Temple Tour', 'Scale the spectacular 8th wonder of the ancient world - the Sigiriya Lion Rock Fortress. Explore the ancient cave temples of Dambulla, featuring historic Buddhist murals. Includes premium resort lodging and transfers.', 'Sigiriya', 45000.00, '2 Days / 1 Night', 'uploads/sigiriya.png'),
(3, 'Ella Scenic Train Adventure & Tea Gardens', 'Ride the famous colonial-era blue train through Sri Lanka\'s breathtaking tea country. Visit the Nine Arch Bridge, hike up Little Adam\'s Peak, and enjoy pure Ceylon tea tastings in a historic plantation.', 'Ella', 75000.00, '3 Days / 2 Nights', 'uploads/ella.png'),
(4, 'Yala National Park Wild Elephant & Leopard Safari', 'Embark on an intense 4x4 wildlife safari in Yala National Park, home to the world\'s highest density of leopards. Encounter wild Asian elephants, sloths, and exotic birds. Includes luxury glamping and meals.', 'Yala', 54000.00, '2 Days / 1 Night', 'uploads/yala.png'),
(5, 'Southern Coast Beach Escape (Galle & Mirissa)', 'Relax on the golden sandy beaches of Mirissa, go blue whale watching, and walk the colonial ramparts of the historic Galle Dutch Fort. Perfect mix of relaxation and culture.', 'Galle & Mirissa', 96000.00, '4 Days / 3 Nights', 'uploads/beach.png')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 3. Table structure for table `hotels`
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `price_per_night` DECIMAL(10, 2) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default hotels
INSERT INTO `hotels` (`id`, `name`, `type`, `description`, `price_per_night`, `image`) VALUES
(1, 'Grand Negombo Beach Resort', 'Luxury & Spa', 'Located directly on the gold sands of Kudapaduwa, offering panoramic ocean views, infinity pools, and world-class local seafood dining.', 18000.00, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'),
(2, 'Ella Hills Nature Canopy', 'Nature Retreat', 'Nestled amongst misty tea plantations. Wake up to majestic views of Ella Rock and enjoy walking trails right from your luxury wood cabin door.', 14000.00, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80'),
(3, 'Yala Wildlife Safari Lodges', 'Wild & Safari', 'Premium eco-glamping tents positioned on the border of Yala National Park. Sleep under the stars and hear the calls of wild elephants.', 22000.00, 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 4. Table structure for table `vehicles`
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `model` VARCHAR(150) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `price_per_day` DECIMAL(10, 2) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default vehicles
INSERT INTO `vehicles` (`id`, `model`, `type`, `description`, `price_per_day`, `image`) VALUES
(1, 'Toyota Prius / Axio', 'Private Sedan Car', 'Guaranteed direct private airport transfer or island touring. Fuel efficient, comfortable, with dual-zone climate control.', 8500.00, 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80'),
(2, 'Toyota KDH Passenger Van', 'Spacious Passenger Van', 'Perfect for families or small groups. High roof, dual A/C, reclining seats, fits up to 10 passengers with luggage.', 15000.00, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80'),
(3, 'Mitsubishi Montero SUV', 'Luxury SUV', 'Premium high-clearance SUV. Perfect for rougher terrain, tea country explorations, and traveling in ultimate luxury.', 25000.00, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80')
ON DUPLICATE KEY UPDATE `model` = VALUES(`model`);

-- 5. Table structure for table `tour_guides`
CREATE TABLE IF NOT EXISTS `tour_guides` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `languages` VARCHAR(255) NOT NULL,
  `rating` DECIMAL(3, 2) NOT NULL,
  `price_per_day` DECIMAL(10, 2) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default guides
INSERT INTO `tour_guides` (`id`, `name`, `languages`, `rating`, `price_per_day`, `image`) VALUES
(1, 'Asanka Fernando', 'English, German', 4.90, 3500.00, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80'),
(2, 'Nimal Siriwardena', 'English, Italian, Japanese', 4.80, 4500.00, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=800&q=80'),
(3, 'Priyantha Bandara', 'English, Russian', 4.95, 4000.00, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 6. Table structure for table `bookings`
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `package_id` INT NOT NULL,
  `arrival_date` DATE NOT NULL,
  `departure_date` DATE NOT NULL,
  `num_adults` INT NOT NULL DEFAULT 1,
  `num_children_older` INT NOT NULL DEFAULT 0,
  `num_children_younger` INT NOT NULL DEFAULT 0,
  `num_rooms` INT NOT NULL DEFAULT 1,
  `special_requests` TEXT DEFAULT NULL,
  `contact_name` VARCHAR(150) NOT NULL,
  `contact_email` VARCHAR(150) NOT NULL,
  `contact_phone` VARCHAR(50) NOT NULL,
  `contact_country` VARCHAR(100) NOT NULL,
  `status` ENUM('pending', 'awaiting_payment', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
  `total_price` DECIMAL(10, 2) NOT NULL,
  `guide_required` TINYINT(1) DEFAULT 0,
  `guide_requirement` TINYINT(1) DEFAULT 0,
  `assigned_hotel_id` INT DEFAULT NULL,
  `assigned_vehicle_id` INT DEFAULT NULL,
  `assigned_guide_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table structure for table `queries`
CREATE TABLE IF NOT EXISTS `queries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'replied') NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default inquiries
INSERT INTO `queries` (`id`, `name`, `email`, `subject`, `message`, `status`) VALUES
(1, 'David Miller', 'david.m@example.com', 'Customized Honeymoon Package', 'Hello! We are looking for a customized 5-day honeymoon package in Ella and Mirissa for late August. We would like private transfers and candlelight dinner arrangements. Please let us know the pricing.', 'unread'),
(2, 'Sophia Wagner', 'sophia@example.com', 'Airport Transfer to Negombo Hotel', 'Does the Lagoon Safari package include free pickup from Bandaranaike International Airport (CMB) to our hotel in Kudapaduwa?', 'unread')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`);

-- 5. Table structure for table `payments`
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `transaction_id` VARCHAR(100) NOT NULL,
  `status` ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'completed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table structure for table `custom_itineraries`
CREATE TABLE IF NOT EXISTS `custom_itineraries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `destination` VARCHAR(150) NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `travel_date` DATE NOT NULL,
  `budget` DECIMAL(10, 2) NOT NULL,
  `special_requirements` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'reviewed', 'contacted') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
