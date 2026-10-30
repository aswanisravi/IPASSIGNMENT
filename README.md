# Student Login Portal - Web Application

A responsive PHP & MySQL student portal web application with full form validation, interactive authentication, digital student identity cards, and self-service password reset.

---

## 🌟 Application Workflow & Architecture

### 1. Homepage (`index.php`)
- **Logo & Portal Branding:** Official University Crest & Golden Cap (`#C9A227`).
- **Centered Login Box:**
  - **Student ID:** Numeric field (digits 0-9 only).
  - **Student Name:** Optional name field for registered students.
  - **Password:** Password field.
  - **Forgot Password Link:** Takes users to the email verification code page.
  - **Validation States:**
    - If Student ID is not found: Displays *"Student ID not found in database."*
    - If Password is incorrect: Specifically displays *"Wrong password."*
    - If Valid: Displays green success badge and redirects to the Student Dashboard.
  - **Create Account Link:** Direct link to registration for new students.

### 2. Student Registration (`register.php`)
- **Student ID:** Full numbers only (digits 0-9 enforced via client regex & server PHP).
- **First Name:** **Mandatory** (letters and spaces only).
- **Last Name:** **Optional** (letters and spaces only).
- **Gmail Address:**
  - Must end with `@gmail.com`.
  - Username before `@gmail.com` must have **NO caps lock** (lowercase only).
- **Address:** Street / Campus Hostel Address.
- **Password:**
  - Alphanumeric, digits 0-9.
  - Must contain at least one **capital letter (A-Z)** and at least one **small letter (a-z)**.
- **Confirm Password:** Must match password.

### 3. Registration Success Page (`registration_success.php`)
- Displays green celebration message: *"You Have Registered Successfully!"*
- Summary card showing:
  - Student ID
  - First Name & Last Name
  - Gmail Address
  - Residential Address
  - Registration Date & Time
- **Next Action Button:** *"Proceed to Student Portal & Digital ID Card ➔"* leading directly to the Dashboard.

### 4. Student Dashboard & Digital ID Card (`dashboard.php`)
- **Official Digital Student ID Card:**
  - University crest and gold-bordered header.
  - Student photo avatar.
  - Full Name, Student ID, Department, Gmail, and Campus Address.
  - Barcode and QR code simulation.
  - 1-click **Print Student ID Card** button.
- **Academic Modules:**
  - Enrolled courses with attendance tracking.
  - Upcoming internal exams and library book status.

### 5. Forgot Password Flow (`forgot_password.php`)
- **Step 1:** Enter registered `@gmail.com` address.
- **Verification Code:** Automatically generates a 6-digit numerical code and stores it in database/session.
- **Demo Simulation Badge:** Displays the 6-digit code on-screen for instant testing without an external mail server.
- **Step 2:** Enter verification code, set new password (meeting the capital + small + number condition), and confirm.
- **Success:** Updates the database and allows logging in immediately with the new password.

---

## 🔑 Demo Accounts for Immediate Testing

| Role | Student ID | Name | Gmail ID | Password |
| :--- | :--- | :--- | :--- | :--- |
| **Demo Student 1** | `1001` | `Alex Morgan` | `alex@gmail.com` | `Password123` |
| **Demo Student 2** | `1002` | `Aswani Ravi` | `aswani@gmail.com` | `Password123` |

---

## 🚀 How to Run Locally

```powershell
# Open terminal in project folder:
cd "c:\Users\LOQ\Desktop\webpage php"

# Start the PHP server:
php -S localhost:8000
```
Open your browser at **`http://localhost:8000`**.
