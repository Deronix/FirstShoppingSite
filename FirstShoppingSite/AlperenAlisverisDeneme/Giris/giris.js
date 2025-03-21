// Toggle password visibility
function toggleLoginPasswordVisibility() {
  const passwordField = document.getElementById("login_password");
  const toggleIcon = document.getElementById("toggle-login-password");

  if (passwordField.type === "password") {
    passwordField.type = "text";
    toggleIcon.classList.remove("bx-show");
    toggleIcon.classList.add("bx-hide");
  } else {
    passwordField.type = "password";
    toggleIcon.classList.remove("bx-hide");
    toggleIcon.classList.add("bx-show");
  }
}

// Form validation
document
  .getElementById("loginForm")
  .addEventListener("submit", function (event) {
    const usernameField = document.getElementById("login_username");
    const passwordField = document.getElementById("login_password");
    const usernameStatus = document.getElementById("login-username-status");
    const passwordStatus = document.getElementById("login-password-status");

    let valid = true;

    // Check if username is empty
    if (usernameField.value.trim() === "") {
      valid = false;
      usernameStatus.textContent = "Kullanıcı Adı gereklidir.";
    } else {
      usernameStatus.textContent = "";
    }

    // Check if password is empty
    if (passwordField.value.trim() === "") {
      valid = false;
      passwordStatus.textContent = "Şifre gereklidir.";
    } else {
      passwordStatus.textContent = "";
    }

    // If not valid, prevent form submission
    if (!valid) {
      event.preventDefault();
    }
  });
