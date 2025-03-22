// Unified JavaScript for Product Details and Image Switching

function toggleProductDetails(type, productId) {
  const infoSection = document.getElementById(`info-${productId}`);
  const commentsSection = document.getElementById(`comments-${productId}`);
  const infoButton = document.querySelector(
    `.info-button[data-product-id='${productId}']`
  );
  const commentsButton = document.querySelector(
    `.comments-button[data-product-id='${productId}']`
  );
  const infoGroup = document.querySelector(
    `.info-group[data-product-id='${productId}']`
  );

  const sectionMap = { info: infoSection, comments: commentsSection };
  const buttonMap = { info: infoButton, comments: commentsButton };

  toggleSection(sectionMap[type], buttonMap[type]);

  const otherType = type === "info" ? "comments" : "info";
  sectionMap[otherType].style.display = "none";
  buttonMap[otherType].style.display = "inline-block";

  updateInfoGroup(infoButton, commentsButton, infoGroup);
}

function toggleSection(section, button) {
  if (section.style.display === "block") {
    section.style.display = "none";
    button.style.display = "inline-block";
  } else {
    section.style.display = "block";
    button.style.display = "none";
  }
}

function updateInfoGroup(infoButton, commentsButton, infoGroup) {
  if (
    infoButton.style.display === "none" ||
    commentsButton.style.display === "none"
  ) {
    infoGroup.classList.add("one-button");
  } else {
    infoGroup.classList.remove("one-button");
  }
}

function closeDetails(productId) {
  const infoSection = document.getElementById(`info-${productId}`);
  const commentsSection = document.getElementById(`comments-${productId}`);

  infoSection.style.display = "none";
  commentsSection.style.display = "none";

  document.querySelector(
    `.info-button[data-product-id='${productId}']`
  ).style.display = "inline-block";
  document.querySelector(
    `.comments-button[data-product-id='${productId}']`
  ).style.display = "inline-block";
}

const productSections = ["regular", "cart", "favorites"];

productSections.forEach((section) => {
  document
    .querySelectorAll(`.${section}-products, .${section}-items .product`)
    .forEach((product) => {
      const mainImage = product.querySelector(".main-image");
      const imageCount = product.querySelector(".image-count");
      const thumbnails = product.querySelectorAll(".thumbnail");

      thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener("click", () => {
          mainImage.src = thumbnail.src;
          if (imageCount) {
            imageCount.textContent = `${index + 1} / ${thumbnails.length}`;
          }
        });
      });
    });
});

function changeImage(section, productId, imageUrl, imageIndex) {
  const mainImage = document.getElementById(
    `main-image-${section}-${productId}`
  );
  const imageCount = document.getElementById(
    `image-count-${section}-${productId}`
  );

  if (mainImage) {
    mainImage.src = imageUrl;
  }
  if (imageCount) {
    imageCount.textContent = `${imageIndex} / 3`;
  }
}

function togglePasswordVisibility() {
  var passwordField = document.getElementById("password");
  passwordField.type = passwordField.type === "password" ? "text" : "password";
}

function formatPhoneNumber(event) {
  var input = event.target.value.replace(/\D/g, "");

  if (input.length > 0 && !input.startsWith("90")) {
    input = "90" + input;
  }

  if (input.length > 12) {
    input = input.substring(0, 12);
  }

  var formatted = "+" + input.slice(0, 2);
  if (input.length > 2) {
    formatted += " " + input.slice(2, 5);
  }
  if (input.length > 5) {
    formatted += " " + input.slice(5, 8);
  }
  if (input.length > 8) {
    formatted += " " + input.slice(8, 10);
  }
  if (input.length > 10) {
    formatted += " " + input.slice(10);
  }

  event.target.value = formatted;
}

function validateForm() {
  var username = document.getElementById("username").value;
  var email = document.getElementById("email").value;
  var password = document.getElementById("password").value;
  var phone = document.getElementById("phone").value.replace(/\D/g, "");
  var saveButton = document.getElementById("saveButton");

  var usernameRegex = /^[a-zA-Z0-9çÇğĞıİöÖşŞüÜ]{3,}$/;
  var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var passwordRegex =
    /^(?=.*[A-ZÇĞİÖŞÜ])(?=.*[a-zçğıöşü])(?=.*\d)(?=.*[@$!%*?&#_])[A-Za-zçğıöşüÇĞİÖŞÜ\d@$!%*?&#_]{8,}$/;

  var phoneRegex = /^\d{12}$/;

  validateField(username, usernameRegex, "username-status");
  validateField(email, emailRegex, "email-status");
  validateField(password, passwordRegex, "password-status");
  validateField(phone, phoneRegex, "phone-status");

  if (
    usernameRegex.test(username) &&
    emailRegex.test(email) &&
    passwordRegex.test(password) &&
    phoneRegex.test(phone)
  ) {
    saveButton.disabled = false;
  } else {
    saveButton.disabled = true;
  }
}

function validateField(value, regex, statusElementId) {
  var statusElement = document.getElementById(statusElementId);
  if (regex.test(value)) {
    statusElement.innerHTML =
      '<i class="bx bx-check-circle" style="color: green;"></i>';
    statusElement.className = "status-icon correct";
  } else {
    statusElement.innerHTML =
      '<i class="bx bx-x-circle" style="color: red;"></i>';
    statusElement.className = "status-icon incorrect";
  }
}

document.getElementById("userForm").addEventListener("input", validateForm);
document.getElementById("phone").addEventListener("input", formatPhoneNumber);

function showSuccessMessage() {
  var overlay = document.getElementById("overlay");
  overlay.style.display = "grid";
  setTimeout(function () {
    alert("Kayıt başarılı!");
    overlay.style.display = "none";
  }, 3000);
  var formContainer = document.querySelector(".form-container");
  formContainer.classList.add("hidden");
}

function submitForm() {
  document.getElementById("form").submit();
}

/////////////////////////////////////////////////////////////////////////////////////

// Update the current page number displayed in the pagination
const pageNumberDisplay = document.getElementById("current-page");
if (pageNumberDisplay) {
  pageNumberDisplay.textContent = `Page ${currentProductsPage + 1}`;
}

// Adjust layout based on the number of visible products
if (visibleProducts.length === 3) {
  productGrid.classList.remove("center-items");
} else {
  productGrid.classList.add("center-items");
}

regularProducts.forEach((product, index) => {
  product.style.display =
    index >= currentProductsPage * productsPerPage &&
    index < (currentProductsPage + 1) * productsPerPage
      ? "block"
      : "none";
});

document.addEventListener("DOMContentLoaded", () => {
  const welcomeMessage = document.getElementById("welcomeMessage");
  const shoppingSite = document.getElementById("product-grid-section");
  const productGrid = document.querySelector(".product-grid");
  const regularProducts = productGrid.querySelectorAll(".product");
  const productsPerPage = 3;
  const pageNumberDisplay = document.getElementById("current-page");
  let currentProductsPage = 0;

  const updateRegularProductVisibility = () => {
    regularProducts.forEach((product, index) => {
      product.style.display =
        index >= currentProductsPage * productsPerPage &&
        index < (currentProductsPage + 1) * productsPerPage
          ? "block"
          : "none";
    });

    if (pageNumberDisplay) {
      pageNumberDisplay.textContent = `Page ${currentProductsPage + 1}`;
    }
  };

  const initializePaginationControls = () => {
    const nextAllButton = document.getElementById("next-all-button");
    const prevAllButton = document.getElementById("prev-all-button");

    nextAllButton?.addEventListener("click", () => {
      if (
        (currentProductsPage + 1) * productsPerPage <
        regularProducts.length
      ) {
        currentProductsPage++;
        updateRegularProductVisibility();
      }
    });

    prevAllButton?.addEventListener("click", () => {
      if (currentProductsPage > 0) {
        currentProductsPage--;
        updateRegularProductVisibility();
      }
    });
  };

  // Add to Cart button
  document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", () => {
      const productId = button.getAttribute("data-product-id");
      sendActionRequest("cart", productId);
    });
  });

  // Add to Favorites button
  document.querySelectorAll(".add-to-favorites").forEach((button) => {
    button.addEventListener("click", () => {
      const productId = button.getAttribute("data-product-id");
      sendActionRequest("favorites", productId);
    });
  });

  setTimeout(() => {
    welcomeMessage.style.display = "none"; // Hide welcome message
    shoppingSite.style.display = "flex"; // Show shopping site
    updateRegularProductVisibility(); // Update product visibility for first page
  }, 2000);

  initializePaginationControls();

  function sendActionRequest(action, productId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "path_to_your_php_script.php", true); // Replace with the correct path to your PHP script

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
      const response = JSON.parse(xhr.responseText);
      alert(response.message);
      updateRegularProductVisibility(); // Refresh the product visibility after the action
    };
    xhr.send(`action=${action}&product_id=${productId}`);
  }
});
