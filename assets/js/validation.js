/**
 * Form Validation & UI Interactivity Script
 * IP Assignment: Responsive PHP Web Application
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

  // 2. Helper validation functions
  const isValidEmail = (email) => {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(String(email).trim());
  };

  const setInputState = (input, isValid, message = '') => {
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

  const clearInputState = (input) => {
    input.classList.remove('is-valid', 'is-invalid');
    const parent = input.closest('.form-group');
    const feedback = parent ? parent.querySelector('.validation-feedback') : null;
    if (feedback) {
      feedback.textContent = '';
      feedback.className = 'validation-feedback';
    }
  };

  // 3. Login Form Validation (Name, Email, Password)
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    const nameInput = document.getElementById('loginName');
    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');

    // Live validation on blur & input
    if (nameInput) {
      nameInput.addEventListener('blur', function () {
        if (!this.value.trim()) {
          setInputState(this, false, 'Full Name is required.');
        } else if (this.value.trim().length < 2) {
          setInputState(this, false, 'Name must be at least 2 characters.');
        } else {
          setInputState(this, true, 'Valid name format.');
        }
      });
      nameInput.addEventListener('input', function () {
        if (this.classList.contains('is-invalid')) {
          if (this.value.trim().length >= 2) {
            setInputState(this, true);
          }
        }
      });
    }

    if (emailInput) {
      emailInput.addEventListener('blur', function () {
        if (!this.value.trim()) {
          setInputState(this, false, 'Email Address is required.');
        } else if (!isValidEmail(this.value)) {
          setInputState(this, false, 'Please enter a valid email address.');
        } else {
          setInputState(this, true, 'Valid email format.');
        }
      });
      emailInput.addEventListener('input', function () {
        if (this.classList.contains('is-invalid') && isValidEmail(this.value)) {
          setInputState(this, true);
        }
      });
    }

    if (passwordInput) {
      passwordInput.addEventListener('blur', function () {
        if (!this.value) {
          setInputState(this, false, 'Password is required.');
        } else if (this.value.length < 4) {
          setInputState(this, false, 'Password must be at least 4 characters.');
        } else {
          setInputState(this, true);
        }
      });
      passwordInput.addEventListener('input', function () {
        if (this.classList.contains('is-invalid') && this.value.length >= 4) {
          setInputState(this, true);
        }
      });
    }

    // Submit Validation
    loginForm.addEventListener('submit', function (e) {
      let hasError = false;

      if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
        setInputState(nameInput, false, 'Please enter your registered Name.');
        hasError = true;
      }
      if (!emailInput.value.trim() || !isValidEmail(emailInput.value)) {
        setInputState(emailInput, false, 'Please enter a valid Email ID.');
        hasError = true;
      }
      if (!passwordInput.value || passwordInput.value.length < 4) {
        setInputState(passwordInput, false, 'Please enter your password.');
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = loginForm.closest('.form-card');
        if (card) {
          card.classList.remove('shake');
          void card.offsetWidth; // trigger reflow
          card.classList.add('shake');
        }
      }
    });

    // Autofill Demo credentials helper
    window.fillDemoCredentials = function (name, email, pass) {
      if (nameInput) nameInput.value = name;
      if (emailInput) emailInput.value = email;
      if (passwordInput) passwordInput.value = pass;
      setInputState(nameInput, true, 'Demo Name loaded');
      setInputState(emailInput, true, 'Demo Email loaded');
      setInputState(passwordInput, true);
    };
  }

  // 4. Registration Form Validation
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    const regName = document.getElementById('regName');
    const regEmail = document.getElementById('regEmail');
    const regPhone = document.getElementById('regPhone');
    const regPassword = document.getElementById('regPassword');
    const regConfirm = document.getElementById('regConfirmPassword');

    if (regName) {
      regName.addEventListener('blur', function () {
        if (!this.value.trim()) {
          setInputState(this, false, 'Full Name is required.');
        } else if (this.value.trim().length < 3) {
          setInputState(this, false, 'Name must be at least 3 characters.');
        } else if (!/^[a-zA-Z\s]+$/.test(this.value.trim())) {
          setInputState(this, false, 'Name should contain letters only.');
        } else {
          setInputState(this, true, 'Name looks great!');
        }
      });
    }

    if (regEmail) {
      regEmail.addEventListener('blur', function () {
        if (!this.value.trim()) {
          setInputState(this, false, 'Email is required.');
        } else if (!isValidEmail(this.value)) {
          setInputState(this, false, 'Enter a valid email address.');
        } else {
          setInputState(this, true, 'Email format is valid.');
        }
      });
    }

    if (regPhone) {
      regPhone.addEventListener('blur', function () {
        const cleaned = this.value.replace(/[\s-]/g, '');
        if (!cleaned) {
          setInputState(this, false, 'Phone number is required.');
        } else if (!/^\d{10}$/.test(cleaned)) {
          setInputState(this, false, 'Enter a valid 10-digit phone number.');
        } else {
          setInputState(this, true, 'Valid phone number.');
        }
      });
    }

    if (regPassword) {
      regPassword.addEventListener('input', function () {
        if (this.value.length < 6) {
          setInputState(this, false, 'Password must be at least 6 characters.');
        } else {
          setInputState(this, true, 'Password strength: Good');
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
        setInputState(regConfirm, true, 'Passwords match perfectly!');
        return true;
      }
    }

    if (regConfirm) {
      regConfirm.addEventListener('input', checkPasswordsMatch);
      regConfirm.addEventListener('blur', checkPasswordsMatch);
    }

    registerForm.addEventListener('submit', function (e) {
      let hasError = false;

      if (!regName.value.trim() || regName.value.trim().length < 3) {
        setInputState(regName, false, 'Please provide a valid full name.');
        hasError = true;
      }
      if (!regEmail.value.trim() || !isValidEmail(regEmail.value)) {
        setInputState(regEmail, false, 'Please provide a valid email.');
        hasError = true;
      }
      const phoneClean = regPhone.value.replace(/[\s-]/g, '');
      if (!phoneClean || !/^\d{10}$/.test(phoneClean)) {
        setInputState(regPhone, false, 'Please enter a valid 10-digit phone number.');
        hasError = true;
      }
      if (!regPassword.value || regPassword.value.length < 6) {
        setInputState(regPassword, false, 'Password must be at least 6 characters.');
        hasError = true;
      }
      if (!checkPasswordsMatch()) {
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        const card = registerForm.closest('.form-card');
        if (card) {
          card.classList.remove('shake');
          void card.offsetWidth;
          card.classList.add('shake');
        }
      }
    });
  }

  // 5. Contact Form Validation
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
      if (!contactEmail.value.trim() || !isValidEmail(contactEmail.value)) {
        setInputState(contactEmail, false, 'Please enter a valid email.');
        hasError = true;
      }
      if (!contactSubject.value.trim() || contactSubject.value.trim().length < 3) {
        setInputState(contactSubject, false, 'Please enter a message subject.');
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

