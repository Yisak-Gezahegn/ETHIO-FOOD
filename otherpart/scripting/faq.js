// FAQ Page Functionality
document.addEventListener("DOMContentLoaded", function () {
  // Elements
  const faqItems = document.querySelectorAll(".faq-item");
  const categoryTags = document.querySelectorAll(".category-tag");
  const searchInput = document.getElementById("faq-search");
  const searchBtn = document.getElementById("search-btn");
  const noResults = document.getElementById("no-results");
  const resultsCount = document.getElementById("results-count");

  // Initialize FAQ accordion
  initFaqAccordion();

  // Initialize category filtering
  initCategoryFilter();

  // Initialize search functionality
  initSearch();

  // FAQ Accordion Functionality
  function initFaqAccordion() {
    faqItems.forEach((item) => {
      const question = item.querySelector(".faq-question");

      question.addEventListener("click", () => {
        // Close all other FAQ items
        faqItems.forEach((otherItem) => {
          if (otherItem !== item && otherItem.classList.contains("active")) {
            otherItem.classList.remove("active");
          }
        });

        // Toggle current FAQ item
        item.classList.toggle("active");

        // Update results count after toggle (if filtering is active)
        updateResultsCount();
      });
    });
  }

  // Category Filter Functionality
  function initCategoryFilter() {
    categoryTags.forEach((tag) => {
      tag.addEventListener("click", () => {
        // Update active tag
        categoryTags.forEach((t) => t.classList.remove("active"));
        tag.classList.add("active");

        const selectedCategory = tag.getAttribute("data-category");

        // Filter FAQ items
        filterFaqs(selectedCategory, searchInput.value.trim());
      });
    });
  }

  // Search Functionality
  function initSearch() {
    // Search button click
    searchBtn.addEventListener("click", () => {
      const searchTerm = searchInput.value.trim().toLowerCase();
      const activeCategory = document
        .querySelector(".category-tag.active")
        .getAttribute("data-category");

      filterFaqs(activeCategory, searchTerm);
    });

    // Search on Enter key
    searchInput.addEventListener("keyup", (event) => {
      if (event.key === "Enter") {
        const searchTerm = searchInput.value.trim().toLowerCase();
        const activeCategory = document
          .querySelector(".category-tag.active")
          .getAttribute("data-category");

        filterFaqs(activeCategory, searchTerm);
      }
    });

    // Clear search when input is cleared
    searchInput.addEventListener("input", () => {
      if (searchInput.value.trim() === "") {
        const activeCategory = document
          .querySelector(".category-tag.active")
          .getAttribute("data-category");
        filterFaqs(activeCategory, "");
      }
    });
  }

  // Filter FAQs based on category and search term
  function filterFaqs(category, searchTerm) {
    let visibleCount = 0;

    faqItems.forEach((item) => {
      const itemCategory = item.getAttribute("data-category");
      const questionText = item
        .querySelector(".faq-question h3")
        .textContent.toLowerCase();
      const answerText = item
        .querySelector(".faq-answer")
        .textContent.toLowerCase();

      // Check if item matches category filter
      const matchesCategory = category === "all" || itemCategory === category;

      // Check if item matches search filter
      const matchesSearch =
        searchTerm === "" ||
        questionText.includes(searchTerm) ||
        answerText.includes(searchTerm);

      // Show/hide item based on filters
      if (matchesCategory && matchesSearch) {
        item.style.display = "block";
        visibleCount++;
      } else {
        item.style.display = "none";
      }
    });

    // Show/hide no results message
    if (visibleCount === 0) {
      noResults.style.display = "block";
    } else {
      noResults.style.display = "none";
    }

    // Update results count
    updateResultsCount();
  }

  // Update the results count display
  function updateResultsCount() {
    const visibleItems = document.querySelectorAll(
      '.faq-item[style="display: block"]'
    );

    // If no filtering is active, count all items
    if (visibleItems.length === 0) {
      const activeCategory = document
        .querySelector(".category-tag.active")
        .getAttribute("data-category");
      const searchTerm = searchInput.value.trim().toLowerCase();

      // If both filters are empty, count all items
      if (activeCategory === "all" && searchTerm === "") {
        resultsCount.textContent = faqItems.length;
      } else {
        resultsCount.textContent = "0";
      }
    } else {
      resultsCount.textContent = visibleItems.length;
    }
  }

  // Simulate a click on the first FAQ item to make it expanded by default
  if (faqItems.length > 0) {
    faqItems[0].classList.add("active");
  }

  // Add some sample interaction for demonstration
  console.log("FAQ page loaded successfully!");
  console.log("Total FAQ items:", faqItems.length);

  // Add a welcome message in the console
  console.log(
    "%c🍔 QuickBite FAQs 🍕",
    "color: #ff6b35; font-size: 16px; font-weight: bold;"
  );
  console.log(
    "%cFind answers to all your food ordering questions!",
    "color: #666;"
  );
});
function updateFAQCount() {
  // 1. Get all FAQ items
  const allItems = document.querySelectorAll(".faq-item");
  let count = 0;

  // 2. Count only those that are NOT hidden
  allItems.forEach((item) => {
    if (item.style.display !== "none") {
      count++;
    }
    
  });

  // 3. Update the span in your HTML
  const countDisplay = document.getElementById("results-count");
  if (countDisplay) {
    countDisplay.textContent = count;
  }
}

// 4. Link this to your category buttons
const categoryButtons = document.querySelectorAll(".category-tag"); // match your class in image_1b4ed9
categoryButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    // ... (your existing filtering code that sets display: none or block) ...

    // RECOUNT AFTER FILTERING
    updateFAQCount();
  });
});

// Run once when page loads to show the initial '12'
document.addEventListener("DOMContentLoaded", updateFAQCount);
function updateFAQCount() {
  // 1. Get all items using the class from your HTML
  const allItems = document.querySelectorAll(".faq-item");

  // DEBUG: See if the script finds any items at all
  console.log("Total FAQ items found: " + allItems.length);

  let visibleCount = 0;

  // 2. Check visibility properly
  allItems.forEach((item) => {
    // This checks both 'display: none' and if the element is hidden by parent logic
    const isVisible = window.getComputedStyle(item).display !== "none";
    if (isVisible) {
      visibleCount++;
    }
  });

  // 3. Update the span
  const countDisplay = document.getElementById("results-count");
  if (countDisplay) {
    countDisplay.textContent = visibleCount;
    console.log("Updated display to: " + visibleCount);
  } else {
    console.error("Could not find the span with ID 'results-count'!");
  }
}

// 4. Force the count to run whenever a category tag is clicked
document.querySelectorAll(".category-tag").forEach((button) => {
  button.addEventListener("click", () => {
    // We use a tiny delay (10ms) to ensure the filter finishes hiding items first
    setTimeout(updateFAQCount, 10);
  });
});

// 5. Run on initial page load
window.addEventListener("load", updateFAQCount);