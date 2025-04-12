<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Drivers List</h2>

        <a href="{{ route('drivers.create') }}" class="btn btn-primary mb-3">Add Driver</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="drivers-table-body">
                <!-- Les données seront insérées ici -->
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS et jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            fetchDrivers();
        });

        function fetchDrivers() {
            $.ajax({
                url: "{{ url('/api/drivers') }}", // L'URL de ton API
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        let drivers = response.data;
                        let tableBody = $("#drivers-table-body");
                        tableBody.empty(); // Vide le tableau avant d'ajouter les données

                        drivers.forEach(driver => {
                            let row = `
                                <tr>
                                    <td>${driver.id}</td>
                                    <td>${driver.name}</td>
                                    <td>${driver.email}</td>
                                    <td>${driver.phone ? driver.phone : 'Non renseigné'}</td>
                                    <td>${new Date(driver.created_at).toLocaleDateString()}</td>
                                    <td>
                                        <a href="/drivers/${driver.id}" class="btn btn-info">View</a>
                                        <a href="/drivers/${driver.id}/edit" class="btn btn-warning">Edit</a>
                                        <button onclick="deleteDriver(${driver.id})" class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    } else {
                        console.error("Erreur de chargement des chauffeurs :", response.message);
                    }
                },
                error: function(error) {
                    console.error("Erreur lors de la récupération des données :", error);
                }
            });
        }

        function deleteDriver(id) {
            if (confirm("Voulez-vous vraiment supprimer ce chauffeur ?")) {
                $.ajax({
                    url: `/api/drivers/${id}`,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert(response.message);
                        fetchDrivers(); // Recharge la liste après suppression
                    },
                    error: function(error) {
                        console.error("Erreur lors de la suppression :", error);
                    }
                });
            }
        }
    </script>

</body>
</html>
