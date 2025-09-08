document.addEventListener('DOMContentLoaded', () => {
    // Initialisation du graphique Chart.js
    const ctx = document.getElementById('sales-chart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr'],
            datasets: [{
                label: 'Inscriptions',
                data: [30, 50, 80, 150],
                borderColor: '#0066ff',
                backgroundColor: 'rgba(0, 102, 255, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});