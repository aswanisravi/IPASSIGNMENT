# Responsive Web Application - IP Assignment

A complete, responsive 5-page web application developed for the **Internet Programming (IP)** course assignment. Built with pure **PHP 8** on the backend, **MySQL / SQLite** database connectivity, client-side & server-side **Form Validation**, an interactive **3-field Login authentication system** (asking for **Name**, **Email ID**, and **Password** with both valid and invalid states), styled with a **White, Royal Navy Blue, and Metallic Gold** theme.

---

## 📋 Assignment Requirements & Implementation Matrix

| Requirement | Implementation Details | Status |
| :--- | :--- | :---: |
| **Responsive Webpage** | Mobile-first CSS3 Flexbox & Grid layouts, collapsible hamburger menu, cross-device tested (Mobile, Tablet, Laptop, Desktop). | ✅ Complete |
| **3 - 5 Web Pages** | 1. `index.php` (Home)<br>2. `login.php` (Login Portal)<br>3. `register.php` (User Sign Up)<br>4. `dashboard.php` (Protected Member Dashboard)<br>5. `contact.php` (Feedback & Inquiries) | ✅ 5 Pages |
| **PHP for Backend** | Native PHP 8 with session management, prepared statement queries, and modular headers/footers. | ✅ Complete |
| **Database Connectivity** | MySQL / MariaDB via MySQLi/PDO with parameterized queries, plus automatic zero-config SQLite fallback (`database.sqlite`). | ✅ Complete |
| **Form Validation** | Dual-tier validation: instant client-side JavaScript regex checking + strict server-side PHP validation with error feedback. | ✅ Complete |
| **Login with Name, Email & Password** | Specialized 3-field login checking **Full Name**, **Email ID**, and **Password** against database records. | ✅ Complete |
| **Valid and Invalid States** | **Valid State:** Green success badge, greeting message, and auto-redirect to dashboard.<br>**Invalid State:** Red alert, field highlight, error shake animation, and error diagnosis. | ✅ Complete |
| **White, Navy Blue & Gold Theme** | Clean white background (`#ffffff` / `#f8fafc`), deep navy blue structures (`#0a192f` / `#0f2744`), and gold accent buttons (`linear-gradient(135deg, #d4af37, #f39c12)`). | ✅ Complete |

---

## 🔑 Test Credentials (Pre-seeded in Database)

| Role | Full Name | Email ID | Password |
| :--- | :--- | :--- | :--- |
| **Student (Demo)** | `Alex Morgan` | `alex@example.com` | `password123` |
| **Administrator** | `Admin User` | `admin@example.com` | `password123` |

> 💡 **Quick Testing:** On the `login.php` page, you can click the **"Auto-Fill Valid"** button or **"Fill Invalid Demo"** button to immediately preview both states!

---

## 🚀 How to Run the Application

### Option 1: Instant Run using PHP Built-in Server (Recommended)
You do not need to install anything extra if PHP is installed on your computer.

1. Open PowerShell or Terminal in the project directory:
   ```powershell
   cd "c:\Users\LOQ\Desktop\webpage php"
   ```
2. Start the local server:
   ```powershell
   php -S localhost:8000
   ```
3. Open your browser and visit:
   ```
   http://localhost:8000
   ```

*(The application will automatically initialize the database and demo user accounts on first launch!)*

---

### Option 2: Running with XAMPP / WampServer (College / University Lab)

1. Copy the `webpage php` folder to your XAMPP web root directory:
   ```
   C:\xampp\htdocs\webpage-php
   ```
2. Open the **XAMPP Control Panel** and click **Start** for both **Apache** and **MySQL**.
3. Open your browser and go to `http://localhost/phpmyadmin/`.
4. Click **Import**, select `database.sql` from this folder, and click **Go**.
5. Visit the project in your browser:
   ```
   http://localhost/webpage-php/
   ```

---

## 📂 Project Structure

```
webpage php/
├── assets/
│   ├── css/
│   │   └── style.css            # Custom responsive styles (White, Navy Blue, Gold theme)
│   └── js/
│       └── validation.js        # Real-time form validation & mobile menu toggle
├── config/
│   └── db.php                  # Database connection, prepared statement helpers & SQLite auto-fallback
├── includes/
│   ├── header.php              # Meta tags, responsive viewport, page title
│   ├── navbar.php              # Sticky navy & gold responsive navbar with active indicator
│   └── footer.php              # Branded footer, live database status badge, tech stack
├── index.php                   # Page 1: Landing / Home page with feature showcase & CTA
├── login.php                   # Page 2: Login page asking for Name, Email ID, and Password (Valid & Invalid)
├── register.php                # Page 3: New student registration with real-time field validation
├── dashboard.php               # Page 4: Protected student portal displaying live DB records & stats
├── contact.php                 # Page 5: Contact Us & Feedback form with DB message storage
├── logout.php                  # Session destruction and flash message redirect
├── setup.php                   # Diagnostic tool & 1-click database re-seeder
├── database.sql                # Complete MySQL schema and demo data for phpMyAdmin
└── README.md                   # Project documentation & execution guide
```

---

## 🛡️ Form Validation Features

- **Full Name Validation:** Minimum 2-3 characters, alphabetic characters only.
- **Email ID Validation:** Standard RFC-compliant regex pattern match (`name@domain.tld`) + database uniqueness verification on registration.
- **Phone Number Validation:** 10-digit mobile number format.
- **Password Strength & Matching:** Minimum 6 characters on registration, real-time comparison between `password` and `confirm_password`.
- **Database Mismatch Handling:** Detects whether an authentication failure is caused by an unregistered email, a name mismatch, or an incorrect password.

