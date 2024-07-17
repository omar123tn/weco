<?php
include("connection.php");
session_start();

$order = null;

// Fetch order details if ID is provided
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT o.*, s.username FROM `open_orders_26.06` o
            JOIN `signup` s ON o.username = s.username
            WHERE o.id = '$id'";
    $result = mysqli_query($conn, $sql);
    $order = mysqli_fetch_assoc($result);

    if (!$order) {
        echo "Commande non trouvée.";
        exit();
    }
}

// Process form submission to update order details
if (isset($_POST['save'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $username = mysqli_real_escape_string($conn, $_SESSION['username']);
    $produit_declaree = isset($_POST['produit_declared']) ? (int) mysqli_real_escape_string($conn, $_POST['produit_declared']) : 0;
    $previousQtyRestante = (int) mysqli_real_escape_string($conn, $_POST['previous_qty_restante']);
    $newQty = $previousQtyRestante - $produit_declaree;

    $qtyTotale = (int) $order['qty totale'];
    $qtyProduite = $qtyTotale - $newQty;
    $timeRemaining = (double) $order['time remaining'];
    $tempsProduit = ($newQty > 0) ? ($timeRemaining / $newQty) * $qtyProduite : 0;
    $tempsDeclare = ($qtyTotale > 0) ? ($timeRemaining / $qtyTotale) * $qtyProduite : 0;

    // Récupérer et formater la date et l'heure de déclaration
    $date_declaration = isset($_POST['date_declaration']) ? mysqli_real_escape_string($conn, $_POST['date_declaration']) : date('Y-m-d');
    $heure_declaration = isset($_POST['heure_declaration']) ? mysqli_real_escape_string($conn, $_POST['heure_declaration']) : date('H:i:s');
    $date_heure_declaration = $date_declaration . ' ' . $heure_declaration;

    // Récupérer la date et l'heure actuelles avec une heure de moins
$date_enregistrement = date('Y-m-d H:i:s', strtotime('-1 hour'));


    // Insertion dans l'historique
    $historySql = "INSERT INTO order_updates (`AG`, `Abt.`, `work center`, `AG Bezeichnung`, `Auftrags-Nr.`, `ST`, `Teile-Nr`, `Bezeichnung 1`, `Start`, `Ende`, `qty totale`, `qte restante`, `Qte produite`, `time remaining`, `username`, `temps produit`, `temps declaré`, `produit declaree`, `date_declaration`, `heure_declaration`, `date_enregistrement`) 
                   VALUES ('" . mysqli_real_escape_string($conn, $order['AG']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['Abt.']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['work center']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['AG Bezeichnung']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['Auftrags-Nr.']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['ST']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['Teile-Nr']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['Bezeichnung 1']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['Start']) . "', 
                           '" . mysqli_real_escape_string($conn, $order['Ende']) . "', 
                           '$qtyTotale', 
                           '$newQty', 
                           '$qtyProduite', 
                           '$timeRemaining', 
                           '$username', 
                           '$tempsProduit', 
                           '$tempsDeclare', 
                           '$produit_declaree', 
                           '$date_declaration', 
                           '$heure_declaration', 
                           '$date_enregistrement')";
    if (!mysqli_query($conn, $historySql)) {
        echo "Erreur lors de l'enregistrement dans l'historique : " . mysqli_error($conn);
    }

    // Mise à jour des détails de la commande principale
    $updateSql = "UPDATE `open_orders_26.06` SET 
                  `username` = '$username', 
                  `produit declaree` = '$produit_declaree', 
                  `qte restante` = '$newQty', 
                  `Qte produite` = '$qtyProduite', 
                  `temps produit` = '$tempsProduit', 
                  `temps declaré` = '$tempsDeclare', 
                  `date_enregistrement` = '$date_enregistrement',
                  `date_declaration` = '$date_declaration', 
                  `heure_declaration` = '$heure_declaration'
                  WHERE `id` = '$id'";
    if (mysqli_query($conn, $updateSql)) {
        echo "Nom d'utilisateur, Produit Déclaré et Quantité mis à jour avec succès.<br>";
        header("Location: display_order.php?id=$id");
        exit();
    } else {
        echo "Erreur lors de l'enregistrement des données : " . mysqli_error($conn);
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détails de la Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #f0f0f0;
        }
        .btn {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: bold;
        }
    </style>
    <script>
        function updateRemainingQuantity() {
            var previousQtyRestante = parseInt(document.getElementById('previous_qty_restante').value);
            var produitDeclared = parseInt(document.getElementById('produit_declared').value) || 0;
            var remainingQty = previousQtyRestante - produitDeclared;
            document.getElementById('remaining_qty_display').innerText = remainingQty;
        }
    </script>
</head>
<body>
    
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php">
            <img src="images.jfif" alt="Logo de WECO" style="height: 50px;">
        </a>
        <a href="dashboard.php" class="btn btn-secondary">Retour</a>
    </div>
    <h1>Détails de la Commande</h1>
    <?php if ($order): ?>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
            </tr>
            <tr>
                <th>AG</th>
                <td><?php echo htmlspecialchars($order['AG']); ?></td>
            </tr>
            <tr>
                <th>Abt.</th>
                <td><?php echo htmlspecialchars($order['Abt.']); ?></td>
            </tr>
            <tr>
                <th>Work Center</th>
                <td><?php echo htmlspecialchars($order['work center']); ?></td>
            </tr>
            <tr>
                <th>AG Bezeichnung</th>
                <td><?php echo htmlspecialchars($order['AG Bezeichnung']); ?></td>
            </tr>
            <tr>
                <th>Auftrags-Nr.</th>
                <td><?php echo htmlspecialchars($order['Auftrags-Nr.']); ?></td>
            </tr>
            <tr>
                <th>ST</th>
                <td><?php echo htmlspecialchars($order['ST']); ?></td>
            </tr>
            <tr>
                <th>Teile-Nr</th>
                <td><?php echo htmlspecialchars($order['Teile-Nr']); ?></td>
            </tr>
            <tr>
                <th>Bezeichnung 1</th>
                <td><?php echo htmlspecialchars($order['Bezeichnung 1']); ?></td>
            </tr>
            <tr>
                <th>Start</th>
                <td><?php echo htmlspecialchars($order['Start']); ?></td>
            </tr>
            <tr>
                <th>Ende</th>
                <td><?php echo htmlspecialchars($order['Ende']); ?></td>
            </tr>
            <tr>
                <th>Quantité Totale</th>
                <td><?php echo htmlspecialchars($order['qty totale']); ?></td>
            </tr>
            <tr>
                <th>Quantité Restante</th>
                <td><?php echo htmlspecialchars($order['qte restante']); ?></td>
            </tr>
            <tr>
                <th>Quantité Produite</th>
                <td><?php echo htmlspecialchars($order['Qte produite']); ?></td>
            </tr>
            <tr>
                <th>Temps Restant</th>
                <td><?php echo htmlspecialchars($order['time remaining']); ?></td>
            </tr>
            <tr>
                <th>Utilisateur</th>
                <td><?php echo htmlspecialchars($order['username']); ?></td>
            </tr>
            <tr>
                <th>Temps Produit</th>
                <td><?php echo htmlspecialchars($order['temps produit']); ?></td>
            </tr>
            <tr>
                <th>Temps Déclaré</th>
                <td><?php echo htmlspecialchars($order['temps declaré']); ?></td>
            </tr>
            <tr>
                <th>Produit Déclaré</th>
                <td><?php echo htmlspecialchars($order['produit declaree']); ?></td>
            </tr>
            <tr>
                <th>Date de Déclaration</th>
                <td><?php echo htmlspecialchars($order['date_declaration']); ?></td>
            </tr>
            <tr>
                <th>Heure de Déclaration</th>
                <td><?php echo htmlspecialchars($order['heure_declaration']); ?></td>
            </tr>
            <tr>
                <th>Date et Heure de production</th>
                <td><?php echo htmlspecialchars($order['date_enregistrement']); ?></td>
            </tr>
        </tbody>
    </table>
    <form method="post" class="mb-4">
        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
        <div class="mb-3">
            <label for="produit_declared" class="form-label">Produit Déclaré</label>
            <input type="number" id="produit_declared" name="produit_declared" class="form-control" oninput="updateRemainingQuantity()">
        </div>
        <div class="mb-3">
            <label for="date_declaration" class="form-label">Date de la Déclaration</label>
            <input type="date" id="date_declaration" name="date_declaration" class="form-control">
        </div>
        <div class="mb-3">
            <label for="heure_declaration" class="form-label">Heure de la Déclaration</label>
            <input type="time" id="heure_declaration" name="heure_declaration" class="form-control">
        </div>
        <input type="hidden" id="previous_qty_restante" name="previous_qty_restante" value="<?php echo $order['qte restante']; ?>">
        <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
    </form>
    <div>
        <strong>Quantité Restante - Produit Déclaré: </strong>
        <span id="remaining_qty_display"><?php echo htmlspecialchars($order['qte restante']); ?></span>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
