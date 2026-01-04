// Modal functions
function openAddMenuItemModal() {
  document.getElementById("addMenuItemModal").style.display = "block";
}

function openEditMenuItemModal(id, name, price, available) {
  document.getElementById("edit_item_id").value = id;
  document.getElementById("edit_item_name").value = name;
  document.getElementById("edit_item_price").value = price;
  document.getElementById("edit_item_available").checked = available == 1;
  document.getElementById("editMenuItemModal").style.display = "block";
}

function openAddPromoModal() {
  document.getElementById("addPromoModal").style.display = "block";
}

function openAddStaffModal() {
  document.getElementById("addStaffModal").style.display = "block";
}

function closeModal() {
  document.querySelectorAll(".modal").forEach((modal) => {
    modal.style.display = "none";
  });
}

// Close modal when clicking outside
window.onclick = function (event) {
  if (event.target.classList.contains("modal")) {
    closeModal();
  }
};
// Initialize Charts
document.addEventListener("DOMContentLoaded", function () {
  // Update date time
  function updateDateTime() {
    const now = new Date();
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "numeric",
      minute: "numeric",
      hour12: true,
    };
    document.getElementById("currentDateTime").textContent =
      now.toLocaleDateString("en-US", options);
  }
  setInterval(updateDateTime, 60000);

  // Performance Chart
  const performanceCtx = document.getElementById("performanceChart");
  if (performanceCtx) {
    new Chart(performanceCtx.getContext("2d"), {
      type: "line",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [
          {
            label: "Orders",
            data: [12, 19, 15, 25, 22, 30, 28],
            borderColor: "#4CAF50",
            tension: 0.4,
          },
          {
            label: "Revenue (ETB)",
            data: [8500, 9200, 11000, 12500, 13500, 15000, 14200],
            borderColor: "#2196F3",
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  }

  // Order Status Chart
  const orderStatusCtx = document.getElementById("orderStatusChart");
  if (orderStatusCtx) {
    new Chart(orderStatusCtx.getContext("2d"), {
      type: "doughnut",
      data: {
        labels: ["Completed", "Preparing", "Pending", "Cancelled"],
        datasets: [
          {
            data: [65, 20, 10, 5],
            backgroundColor: ["#e82020ff", "#2196F3", "#FF9800", "#F44336"],
          },
        ],
      },
    });
  }

  // Revenue Chart
  const revenueCtx = document.getElementById("revenueChart");
  if (revenueCtx) {
    new Chart(revenueCtx.getContext("2d"), {
      type: "bar",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [
          {
            label: "Revenue (ETB)",
            data: [45000, 52000, 48000, 55000, 60000, 65000],
            backgroundColor: "#ef1c1cff",
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  }
});

// Update order status function
function updateOrderStatus(orderId, status) {
  // AJAX call to update order status
  console.log(`Updating order ${orderId} to ${status}`);
  // In production: fetch('update_order.php', {method: 'POST', body: {orderId, status}})
}

// Print report function
function printReport() {
  window.print();
}

// Update chart based on time selection
function updateChart(timeRange) {
  console.log(`Updating chart for ${timeRange}`);
  // Update chart data based on time range
}
