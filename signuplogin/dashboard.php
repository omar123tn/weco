<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if (isset($_POST['search'])) {
    $auftragsNr = mysqli_real_escape_string($conn, $_POST['auftragsNr']);
    $sql = "SELECT * FROM `open_orders_26.06` WHERE `Auftrags-Nr.` = '$auftragsNr'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Récupérer tous les numéros de commande
$sql_all_orders = "SELECT DISTINCT `Auftrags-Nr.` FROM `open_orders_26.06`";
$result_all_orders = mysqli_query($conn, $sql_all_orders);

if (!$result_all_orders) {
    die("Query Failed: " . mysqli_error($conn));
}

$all_orders = mysqli_fetch_all($result_all_orders, MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recherche de Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .header {
            background-color: white;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header img {
            height: 55px;
            transition: transform 0.3s ease-in-out;
        }

        .header img:hover {
            transform: scale(1.05);
        }

        .header .btn {
            margin-right: 10px;
        }

        .content {
            margin-top: 20px;
        }

        .center-text {
            text-align: center;
        }

        .animated {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .animated:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            overflow: hidden;
        }

        .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
            transition: background-color 0.2s, box-shadow 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .text-red {
            color: red;
        }

        .table-container {
            overflow-x: scroll;
        }

        .table {
            white-space: nowrap;
            background-color: #ffffff;
        }

        .table thead th {
            background-color: #343a40;
            color: #ffffff;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-primary,
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-success,
        .btn-success:hover,
        .btn-success:focus {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-danger,
        .btn-danger:hover,
        .btn-danger:focus {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-warning,
        .btn-warning:hover,
        .btn-warning:focus {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .table th,
        .table td {
            transition: background-color 0.3s ease-in-out;
        }

        .table tbody tr:hover th,
        .table tbody tr:hover td {
            background-color: #e9ecef;
        }

        .new-field {
            background-color: #f9f871;
        }

        .navbar-light .navbar-nav .nav-link {
            color: black;
            transition: color 0.3s;
        }

        .navbar-light .navbar-nav .nav-link:hover {
            color: blueviolet;
        }

        .navbar-light .navbar-toggler {
            color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Spinner Styles */
        #spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            visibility: hidden;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Spinner Styles */
#spinner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    visibility: hidden;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

    </style>
</head>

<body>

<script>
    function showSpinner() {
        document.getElementById('spinner').style.visibility = 'visible';
    }
</script>

<div id="spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <header class="header d-flex justify-content-between align-items-center px-3">
        <img src="images.jfif" alt="Logo WECO">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload_clear.php">Gérer les Données</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($username); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <form method="POST">
                                        <button type="submit" class="dropdown-item btn btn-outline-danger animated text-red" name="logout" onclick="return confirm('Vous êtes sûr de vouloir vous déconnecter?')">Déconnexion</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container content">
        <div class="center-text mb-4">
            <h1>Bienvenue</h1>
        </div>
        <h2 class="text-center mb-4">Rechercher une commande</h2>
        <form method="POST" action="" class="mb-4" onsubmit="showSpinner()">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="auftragsNr" class="form-label">Numéro de commande</label>
                        <select class="form-control animated" id="auftragsNr" name="auftragsNr" required>
                            <option value="" disabled selected>Choisissez un numéro de commande</option>
                            <?php foreach ($all_orders as $order): ?>
                            <option value="<?php echo htmlspecialchars($order['Auftrags-Nr.']); ?>"><?php echo htmlspecialchars($order['Auftrags-Nr.']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary animated" name="search">Rechercher</button>
                    
                    </div>
                </div>
            </div>
        </form>

        <?php if (isset($orders) && !empty($orders)): ?>
        <div class="table-container">
            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                    <th>ID</th>
                        <th>AG</th>
                        <th>Abt.</th>
                        <th>Centre de travail</th>
                        <th>Désignation AG</th>
                        <th>Numéro de commande</th>
                        <th>ST</th>
                        <th>Numéro de pièce</th>
                        <th>Désignation 1</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Quantité totale</th>
                        <th>Quantité restante</th>
                        <th>Quantité produite</th>
                        <th>Temps restant</th>
                        <th>Temps produit</th>
                        <th class="new-field">Produit déclaré</th>
                        <th class="new-field">Nom d'utilisateur</th>
                        <th>Date de declaration</th>
                        <th>heure de declaration</th>
                        <th>Date et Heure de production</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr class="animated-row">
                        <td><?php echo htmlspecialchars($order['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['AG'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['Abt.'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['work center'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['AG Bezeichnung'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['Auftrags-Nr.'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['ST'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['Teile-Nr'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['Bezeichnung 1'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['Start'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['Ende'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(trim($order['qty totale'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars(trim($order['qte restante'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($order['Qte produite'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['time remaining'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['temps produit'] ?? ''); ?></td>
                        <td class="new-field"><?php echo htmlspecialchars($order['produit declaree'] ?? ''); ?></td>
                        <td class="new-field"><?php echo htmlspecialchars($order['username'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['date_declaration'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['heure_declaration'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($order['date_enregistrement'] ?? ''); ?></td>
                        <td>
                            <form method="GET" action="display_order.php" style="display:inline;" onsubmit="showSpinner()">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                <button type="submit" class="btn btn-warning animated">Afficher</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php elseif (isset($orders) && empty($orders)): ?>
        <p class="text-center mt-4">Aucune commande trouvée pour ce numéro.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>

</html>

