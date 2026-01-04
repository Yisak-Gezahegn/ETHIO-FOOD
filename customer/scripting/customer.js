function switchTab(evt, tabName) {
  // 1. Hide all tab panes
  const panes = document.querySelectorAll(".tab-pane");
  panes.forEach((p) => (p.style.display = "none"));

  // 2. Remove 'active' class from all buttons
  const buttons = document.querySelectorAll(".tab-item");
  buttons.forEach((b) => b.classList.remove("active"));

  // 3. Show the clicked tab and set button to active
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.classList.add("active");

  // 4. If switching to 'history', render the graph
  if (tabName === "history") {
    renderOrderGraph();
  }
}

function renderOrderGraph() {
  const ctx = document.getElementById("orderChart").getContext("2d");
  new Chart(ctx, {
    type: "line",
    data: {
      labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
      datasets: [
        {
          label: "Orders (Birr)",
          data: [120, 450, 300, 700, 200, 900, 400],
          borderColor: "#FF5E3A",
          tension: 0.4,
        },
      ],
    },
  });
}
// Function to handle tab switching for Favorite, Blog, etc.
function switchTab(evt, tabName) {
    const tabPanes = document.querySelectorAll('.tab-pane');
    const tabButtons = document.querySelectorAll('.tab-item');

    // Hide all content areas
    tabPanes.forEach(pane => pane.style.display = 'none');
    
    // Remove active class from all buttons
    tabButtons.forEach(btn => btn.classList.remove('active'));

    // Show the selected tab content
    document.getElementById(tabName).style.display = 'block';
    
    // Mark the clicked button as active
    if(evt) {
        evt.currentTarget.classList.add('active');
    }
}

// Specifically for the Settings gear icon
function openSettings() {
    switchTab(null, 'info'); // Switches to the profile editor tab
}
// Function to open the receipt popup
function showReceipt() {
    document.getElementById("receiptModal").style.display = "block";
}

// Function to close the receipt popup
function closeReceipt() {
    document.getElementById("receiptModal").style.display = "none";
}

// Close if user clicks outside the white box
window.onclick = function(event) {
    let modal = document.getElementById("receiptModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
// The function now takes 'name' and 'price' as inputs
function showReceipt(name, price) {
    // 1. Find the elements by ID
    const nameElement = document.getElementById("receipt-food-name");
    const priceElement = document.getElementById("receipt-food-price");

    // 2. Change their text to the clicked food's data
    nameElement.innerText = "1x " + name;
    priceElement.innerText = price;

    // 3. Show the modal
    document.getElementById("receiptModal").style.display = "block";
}
function openAllReceipts() {
  document.getElementById("allReceiptsModal").style.display = "block";
}

function closeAllReceipts() {
  document.getElementById("allReceiptsModal").style.display = "none";
}