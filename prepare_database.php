<?php

$host = 'database';
$port = '3306';
$charset = 'utf8';
$db_name = $username = $password = 'lamp';
$dsn = "mysql:host={$host};port={$port};charset={$charset};dbname={$db_name}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    die;
}

// Create a 'listings' table if needed
if (table_exists($pdo, 'listings') === 0) {
    $listings_table_creation_query = "CREATE TABLE `listings` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` LONGTEXT NULL,
        `salary` VARCHAR(45) NULL,
        `tags` VARCHAR(255) NULL,
        `company` VARCHAR(255) NULL,
        `address` VARCHAR(255) NULL,
        `city` VARCHAR(45) NULL,
        `state` VARCHAR(45) NULL,
        `phone` VARCHAR(45) NULL,
        `email` VARCHAR(45) NULL,
        `requirements` LONGTEXT NULL,
        `benefits` LONGTEXT NULL,
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(`id`)
    );";

    create_table($pdo, $listings_table_creation_query);
}

// Create a 'users' table if needed
if (table_exists($pdo, 'users') === 0) {
    $users_table_creation_query = "CREATE TABLE `users` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `city` VARCHAR(45) NULL,
        `state` VARCHAR(45) NULL,
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(`id`)
    );";

    create_table($pdo, $users_table_creation_query);
}

// Add relationship between tables by user id field
if (table_exists($pdo, 'listings') === 1 && table_exists($pdo, 'users') === 1) {
    try {
        // Check index
        $index_exists = $pdo->query("SHOW INDEX FROM `listings` WHERE Key_name = 'fk_listings_users_idx'");
        if ($index_exists->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `listings` ADD INDEX `fk_listings_users_idx` (`user_id` ASC) VISIBLE;");
            echo "The index fk_listings_users was successfully added.";
        } else {
            echo "The index fk_listings_users already exists.";
        }

        // Check the presence of an foreign key
        $fk_exists = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = '{$db_name}' AND TABLE_NAME = 'listings' AND CONSTRAINT_NAME = 'fk_listings_users'");
        if ($fk_exists->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `listings` ADD CONSTRAINT `fk_listings_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
            echo "The foreign key fk_listings_users was successfully added.";
        } else {
            echo "The foreign key fk_listings_users already exists.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

/**
 * Check if the table exists in the database
 *
 * @param PDO $pdo PDO object for interacting with the database
 * @param string $table_name
 * @return int returns 1 if the table exists, 0 if the table does not exist, and -1 if an error occurs
 */
function table_exists($pdo, $table_name) {
    try {
        $result = $pdo->query("SHOW TABLES LIKE '$table_name'");
        if ($result->rowCount() > 0) {
            return 1;  // The table exists
        }
        return 0;  // The table doesn't exist
    } catch (PDOException $e) {
        // Error
        return -1;
    }
}

/**
 * Create a table in the database
 *
 * @param PDO $pdo
 * @param string $sql
 * @return void
 */
function create_table($pdo, $sql) {
    try {
        $pdo->exec($sql);
        inspect($sql);
        echo "Table has been created";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}