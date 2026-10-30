/**
 * Form Validation & UI Interactivity Script
 * Student Login Portal & Registration Verification
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Navigation Toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const navMenu = document.getElementById('navMenu');

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', function () {
      navMenu.classList.toggle('open');
      const isOpen = navMenu.classList.contains('open');
      mobileToggle.setAttribute('aria-expanded', isOpen);
      mobileToggle.innerHTML = isOpen ? '&#10005;' : '&#9776;';
    });
  }

  // Helper validation state handler
  const setInputState = (input, isValid, message = '') => {
    if (!input) return;
    const parent = input.closest('.form-group');
    const feedback = parent ? parent.querySelector('.validation-feedback') : null;

    if (isValid) {
      input.classList.remove('is-invalid');
      input.classList.add('is-valid');
      if (feedback) {
        feedback.className = 'validation-feedback success';
        feedback.textContent = message || 'Looks good!';
      }
    } else {
      input.classList.remove('is-valid');
      input.classList.add('is-invalid');
      if (feedback) {
        feedback.className = 'validation-feedback error';
        feedback.textContent = message;
      }
    }
  };

  // 2. Homepage Student Login Validation
  const homeLoginForm = document.getElementById('homeLoginForm');
  if (homeLoginForm) {
    const studentIdInput = document.getElementById('studentId');
    const passwordInput = document.getElementById('studentPassword');

    if (studentIdInput) {
      studentIdInput.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setInputState(this, false, 'Student ID is required.');
        } else if (!/^[0-9]+$/.test(val)) {
          setInputState(this, false, 'Student ID must contain numbers only (0-9).');
        } else {
          setInputState(this, true, 'Valid numeric ID format.');
        }
      });
    }

    if (passwordInput) {
      passwordInput.addEventListener('blur', function () {
        if (!this.value) {
          setInputState(this, false, 'Password is required.');
        } else {
          setInputState(this, true);
        }
      });
    }

    homeLoginForm.addEventListener('submit', function (e) {
      let hasError = false;
      const sId = studentIdInput.value.trim();

      if (!sId || !/^[0-9]+$/.test(sId)) {
        setInputState(studentIdInput, false, 'Student ID must be numbers only.');
        hasError = true;
      }
      if (!passwordInput.value) {
        setInputState(passwordInput, false, 'Please enter your password.');
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = homeLoginForm.closest('.student-login-card') || homeLoginForm;
        card.classList.remove('shake');
        void card.offsetWidth;
        card.classList.add('shake');
      }
    });
  }

  // 3. Student Registration Form Validation
  const regForm = document.getElementById('studentRegisterForm');
  if (regForm) {
    const regStudentId = document.getElementById('regStudentId');
    const regFirstName = document.getElementById('regFirstName');
    const regLastName = document.getElementById('regLastName');
    const regEmail = document.getElementById('regEmail');
    const regAddress = document.getElementById('regAddress');
    const regPassword = document.getElementById('regPassword');
    const regConfirm = document.getElementById('regConfirmPassword');

    // Rule: Student ID full numbers, all should numbers
    if (regStudentId) {
      regStudentId.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setInputState(this, false, 'Student ID is required.');
        } else if (!/^[0-9]+$/.test(val)) {
          setInputState(this, false, 'Numbers only: Student ID cannot contain letters or symbols.');
        } else {
          setInputState(this, true, 'Valid numerical Student ID.');
        }
      });
    }

    // Rule: First Name is mandatory
    if (regFirstName) {
      regFirstName.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setInputState(this, false, 'First Name is mandatory.');
        } else if (!/^[a-zA-Z\s]+$/.test(val)) {
          setInputState(this, false, 'First Name should contain letters only.');
        } else {
          setInputState(this, true, 'Looks good!');
        }
      });
    }

    // Rule: Last Name is optional
    if (regLastName) {
      regLastName.addEventListener('input', function () {
        const val = this.value.trim();
        if (val && !/^[a-zA-Z\s]+$/.test(val)) {
          setInputState(this, false, 'Last Name should contain letters only.');
        } else if (val) {
          setInputState(this, true, 'Optional Last Name accepted.');
        }
      });
    }

    // Rule: @gmail.com is necessary. Before that, name should be written with NO CAPS LOCK
    if (regEmail) {
      regEmail.addEventListener('input', function () {
        const val = this.value.trim();
        if (!val) {
          setInputState(this, false, 'Email is required.');
        } else if (!val.toLowerCase().endsWith('@gmail.com')) {
          setInputState(this, false, '@gmail.com is necessary! Must end with @gmail.com.');
        } else {
          const usernamePart = val.split('@')[0];
          if (/[A-Z]/.test(usernamePart)) {
            setInputState(this, false, 'No caps lock! Username before @gmail.com must be lowercase only.');
          } else if (!/^[a-z0-9._%+-]+@gmail\.com$/.test(val)) {
            setInputState(this, false, 'Please enter a valid lowercase Gmail address.');
          } else {
            setInputState(this, true, 'Valid Gmail address (no caps lock).');
          }
        }
      });
    }

    // Rule: Address required
    if (regAddress) {
      regAddress.addEventListener('blur', function () {
        if (!this.value.trim()) {
          setInputState(this, false, 'Address is required.');
        } else {
          setInputState(this, true);
        }
      });
    }

    // Rule: Password condition: alphanumeric, 0 to 9, capital letter and small letter
    const validatePasswordFormat = (pwd) => {
      const hasUpper = /[A-Z]/.test(pwd);
      const hasLower = /[a-z]/.test(pwd);
      const hasNumber = /[0-9]/.test(pwd);
      return {
        isValid: hasUpper && hasLower && hasNumber && pwd.length >= 6,
        hasUpper,
        hasLower,
        hasNumber
      };
    };

    if (regPassword) {
      regPassword.addEventListener('input', function () {
        const pwd = this.value;
        const res = validatePasswordFormat(pwd);

        if (!pwd) {
          setInputState(this, false, 'Password is required.');
        } else if (pwd.length < 6) {
          setInputState(this, false, 'Password must be at least 6 characters.');
        } else if (!res.hasUpper) {
          setInputState(this, false, 'Must include at least one CAPITAL letter (A-Z).');
        } else if (!res.hasLower) {
          setInputState(this, false, 'Must include at least one SMALL letter (a-z).');
        } else if (!res.hasNumber) {
          setInputState(this, false, 'Must include at least one NUMBER (0-9).');
        } else {
          setInputState(this, true, 'Strong password meets all conditions!');
        }

        if (regConfirm && regConfirm.value) {
          checkPasswordsMatch();
        }
      });
    }

    function checkPasswordsMatch() {
      if (!regConfirm.value) {
        setInputState(regConfirm, false, 'Please confirm your password.');
        return false;
      }
      if (regPassword.value !== regConfirm.value) {
        setInputState(regConfirm, false, 'Passwords do not match.');
        return false;
      } else {
        setInputState(regConfirm, true, 'Passwords match!');
        return true;
      }
    }

    if (regConfirm) {
      regConfirm.addEventListener('input', checkPasswordsMatch);
    }

    // Registration Submit Event
    regForm.addEventListener('submit', function (e) {
      let hasError = false;

      // Validate ID
      const sid = regStudentId.value.trim();
      if (!sid || !/^[0-9]+$/.test(sid)) {
        setInputState(regStudentId, false, 'Student ID must contain numbers only.');
        hasError = true;
      }

      // Validate First Name
      if (!regFirstName.value.trim()) {
        setInputState(regFirstName, false, 'First Name is mandatory.');
        hasError = true;
      }

      // Validate Email
      const emailVal = regEmail.value.trim();
      if (!emailVal || !emailVal.toLowerCase().endsWith('@gmail.com') || /[A-Z]/.test(emailVal.split('@')[0])) {
        setInputState(regEmail, false, 'Must be a lowercase @gmail.com address with no caps lock.');
        hasError = true;
      }

      // Validate Address
      if (!regAddress.value.trim()) {
        setInputState(regAddress, false, 'Address is required.');
        hasError = true;
      }

      // Validate Password
      const pwdRes = validatePasswordFormat(regPassword.value);
      if (!pwdRes.isValid) {
        setInputState(regPassword, false, 'Must contain 0-9, capital letter and small letter.');
        hasError = true;
      }

      // Confirm match
      if (!checkPasswordsMatch()) {
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = regForm.closest('.form-card') || regForm;
        card.classList.remove('shake');
        void card.offsetWidth;
        card.classList.add('shake');
      }
    });
  }

  // 4. Contact Form Validation
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    const contactName = document.getElementById('contactName');
    const contactEmail = document.getElementById('contactEmail');
    const contactSubject = document.getElementById('contactSubject');
    const contactMessage = document.getElementById('contactMessage');

    contactForm.addEventListener('submit', function (e) {
      let hasError = false;

      if (!contactName.value.trim() || contactName.value.trim().length < 2) {
        setInputState(contactName, false, 'Please enter your name.');
        hasError = true;
      }
      if (!contactEmail.value.trim() || !contactEmail.value.includes('@')) {
        setInputState(contactEmail, false, 'Please enter a valid email.');
        hasError = true;
      }
      if (!contactSubject.value.trim() || contactSubject.value.trim().length < 3) {
        setInputState(contactSubject, false, 'Please enter a subject.');
        hasError = true;
      }
      if (!contactMessage.value.trim() || contactMessage.value.trim().length < 10) {
        setInputState(contactMessage, false, 'Message should be at least 10 characters.');
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = contactForm.closest('.form-card') || contactForm;
        card.classList.remove('shake');
        void card.offsetWidth;
        card.classList.add('shake');
      }
    });
  }
});
