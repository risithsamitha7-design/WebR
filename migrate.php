<?php
// GlobeTrek Adventures - Database Migration Script
require_once __DIR__ . '/config.php';

echo "<h2>Starting Database Migration...</h2>";

try {
    // 1. Create Lookup Tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `hotels` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(150) NOT NULL,
            `type` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `price_per_night` DECIMAL(10, 2) NOT NULL,
            `image` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p class='text-success'>✓ Table `hotels` created or already exists.</p>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `vehicles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `model` VARCHAR(150) NOT NULL,
            `type` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `price_per_day` DECIMAL(10, 2) NOT NULL,
            `image` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p class='text-success'>✓ Table `vehicles` created or already exists.</p>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `tour_guides` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(150) NOT NULL,
            `languages` VARCHAR(255) NOT NULL,
            `rating` DECIMAL(3, 2) NOT NULL,
            `price_per_day` DECIMAL(10, 2) NOT NULL,
            `image` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p class='text-success'>✓ Table `tour_guides` created or already exists.</p>";

    // 2. Add relational columns to bookings if they do not exist
    $columns = [
        'assigned_hotel_id' => "ALTER TABLE `bookings` ADD COLUMN `assigned_hotel_id` INT DEFAULT NULL",
        'assigned_vehicle_id' => "ALTER TABLE `bookings` ADD COLUMN `assigned_vehicle_id` INT DEFAULT NULL",
        'assigned_guide_id' => "ALTER TABLE `bookings` ADD COLUMN `assigned_guide_id` INT DEFAULT NULL"
    ];

    foreach ($columns as $col => $sql) {
        $check = $pdo->query("SHOW COLUMNS FROM `bookings` LIKE '$col'")->fetchAll();
        if (empty($check)) {
            $pdo->exec($sql);
            echo "<p class='text-success'>✓ Column `$col` added to `bookings` table.</p>";
        } else {
            echo "<p class='text-muted'>Column `$col` already exists in `bookings` table.</p>";
        }
    }

    // 3. Add Foreign Key constraints if not already added
    // Helper to check if constraint exists
    try {
        $pdo->exec("ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_hotels` FOREIGN KEY (`assigned_hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL");
        echo "<p class='text-success'>✓ Foreign key constraint `fk_bookings_hotels` added.</p>";
    } catch (PDOException $ex) {
        echo "<p class='text-muted'>Foreign key constraint `fk_bookings_hotels` already exists or could not be added.</p>";
    }

    try {
        $pdo->exec("ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_vehicles` FOREIGN KEY (`assigned_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL");
        echo "<p class='text-success'>✓ Foreign key constraint `fk_bookings_vehicles` added.</p>";
    } catch (PDOException $ex) {
        echo "<p class='text-muted'>Foreign key constraint `fk_bookings_vehicles` already exists or could not be added.</p>";
    }

    try {
        $pdo->exec("ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_guides` FOREIGN KEY (`assigned_guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE SET NULL");
        echo "<p class='text-success'>✓ Foreign key constraint `fk_bookings_guides` added.</p>";
    } catch (PDOException $ex) {
        echo "<p class='text-muted'>Foreign key constraint `fk_bookings_guides` already exists or could not be added.</p>";
    }

    // 4. Seed Default Lookups if empty
    $hotelsCount = $pdo->query("SELECT COUNT(*) FROM `hotels`")->fetchColumn();
    if ($hotelsCount == 0) {
        $pdo->exec("
            INSERT INTO `hotels` (`id`, `name`, `type`, `description`, `price_per_night`, `image`) VALUES
            (1, 'Grand Negombo Beach Resort', 'Luxury & Spa', 'Located directly on the gold sands of Kudapaduwa, offering panoramic ocean views, infinity pools, and world-class local seafood dining.', 18000.00, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'),
            (2, 'Ella Hills Nature Canopy', 'Nature Retreat', 'Nestled amongst misty tea plantations. Wake up to majestic views of Ella Rock and enjoy walking trails right from your luxury wood cabin door.', 14000.00, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80'),
            (3, 'Yala Wildlife Safari Lodges', 'Wild & Safari', 'Premium eco-glamping tents positioned on the border of Yala National Park. Sleep under the stars and hear the calls of wild elephants.', 22000.00, 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80')
        ");
        echo "<p class='text-success'>✓ Seeded default hotels data.</p>";
    }

    $vehiclesCount = $pdo->query("SELECT COUNT(*) FROM `vehicles`")->fetchColumn();
    if ($vehiclesCount == 0) {
        $pdo->exec("
            INSERT INTO `vehicles` (`id`, `model`, `type`, `description`, `price_per_day`, `image`) VALUES
            (1, 'Toyota Prius / Axio', 'Private Sedan Car', 'Guaranteed direct private airport transfer or island touring. Fuel efficient, comfortable, with dual-zone climate control.', 8500.00, 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80'),
            (2, 'Toyota KDH Passenger Van', 'Spacious Passenger Van', 'Perfect for families or small groups. High roof, dual A/C, reclining seats, fits up to 10 passengers with luggage.', 15000.00, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80'),
            (3, 'Mitsubishi Montero SUV', 'Luxury SUV', 'Premium high-clearance SUV. Perfect for rougher terrain, tea country explorations, and traveling in ultimate luxury.', 25000.00, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80')
        ");
        echo "<p class='text-success'>✓ Seeded default vehicles data.</p>";
    }

    $guidesCount = $pdo->query("SELECT COUNT(*) FROM `tour_guides`")->fetchColumn();
    if ($guidesCount == 0) {
        $pdo->exec("
            INSERT INTO `tour_guides` (`id`, `name`, `languages`, `rating`, `price_per_day`, `image`) VALUES
            (1, 'Asanka Fernando', 'English, German', 4.90, 3500.00, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80'),
            (2, 'Nimal Siriwardena', 'English, Italian, Japanese', 4.80, 4500.00, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=800&q=80'),
            (3, 'Priyantha Bandara', 'English, Russian', 4.95, 4000.00, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80')
        ");
        echo "<p class='text-success'>✓ Seeded default tour guides data.</p>";
    }

    echo "<h3>Migration Completed Successfully!</h3>";

} catch (PDOException $e) {
    echo "<h3 class='text-danger'>Migration Failed: " . $e->getMessage() . "</h3>";
}
?>
