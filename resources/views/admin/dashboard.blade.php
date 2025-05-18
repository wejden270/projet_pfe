<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta http-equiv="refresh" content="30">
    <style>
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 2rem;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin!</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Clients</h5>
                        <h2 class="card-text">{{ $stats['total_clients'] }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Chauffeurs</h5>
                        <h2 class="card-text">{{ $stats['total_drivers'] }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Demandes</h5>
                        <h2 class="card-text">{{ $stats['total_demandes'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Répartition des Demandes</h5>
                        <!-- Ajout d'un conteneur avec hauteur définie -->
                        <div style="height: 400px;">
                            <canvas id="demandesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div>
                <a href="/index.html" class="btn btn-secondary mr-2">
                    <i class="fas fa-home"></i> Home
                </a>
            
    </div>

    <!-- Ajouter Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('demandesChart').getContext('2d');

        fetch('/stats')
            .then(response => response.json())
            .then(data => {
                const statsData = data.data.demandes_par_status;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Acceptées', 'Refusées', 'Annulées', 'En attente'],
                        datasets: [{
                            label: 'Nombre de demandes',
                            data: [
                                statsData.acceptees,
                                statsData.refusees,
                                statsData.annulees,
                                statsData.en_attente
                            ],
                            backgroundColor: [
                                '#28a745', // vert pour acceptées
                                '#dc3545', // rouge pour refusées
                                '#ffc107', // jaune pour annulées
                                '#17a2b8'  // bleu pour en attente
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Permet au graphique de s'adapter à la hauteur du conteneur
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            });
    });
    </script>
</body>
</html>
