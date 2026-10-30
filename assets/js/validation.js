/**
 * Client-Side Live Validation & Responsive UI Controls
 * Real-time feedback as the user types and leaves input fields.
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Menu Toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const navMenu = document.getElementById('navMenu');

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', function () {
      const isOpen = navMenu.classList.toggle('open');
      mobileToggle.setAttribute('aria-expanded', isOpen);
    });
  }

  // 2. Helper State Formatter
  const setFieldState = (input, isValid, errorMsg = '', successMsg = '') => {
    if (!input) return;
    const parent = input.closest('.form-group');
    if (!parent) return;

    let errorElem = parent.querySelector('.field-error');
    let successElem = parent.querySelector('.field-success');

    if (!errorElem) {
      errorElem = document.createElement('span');
      errorElem.className = 'field-error';
      parent.appendChild(errorElem);
    }

    if (!successElem) {
      successElem = document.createElement('span');
      successElem.className = 'field-success';
      parent.appendChild(successElem);
    }

    if (isValid) {
      input.classList.remove('is-invalid');
      input.classList.add('is-valid');
      errorElem.textContent = '';
      errorElem.style.display = 'none';
      if (successMsg) {
        successElem.textContent = successMsg;
        successElem.style.display = 'block';
      } else {
        successElem.style.display = 'none';
      }
    } else {
      input.classList.remove('is-valid');
      input.classList.add('is-invalid');
      successElem.textContent = '';
      successElem.style.display = 'none';
      errorElem.textContent = errorMsg;
      errorElem.style.display = 'block';
    }
  };

  const clearFieldState = (input) => {
    if (!input) return;
    input.classList.remove('is-valid', 'is-invalid');
    const parent = input.closest('.form-group');
    if (parent) {
      const errorElem = parent.querySelector('.field-error');
      const successElem = parent.querySelector('.field-success');
      if (errorElem) errorElem.style.display = 'none';
      if (successElem) successElem.style.display = 'none';
    }
  };

  // 3. Validation Logic for Page 1: Login (index.php)
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    const studentIdInput = document.getElementById('login_student_id');
    const passwordInput = document.getElementById('login_password');

    if (studentIdInput) {
      studentIdInput.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setFieldState(this, false, 'Student ID is required.');
        } else if (!/^\d+$/.test(val)) {
          setFieldState(this, false, 'Student ID must contain numbers only.');
        } else {
          setFieldState(this, true);
        }
      });
    }

    if (passwordInput) {
      passwordInput.addEventListener('input', function () {
        if (!this.value) {
          setFieldState(this, false, 'Password is required.');
        } else {
          setFieldState(this, true);
        }
      });
    }

    loginForm.addEventListener('submit', function (e) {
      let hasError = false;
      const sid = studentIdInput ? studentIdInput.value.trim() : '';
      const pwd = passwordInput ? passwordInput.value : '';

      if (!sid) {
        setFieldState(studentIdInput, false, 'Student ID is required.');
        hasError = true;
      } else if (!/^\d+$/.test(sid)) {
        setFieldState(studentIdInput, false, 'Student ID must contain numbers only.');
        hasError = true;
      }

      if (!pwd) {
        setFieldState(passwordInput, false, 'Password is required.');
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = loginForm.closest('.portal-card');
        if (card) {
          card.classList.remove('shake');
          void card.offsetWidth;
          card.classList.add('shake');
        }
      }
    });
  }

  // 4. Validation Logic for Page 2: Create Account (register.php)
  const regForm = document.getElementById('registerForm');
  if (regForm) {
    const sId = document.getElementById('reg_student_id');
    const fName = document.getElementById('reg_first_name');
    const lName = document.getElementById('reg_last_name');
    const email = document.getElementById('reg_email');
    const pwd = document.getElementById('reg_password');
    const cpwd = document.getElementById('reg_confirm_password');

    // Rule 1: Student ID: numbers only (0-9), 6 to 12 digits
    if (sId) {
      sId.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setFieldState(this, false, 'Student ID is required.');
        } else if (!/^\d+$/.test(val)) {
          setFieldState(this, false, 'Student ID must contain numbers only (0-9). No letters or symbols.');
        } else if (val.length < 6 || val.length > 12) {
          setFieldState(this, false, `Student ID must be 6 to 12 digits (currently ${val.length}).`);
        } else {
          setFieldState(this, true, '', 'Valid Student ID format');
        }
      });
    }

    // Rule 2: First Name: required, letters only
    if (fName) {
      fName.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setFieldState(this, false, 'First name is required.');
        } else if (!/^[a-zA-Z\s]+$/.test(val)) {
          setFieldState(this, false, 'First name must contain letters only.');
        } else {
          setFieldState(this, true);
        }
      });
    }

    // Rule 3: Last Name: optional, but if filled letters only
    if (lName) {
      lName.addEventListener('input', function () {
        const val = this.value.trim();
        if (val && !/^[a-zA-Z\s]+$/.test(val)) {
          setFieldState(this, false, 'Last name must contain letters only.');
        } else {
          clearFieldState(this);
        }
      });
    }

    // Rule 4: Email: must end with @gmail.com, part before @ must be lowercase only (no caps lock)
    if (email) {
      email.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setFieldState(this, false, 'Email is required.');
        } else if (!val.endsWith('@gmail.com')) {
          setFieldState(this, false, 'Email must end with @gmail.com');
        } else {
          const prefix = val.slice(0, val.indexOf('@'));
          if (!prefix) {
            setFieldState(this, false, 'Username before @gmail.com cannot be empty.');
          } else if (/[A-Z]/.test(prefix)) {
            setFieldState(this, false, 'No capital letters allowed before @gmail.com. Please use lowercase only.');
          } else if (!/^[a-z0-9._%+-]+$/.test(prefix)) {
            setFieldState(this, false, 'Invalid characters in email username.');
          } else {
            setFieldState(this, true, '', 'Valid lowercase Gmail address');
          }
        }
      });
    }

    // Rule 5: Password: at least 8 chars, 1 capital, 1 small, 1 number
    const checkPasswordRules = (val) => {
      if (!val || val.length < 8) {
        return 'Password must be at least 8 characters long.';
      }
      if (!/[A-Z]/.test(val)) {
        return 'Password must contain at least one capital letter (A-Z).';
      }
      if (!/[a-z]/.test(val)) {
        return 'Password must contain at least one small letter (a-z).';
      }
      if (!/[0-9]/.test(val)) {
        return 'Password must contain at least one number (0-9).';
      }
      return '';
    };

    if (pwd) {
      pwd.addEventListener('input', function () {
        const err = checkPasswordRules(this.value);
        if (err) {
          setFieldState(this, false, err);
        } else {
          setFieldState(this, true, '', 'Password meets all security rules');
        }

        if (cpwd && cpwd.value) {
          checkMatch();
        }
      });
    }

    // Rule 6: Confirm password must match
    function checkMatch() {
      if (!cpwd.value) {
        setFieldState(cpwd, false, 'Please confirm your password.');
        return false;
      }
      if (pwd.value !== cpwd.value) {
        setFieldState(cpwd, false, 'Passwords do not match.');
        return false;
      }
      setFieldState(cpwd, true, '', 'Passwords match');
      return true;
    }

    if (cpwd) {
      cpwd.addEventListener('input', checkMatch);
    }

    regForm.addEventListener('submit', function (e) {
      let hasError = false;

      // Student ID check
      const sidVal = sId ? sId.value.trim() : '';
      if (!sidVal || !/^\d{6,12}$/.test(sidVal)) {
        setFieldState(sId, false, 'Student ID must be 6 to 12 digits, numbers only.');
        hasError = true;
      }

      // First Name check
      const fnVal = fName ? fName.value.trim() : '';
      if (!fnVal || !/^[a-zA-Z\s]+$/.test(fnVal)) {
        setFieldState(fName, false, 'First name is required and must contain letters only.');
        hasError = true;
      }

      // Last Name check
      const lnVal = lName ? lName.value.trim() : '';
      if (lnVal && !/^[a-zA-Z\s]+$/.test(lnVal)) {
        setFieldState(lName, false, 'Last name must contain letters only.');
        hasError = true;
      }

      // Email check
      const emailVal = email ? email.value.trim() : '';
      if (!emailVal || !emailVal.endsWith('@gmail.com')) {
        setFieldState(email, false, 'Must be a valid @gmail.com address.');
        hasError = true;
      } else {
        const prefix = emailVal.slice(0, emailVal.indexOf('@'));
        if (/[A-Z]/.test(prefix)) {
          setFieldState(email, false, 'Lowercase letters only before @gmail.com.');
          hasError = true;
        }
      }

      // Password check
      const pwdErr = checkPasswordRules(pwd ? pwd.value : '');
      if (pwdErr) {
        setFieldState(pwd, false, pwdErr);
        hasError = true;
      }

      // Confirm check
      if (!checkMatch()) {
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = regForm.closest('.portal-card');
        if (card) {
          card.classList.remove('shake');
          void card.offsetWidth;
          card.classList.add('shake');
        }
      }
    });
  }

  // 5. Validation Logic for Page 5: Forgot Password (forgot.php)
  const forgotForm = document.getElementById('forgotForm');
  if (forgotForm) {
    const fEmail = document.getElementById('forgot_email');
    const fCode = document.getElementById('reset_code');
    const fNewPwd = document.getElementById('reset_password');
    const fConfPwd = document.getElementById('reset_confirm');

    if (fEmail) {
      fEmail.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val || !val.endsWith('@gmail.com') || /[A-Z]/.test(val.slice(0, val.indexOf('@')))) {
          setFieldState(this, false, 'Enter a valid lowercase @gmail.com address.');
        } else {
          setFieldState(this, true);
        }
      });
    }

    if (fCode) {
      fCode.addEventListener('input', function () {
        const val = this.value.trim();
        if (!/^\d{6}$/.test(val)) {
          setFieldState(this, false, 'Verification code must be exactly 6 digits.');
        } else {
          setFieldState(this, true);
        }
      });
    }

    if (fNewPwd) {
      fNewPwd.addEventListener('input', function () {
        const val = this.value;
        if (val.length < 8 || !/[A-Z]/.test(val) || !/[a-z]/.test(val) || !/[0-9]/.test(val)) {
          setFieldState(this, false, 'Min 8 chars, at least 1 capital letter, 1 small letter, and 1 number.');
        } else {
          setFieldState(this, true);
        }
      });
    }
  }
});
