// Search Functionality
function filterRestaurants() {
  let input = document.getElementById("searchInput").value.toLowerCase();
  let table = document.getElementById("resTable");
  let tr = table.getElementsByTagName("tr");

  for (let i = 1; i < tr.length; i++) {
    let nameCol = tr[i].getElementsByTagName("td")[0];
    let ratingCol = tr[i].getElementsByTagName("td")[2];

    if (nameCol || ratingCol) {
      let txtValue = nameCol.textContent || nameCol.innerText;
      let ratingValue = ratingCol.textContent || ratingCol.innerText;

      if (
        txtValue.toLowerCase().indexOf(input) > -1 ||
        ratingValue.indexOf(input) > -1
      ) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

// Modal Controls
function showAddModal() {
  document.getElementById("addModal").style.display = "block";
}



window.onclick = function (event) {
  let modal = document.getElementById("addModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
// Add Restaurant Modal Controls
function openAddModal() {
  document.getElementById("addResModal").style.display = "block";
}

function closeAddModal() {
  document.getElementById("addModal").style.display = "none";
}
