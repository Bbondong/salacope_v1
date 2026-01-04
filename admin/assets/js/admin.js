// admin/assets/js/dashboard-charts.js

document.addEventListener('DOMContentLoaded', function() {
    // Attendre que le DOM soit complètement chargé
    setTimeout(initCharts, 100);
    
    function initCharts() {
        // Graphique des revenus par abonnement
        const revenueCtx = document.getElementById('subscriptionRevenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [
                        {
                            label: 'Premium',
                            data: [4500000, 5200000, 4800000, 6100000, 5800000, 7200000, 6900000, 7500000, 8200000, 7800000, 8500000, 9200000],
                            backgroundColor: '#e74c3c',
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Standard',
                            data: [3200000, 3800000, 3500000, 4200000, 3900000, 4500000, 4800000, 5200000, 5800000, 5500000, 6200000, 6800000],
                            backgroundColor: '#3498db',
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Essai',
                            data: [500000, 450000, 520000, 480000, 550000, 620000, 580000, 650000, 720000, 680000, 750000, 820000],
                            backgroundColor: '#f39c12',
                            borderRadius: 8,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FC';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    }
                                    return value / 1000 + 'K';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Graphique de distribution des abonnements
        const distributionCtx = document.getElementById('subscriptionDistributionChart');
        if (distributionCtx) {
            new Chart(distributionCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Premium', 'Standard'],
                    datasets: [{
                        data: [57, 43],
                        backgroundColor: ['#e74c3c', '#3498db'],
                        borderWidth: 0,
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});