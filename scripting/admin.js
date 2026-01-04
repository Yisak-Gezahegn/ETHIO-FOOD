
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], // Later we can pull these from PHP
        datasets: [{
            label: 'Earnings (ETB)',
            data: [1200, 1900, 800, 1500, 2200, 3000, 2500], // Example data
            borderColor: '#ff4757',
            backgroundColor: 'rgba(255, 71, 87, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4 // Makes the line curvy and smooth
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

function openResModal(id, name, location, rating) {
    console.log("Editing Restaurant ID: " + id);
    
    document.getElementById('edit_res_id').value = id;
    document.getElementById('edit_res_name').value = name;
    document.getElementById('edit_res_location').value = location;
    document.getElementById('edit_res_rating').value = rating;
    
    // Set Modal Title
    document.querySelector('#resModal h3').innerText = "Edit Restaurant Information";
    document.getElementById('resModal').style.display = 'block';
}

function openResModal(id, name, location, rating) {
    // 1. Find the modal and display it
    const modal = document.getElementById('resModal');
    modal.style.display = 'block';

    // 2. Fill the form inputs with the data
    document.getElementById('edit_res_id').value = id;
    document.getElementById('edit_res_name').value = name;
    document.getElementById('edit_res_location').value = location;
    document.getElementById('edit_res_rating').value = rating;
}