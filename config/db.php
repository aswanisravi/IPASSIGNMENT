<?php
/**
 * Database Connection & Abstraction Layer
 * IP Assignment: Responsive PHP Web Application
 * 
 * Supports MySQL / MariaDB (standard for XAMPP / WAMP)
 * with graceful SQLite auto-fallback so the app runs instantly out-of-the-box.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// MySQL Configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ip_assignment_db');

// Global connection state
$db_driver = 'none';
$db_error = null;
$pdo_conn = null;
$mysqli_conn = null;

function get_db_connection() {
    global $db_driver, $db_error, $pdo_conn, $mysqli_conn;

    if ($pdo_conn !== null) {
        return ['type' => 'pdo', 'conn' => $pdo_conn, 'driver' => $db_driver];
    }
    if ($mysqli_conn !== null) {
        return ['type' => 'mysqli', 'conn' => $mysqli_conn, 'driver' => $db_driver];
    }

    // 1. Try MySQL via mysqli first (Standard XAMPP / WAMP)
    if (extension_loaded('mysqli')) {
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
        if (!$conn->connect_errno) {
            // Check / create database
            $createDbSql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            @$conn->query($createDbSql);
            if (@$conn->select_db(DB_NAME)) {
                $mysqli_conn = $conn;
                $db_driver = 'MySQL';
                init_mysql_tables($mysqli_conn);
                return ['type' => 'mysqli', 'conn' => $mysqli_conn, 'driver' => 'MySQL'];
            }
        }
    }

    // 2. Fallback to PDO SQLite if MySQL is not currently running
    if (extension_loaded('pdo_sqlite')) {
        try {
            $sqlitePath = __DIR__ . '/../database.sqlite';
            $pdo = new PDO('sqlite:' . $sqlitePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo_conn = $pdo;
            $db_driver = 'SQLite (Auto-Fallback)';
            init_sqlite_tables($pdo_conn);
            return ['type' => 'pdo', 'conn' => $pdo_conn, 'driver' => $db_driver];
        } catch (Exception $e) {
            $db_error = $e->getMessage();
        }
    }

    $db_error = "Could not establish database connection (MySQL or SQLite). Please check server configuration.";
    return ['type' => 'none', 'conn' => null, 'driver' => 'None', 'error' => $db_error];
}

/**
 * Initialize MySQL tables if they don't exist
 */
function init_mysql_tables($conn) {
    $sqlUsers = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `phone` VARCHAR(20) DEFAULT NULL,
        `password` VARCHAR(255) NOT NULL,
        `role` VARCHAR(20) DEFAULT 'Student',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sqlUsers);

    $sqlMessages = "CREATE TABLE IF NOT EXISTS `messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `subject` VARCHAR(200) NOT NULL,
        `category` VARCHAR(50) DEFAULT 'General Inquiry',
        `message` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sqlMessages);

    // Seed default demo user if empty
    $check = $conn->query("SELECT id FROM users LIMIT 1");
    if ($check && $check->num_rows === 0) {
        $demoPass = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $name = 'Alex Morgan';
            $email = 'alex@example.com';
            $phone = '9876543210';
            $role = 'Student';
            $stmt->bind_param("sssss", $name, $email, $phone, $demoPass, $role);
            $stmt->execute();
            $stmt->close();
        }
    }
}

/**
 * Initialize SQLite tables if using SQLite fallback
 */
function init_sqlite_tables($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        phone TEXT DEFAULT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'Student',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT NOT NULL,
        category TEXT DEFAULT 'General Inquiry',
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // Seed default demo user if empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $row = $stmt->fetch();
    if ($row && $row['count'] == 0) {
        $demoPass = password_hash('password123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $ins->execute(['Alex Morgan', 'alex@example.com', '9876543210', $demoPass, 'Student']);
    }
}

/**
 * Helper: Execute a parameterized SELECT and return single row
 */
function db_fetch_one($sql, $params = []) {
    $db = get_db_connection();
    if ($db['type'] === 'pdo') {
        $stmt = $db['conn']->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } elseif ($db['type'] === 'mysqli') {
        $stmt = prepare_mysqli_stmt($db['conn'], $sql, $params);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row;
        }
    }
    return false;
}

/**
 * Helper: Execute a parameterized SELECT and return all rows
 */
function db_fetch_all($sql, $params = []) {
    $db = get_db_connection();
    if ($db['type'] === 'pdo') {
        $stmt = $db['conn']->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } elseif ($db['type'] === 'mysqli') {
        $stmt = prepare_mysqli_stmt($db['conn'], $sql, $params);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        }
    }
    return [];
}

/**
 * Helper: Execute an INSERT/UPDATE/DELETE statement
 */
function db_execute($sql, $params = []) {
    $db = get_db_connection();
    if ($db['type'] === 'pdo') {
        $stmt = $db['conn']->prepare($sql);
        $res = $stmt->execute($params);
        return [
            'success' => $res,
            'insert_id' => $db['conn']->lastInsertId(),
            'affected_rows' => $stmt->rowCount()
        ];
    } elseif ($db['type'] === 'mysqli') {
        $stmt = prepare_mysqli_stmt($db['conn'], $sql, $params);
        if ($stmt) {
            $res = $stmt->execute();
            $insert_id = $stmt->insert_id;
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            return [
                'success' => $res,
                'insert_id' => $insert_id,
                'affected_rows' => $affected_rows
            ];
        }
        return ['success' => false, 'error' => $db['conn']->error];
    }
    return ['success' => false, 'error' => 'No database connection available'];
}

/**
 * Internal helper to bind params for MySQLi
 */
function prepare_mysqli_stmt($conn, $sql, $params) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    return $stmt;
}

/**
 * Helper: Get current database status name for display
 */
function db_status_badge() {
    $db = get_db_connection();
    if ($db['type'] !== 'none') {
        return '<span class="status-badge connected"><span class="dot"></span> DB: ' . htmlspecialchars($db['driver']) . '</span>';
    }
    return '<span class="status-badge error"><span class="dot"></span> DB: Disconnected</span>';
}

