document.addEventListener("DOMContentLoaded", function () {
  function showToast(message) {
    var toast = document.querySelector(".toast");
    if (!toast) {
      toast = document.createElement("div");
      toast.className = "toast";
      toast.innerHTML =
        '<span>🛒</span><span class="toast-message"></span>';
      document.body.appendChild(toast);
    }

    var msgSpan = toast.querySelector(".toast-message");
    msgSpan.textContent = message;

    toast.classList.add("show");

    setTimeout(function () {
      toast.classList.remove("show");
    }, 2200);
  }

  function pulseCartBadge() {
    var badge = document.getElementById("cartCount");
    if (!badge) return;

    badge.classList.remove("pulse");
    void badge.offsetWidth; 
    badge.classList.add("pulse");
  }

  var addToCartForms = document.querySelectorAll(".add-to-cart-form");
  addToCartForms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      var qtyInput = form.querySelector('input[name="quantity"]');
      var qty = qtyInput ? parseInt(qtyInput.value, 10) : 0;

      if (isNaN(qty) || qty < 1) {
        e.preventDefault();
        showToast("❗ Enter a valid quantity (at least 1).");
        return;
      }
    });
  });

  var qtyInputs = document.querySelectorAll(".qty-input");
  if (qtyInputs.length > 0) {
    function updateTotals() {
      var grandTotal = 0;

      qtyInputs.forEach(function (input) {
        var price = parseFloat(input.dataset.price);
        var row = input.closest("tr");
        if (!row) return;

        var subtotalCell = row.querySelector(".subtotal");
        var qty = parseInt(input.value, 10);
        if (isNaN(qty) || qty < 0) qty = 0;

        var subtotal = price * qty;
        if (subtotalCell) {
          subtotalCell.textContent = subtotal.toFixed(2);
        }

        grandTotal += subtotal;
      });

      var grandTotalElement = document.getElementById("grandTotal");
      if (grandTotalElement) {
        grandTotalElement.textContent = grandTotal.toFixed(2);
      }
    }

    qtyInputs.forEach(function (input) {
      input.addEventListener("input", updateTotals);
    });

    updateTotals();
  }

  var checkoutForm = document.getElementById("checkoutForm");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", function (e) {
      var name = document.getElementById("name").value.trim();
      var email = document.getElementById("email").value.trim();
      var phone = document.getElementById("phone").value.trim();
      var errorDiv = document.getElementById("checkoutError");

      var errorMsg = "";
      if (!name || !email || !phone) {
        errorMsg = "Please fill in all required fields.";
      } else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        errorMsg = "Please enter a valid email address.";
      }

      if (errorMsg) {
        e.preventDefault();
        if (errorDiv) {
          errorDiv.textContent = errorMsg;
          errorDiv.style.display = "block";
        } else {
          showToast(errorMsg);
        }
      } else {
        showToast("Processing your order...");
      }
    });
  }

  var productCards = document.querySelectorAll(".product-card");
  if (productCards.length > 0) {
    var randomIndex = Math.floor(Math.random() * productCards.length);
    var chosenCard = productCards[randomIndex];

    var wrapper = document.createElement("div");
    wrapper.className = "deal-of-day";
    chosenCard.parentNode.insertBefore(wrapper, chosenCard);
    wrapper.appendChild(chosenCard);

    var ribbon = document.createElement("div");
    ribbon.className = "deal-ribbon";
    ribbon.textContent = "Deal of the Day";
    wrapper.appendChild(ribbon);
  }

  var subtitleEl = document.getElementById("heroSubtitle");
  if (subtitleEl) {
    var phrases = [
      "Smart deals on powerful laptops.",
      "Perfect machines for code, games and school.",
      "Browse. Add to cart. Check out in seconds."
    ];

    var phraseIndex = 0;
    var charIndex = 0;
    var deleting = false;

    function type() {
      var current = phrases[phraseIndex];

      if (!deleting) {
        subtitleEl.textContent = current.slice(0, charIndex + 1);
        charIndex++;
        if (charIndex === current.length) {
          deleting = true;
          setTimeout(type, 1500);
          return;
        }
      } else {
        subtitleEl.textContent = current.slice(0, charIndex - 1);
        charIndex--;
        if (charIndex === 0) {
          deleting = false;
          phraseIndex = (phraseIndex + 1) % phrases.length;
        }
      }

      var speed = deleting ? 40 : 70;
      setTimeout(type, speed);
    }

    type();
  }
});
