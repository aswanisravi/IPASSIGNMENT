<?php
/**
 * Database Connection via PDO
 * Connects to MySQL (Standard for XAMPP) with seamless SQLite fallback for local testing.
 */

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = null;

function get_pdo() {
    global $pdo;

    if ($pdo !== null) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 1. Attempt MySQL PDO Connection (Primary for XAMPP)
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Try creating the MySQL database if server is reachable
        try {
            $rootDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
            $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, $options);
            init_mysql_tables($pdo);
            return $pdo;
        } catch (Exception $ex) {
            // MySQL service not currently running; proceed to SQLite fallback
        }
    }

    // 2. Automatic SQLite PDO Fallback (Ensures project executes out-of-the-box)
    try {
        $sqlitePath = __DIR__ . '/../database.sqlite';
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, $options);
        init_sqlite_tables($pdo);
        return $pdo;
    } catch (PDOException $e) {
        die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
    }
}

/**
 * Initialize MySQL tables if they do not exist
 */
function init_mysql_tables($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(12) NOT NULL UNIQUE,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) DEFAULT '',
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS reset_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        code VARCHAR(6) NOT NULL,
        expires_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    seed_demo_account($pdo);
}

/**
 * Initialize SQLite tables if using fallback
 */
function init_sqlite_tables($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id TEXT NOT NULL UNIQUE,
        first_name TEXT NOT NULL,
        last_name TEXT DEFAULT '',
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS reset_codes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL,
        code TEXT NOT NULL,
        expires_at DATETIME NOT NULL
    );");

    seed_demo_account($pdo);
}

/**
 * Seed default demo account if table is empty
 */
function seed_demo_account($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM students");
    $row = $stmt->fetch();
    if ($row && $row['total'] == 0) {
        $demoPass = password_hash('Password123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?, ?)");
        $ins->execute(['20240101', 'Aswani', 'Ravi', 'aswani123@gmail.com', $demoPass]);
    }
}
