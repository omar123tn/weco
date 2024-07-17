<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['auftragsNr'])) {
    $auftragsNr = mysqli_real_escape_string($conn, $_GET['auftragsNr']);
    $sql = "SELECT * FROM `order_updates` WHERE `Auftrags-Nr.` = '$auftragsNr'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Export to Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="orders_' . $auftragsNr . '.xls"');

    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>AG</th>';
    echo '<th>Abt.</th>';
    echo '<th>Centre de travail</th>';
    echo '<th>Designation AG</th>';
    echo '<th>Numero de commande</th>';
    echo '<th>ST</th>';
    echo '<th>Numero de piece</th>';
    echo '<th>Designation 1</th>';
    echo '<th>Debut</th>';
    echo '<th>Fin</th>';
    echo '<th>Quantite totale</th>';
    echo '<th>Quantite restante</th>';
    echo '<th>Quantite produite</th>';
    echo '<th>Temps restant</th>';
    echo '<th>Temps produit</th>';
    echo '<th>Temps declare</th>';
    echo '<th>Produit declare</th>';
    echo '<th>Nom utilisateur</th>';
    echo '<th>Date Enregistrement</th>';
    echo '<th>Date et Heure de production</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($orders as $order) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($order['AG']) . '</td>';
        echo '<td>' . htmlspecialchars($order['Abt.']) . '</td>';
        echo '<td>' . htmlspecialchars($order['work center']) . '</td>';
        echo '<td>' . htmlspecialchars($order['AG Bezeichnung']) . '</td>';
        echo '<td>' . htmlspecialchars($order['Auftrags-Nr.']) . '</td>';
        echo '<td>' . htmlspecialchars($order['ST']) . '</td>';
        echo '<td>' . htmlspecialchars($order['Teile-Nr']) . '</td>';
        echo '<td>' . htmlspecialchars($order['Bezeichnung 1']) . '</td>';
        echo '<td>' . htmlspecialchars($order['Start']) . '</td>';
        echo '<td>' . htmlspecialchars($order['Ende']) . '</td>';
        echo '<td>' . htmlspecialchars(trim($order['qty totale'])) . '</td>';
        echo '<td>' . htmlspecialchars(trim($order['qte restante'])) . '</td>';
        echo '<td>' . htmlspecialchars($order['Qte produite']) . '</td>';
        echo '<td>' . htmlspecialchars($order['time remaining']) . '</td>';
        echo '<td>' . htmlspecialchars($order['temps produit']) . '</td>';
        echo '<td>' . htmlspecialchars($order['temps declaré']) . '</td>';
        echo '<td>' . htmlspecialchars($order['produit declaree']) . '</td>';
        echo '<td>' . htmlspecialchars($order['username']) . '</td>';
        echo '<td>' . htmlspecialchars($order['date_enregistrement']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
?>
