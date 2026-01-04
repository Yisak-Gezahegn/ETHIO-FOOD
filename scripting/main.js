/* --- ETHIO FOOD INTERACTIVE SCRIPT --- */

document.addEventListener("DOMContentLoaded", () => {
  // 1. Sticky Header Effect
  const header = document.querySelector("header");
  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.style.padding = "5px 0";
      header.style.backgroundColor = "rgba(255, 255, 255, 0.95)";
    } else {
      header.style.padding = "15px 0";
      header.style.backgroundColor = "white";
    }
  });

  // 2. Client-Side Restaurant Search (Phase 2 Feature)
  // This allows users to browse through the extensive list as planned
  const searchInput = document.createElement("input");
  // Note: For now, we attach this logic to your existing search-bar class
  const searchBar = document.querySelector(".search-bar");

  if (searchBar) {
    searchBar.addEventListener("keyup", (e) => {
      const term = e.target.value.toLowerCase();
      const restaurants = document.querySelectorAll(".restaurant-card");

      restaurants.forEach((card) => {
        const name = card.querySelector("h3").textContent.toLowerCase();
        if (name.includes(term)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  }

  // 3. Smooth Fade-in for Hero Section
  const heroContent = document.querySelector(".hero .container");
  if (heroContent) {
    heroContent.style.opacity = "0";
    heroContent.style.transform = "translateY(20px)";
    heroContent.style.transition = "all 1s ease-out";

    setTimeout(() => {
      heroContent.style.opacity = "1";
      heroContent.style.transform = "translateY(0)";
    }, 300);
  }

  // 4. Mobile Menu Toggle
  const mobileBtn = document.querySelector(".mobile-menu");
  const nav = document.querySelector("nav");

  if (mobileBtn) {
    mobileBtn.addEventListener("click", () => {
      nav.classList.toggle("active");
      // Adding a simple alert for testing as an "action response"
      console.log("Mobile menu toggled");
    });
  }
});
document.addEventListener("DOMContentLoaded", function () {
  const banner = document.getElementById("cookie-banner");
  const allowBtn = document.getElementById("cookie-allow");

  // Check if user already allowed cookies
  if (localStorage.getItem("cookiesAccepted")) {
    banner.style.display = "none";
  }

  allowBtn.addEventListener("click", function () {
    localStorage.setItem("cookiesAccepted", "true");
    banner.style.display = "none";
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const banner = document.getElementById("cookie-banner");
  const buttons = document.querySelectorAll(".cookie-buttons button");

  // Function to hide banner with a smooth fade
  const hideBanner = () => {
    banner.style.opacity = "0";
    setTimeout(() => {
      banner.style.display = "none";
    }, 500); // Wait for fade animation to finish
  };

  // 1. Hide when ANY button is clicked
  buttons.forEach((button) => {
    button.addEventListener("click", () => {
      // If they click 'allow', save to local storage
      if (button.id === "cookie-allow") {
        localStorage.setItem("cookiesAccepted", "true");
      }
      hideBanner();
    });
  });

  // 2. Auto-hide after 1 minute (60,000 milliseconds)
  setTimeout(() => {
    if (banner.style.display !== "none") {
      hideBanner();
    }
  }, 60000);

  // 3. Keep hidden if already accepted
  if (localStorage.getItem("cookiesAccepted")) {
    banner.style.display = "none";
  }
});
document.addEventListener("DOMContentLoaded", () => {
    const banner = document.getElementById("cookie-banner");
    const modal = document.getElementById("cookie-settings-modal");
    const allowBtn = document.getElementById("cookie-allow");
    const manageLink = document.querySelector(".manage-cookies-link");

    // 1. Initial Check: If already accepted, hide banner
    if (localStorage.getItem("cookiesAccepted")) {
        banner.style.display = "none";
    }

    // 2. Open Modal when "Manage Cookies" is clicked
    manageLink.addEventListener("click", (e) => {
        e.preventDefault();
        modal.style.display = "block";
    });

    // 3. Accept All (from Banner or Modal)
    const acceptAction = () => {
        localStorage.setItem("cookiesAccepted", "true");
        banner.style.opacity = "0";
        modal.style.display = "none";
        setTimeout(() => { banner.style.display = "none"; }, 500);
    };

    allowBtn.addEventListener("click", acceptAction);
    document.getElementById("accept-all-modal").addEventListener("click", acceptAction);

    // 4. Close Modal on "Save"
    document.getElementById("save-settings").onclick = () => {
        modal.style.display = "none";
    };

    // 5. Close Modal if clicking outside the box
    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = "none";
    };
});
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("cookie-settings-modal");
  const openBtn = document.getElementById("open-cookie-settings");
  const saveBtn = document.getElementById("save-cookie-settings");
  const acceptBtn = document.getElementById("accept-all-cookies");

  // SHOW MODAL
  if (openBtn) {
    openBtn.onclick = function () {
      modal.style.display = "block";
    };
  }

  // HIDE MODAL
  saveBtn.onclick = function () {
    modal.style.display = "none";
  };

  acceptBtn.onclick = function () {
    localStorage.setItem("cookiesAccepted", "true");
    modal.style.display = "none";
    // Also hide the main banner if it's visible
    document.getElementById("cookie-banner").style.display = "none";
  };

  // CLOSE IF CLICK OUTSIDE
  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };
});
function toggleDrawer() {
  // These IDs MUST match the ones in your HTML
  const drawer = document.getElementById("accountDrawer");
  const overlay = document.getElementById("drawerOverlay");

  drawer.classList.toggle("active");
  overlay.classList.toggle("active");
}