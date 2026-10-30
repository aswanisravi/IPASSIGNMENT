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
        `student_id` VARCHAR(50) NULL UNIQUE,
        `first_name` VARCHAR(100) NULL,
        `last_name` VARCHAR(100) NULL,
        `name` VARCHAR(200) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `address` TEXT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `password` VARCHAR(255) NOT NULL,
        `role` VARCHAR(20) DEFAULT 'Student',
        `reset_code` VARCHAR(20) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sqlUsers);

    // Auto-migrate missing columns if table existed previously
    $colsCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'student_id'");
    if ($colsCheck && $colsCheck->num_rows === 0) {
        @$conn->query("ALTER TABLE users ADD COLUMN student_id VARCHAR(50) NULL UNIQUE AFTER id");
        @$conn->query("ALTER TABLE users ADD COLUMN first_name VARCHAR(100) NULL AFTER student_id");
        @$conn->query("ALTER TABLE users ADD COLUMN last_name VARCHAR(100) NULL AFTER first_name");
        @$conn->query("ALTER TABLE users ADD COLUMN address TEXT NULL AFTER email");
        @$conn->query("ALTER TABLE users ADD COLUMN reset_code VARCHAR(20) NULL AFTER role");
    }

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
        $demoPass = password_hash('Password123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (student_id, first_name, last_name, name, email, address, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $sid = '1001';
            $fname = 'Alex';
            $lname = 'Morgan';
            $name = 'Alex Morgan';
            $email = 'alex@gmail.com';
            $addr = '123 Campus Lane, University Town';
            $phone = '9876543210';
            $role = 'Student';
            $stmt->bind_param("sssssssss", $sid, $fname, $lname, $name, $email, $addr, $phone, $demoPass, $role);
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
        student_id TEXT UNIQUE,
        first_name TEXT,
        last_name TEXT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        address TEXT,
        phone TEXT DEFAULT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'Student',
        reset_code TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // Auto-migrate missing columns in SQLite if table already exists
    $cols = $pdo->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC);
    $existingCols = array_column($cols, 'name');
    if (!in_array('student_id', $existingCols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN student_id TEXT;");
    }
    if (!in_array('first_name', $existingCols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN first_name TEXT;");
    }
    if (!in_array('last_name', $existingCols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_name TEXT;");
    }
    if (!in_array('address', $existingCols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN address TEXT;");
    }
    if (!in_array('reset_code', $existingCols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_code TEXT;");
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT NOT NULL,
        category TEXT DEFAULT 'General Inquiry',
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // Backfill any existing rows with student_id if null
    $pdo->exec("UPDATE users SET student_id = '1001', first_name = 'Alex', last_name = 'Morgan', address = '123 University Campus Lane' WHERE id = 1 AND (student_id IS NULL OR student_id = '');");
    $pdo->exec("UPDATE users SET student_id = '1002', first_name = 'Aswani', last_name = 'Ravi', address = 'College Hostel Block A' WHERE id = 2 AND (student_id IS NULL OR student_id = '');");

    // Seed default demo user if empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $row = $stmt->fetch();
    if ($row && $row['count'] == 0) {
        $demoPass = password_hash('Password123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (student_id, first_name, last_name, name, email, address, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->execute(['1001', 'Alex', 'Morgan', 'Alex Morgan', 'alex@gmail.com', '123 University Campus Lane', '9876543210', $demoPass, 'Student']);
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

