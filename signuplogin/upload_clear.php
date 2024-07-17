<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle file upload
if (isset($_POST['upload'])) {
    if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == 0) {
        $fileTmpPath = $_FILES['excelFile']['tmp_name'];
        $fileName = $_FILES['excelFile']['name'];
        $fileSize = $_FILES['excelFile']['size'];
        $fileType = $_FILES['excelFile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['xls', 'xlsx', 'csv'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploaded_files/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                if ($fileExtension == 'csv') {
                    if (($handle = fopen($dest_path, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                            $sql = "INSERT INTO `open_orders_26.06` (`AG`, `Abt.`, `work center`, `AG Bezeichnung`, `Auftrags-Nr.`, `ST`, `Teile-Nr`, `Bezeichnung 1`, `Start`, `Ende`, `qty totale`, `qté restante`, `Qté produite`, `time remaining`, `temps produit`, `produit declaré`, `username`, `produit travaille`) 
                                    VALUES ('" . implode("','", $data) . "')";
                            if (!mysqli_query($conn, $sql)) {
                                die("Query Failed: " . mysqli_error($conn));
                            }
                        }
                        fclose($handle);
                        echo "CSV file is successfully uploaded and data inserted.";
                    } else {
                        echo "Error opening the CSV file.";
                    }
                } else {
                    require 'vendor/autoload.php';

                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($dest_path);
                    $sheetData = $spreadsheet->getActiveSheet()->toArray();

                    foreach ($sheetData as $row) {
                        $row = array_map(function($value) {
                            return str_replace(';', ',', $value);
                        }, $row);

                        $sql = "INSERT INTO `open_orders_26.06` (`AG`, `Abt.`, `work center`, `AG Bezeichnung`, `Auftrags-Nr.`, `ST`, `Teile-Nr`, `Bezeichnung 1`, `Start`, `Ende`, `qty totale`, `qté restante`, `Qté produite`, `time remaining`, `temps produit`, `produit declaré`, `username`, `produit travaille`) 
                                VALUES ('" . implode("','", $row) . "')";
                        if (!mysqli_query($conn, $sql)) {
                            die("Query Failed: " . mysqli_error($conn));
                        }
                    }
                    echo "Excel file is successfully uploaded and data inserted.";
                }
            } else {
                echo "There was an error moving the uploaded file.";
            }
        } else {
            echo "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}

// Handle table clearing
if (isset($_POST['clear_table'])) {
    $sql = "TRUNCATE TABLE `open_orders_26.06`";
    if (mysqli_query($conn, $sql)) {
        echo "Table cleared successfully.";
    } else {
        die("Query Failed: " . mysqli_error($conn));
    }
}

// Handle data export
if (isset($_POST['export'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    $sql = "SELECT * FROM `open_orders_26.06` WHERE `date_declaration` BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="exported_data_' . date('Ymd_His') . '.xls"');

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
        echo '<th>Date de declaration</th>';
        echo '<th>Heure de declaration</th>';
        echo '<th>Date et Heure de production</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['AG']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Abt.']) . '</td>';
            echo '<td>' . htmlspecialchars($row['work center']) . '</td>';
            echo '<td>' . htmlspecialchars($row['AG Bezeichnung']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Auftrags-Nr.']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ST']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Teile-Nr']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Bezeichnung 1']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Start']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Ende']) . '</td>';
            echo '<td>' . htmlspecialchars(trim($row['qty totale'])) . '</td>';
            echo '<td>' . htmlspecialchars(trim($row['qte restante'])) . '</td>';
            echo '<td>' . htmlspecialchars($row['Qte produite']) . '</td>';
            echo '<td>' . htmlspecialchars($row['time remaining']) . '</td>';
            echo '<td>' . htmlspecialchars($row['temps produit']) . '</td>';
            echo '<td>' . htmlspecialchars($row['temps declaré']) . '</td>';
            echo '<td>' . htmlspecialchars($row['produit declaree']) . '</td>';
            echo '<td>' . htmlspecialchars($row['username']) . '</td>';
            echo '<td>' . htmlspecialchars($row['date_declaration']) . '</td>';
            echo '<td>' . htmlspecialchars($row['heure_declaration']) . '</td>';
            echo '<td>' . htmlspecialchars($row['date_enregistrement']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        exit();
    } else {
        echo "Query Failed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Télécharger et Supprimer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background-color: white;
            padding: 15px 0;
        }

        .header img {
            height: 55px;
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
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .dropdown-menu {
            overflow: hidden;
        }

        .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .navbar-light .navbar-toggler {
            color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .dropdown-item:hover {
            transform: scale(1.05);
            transform-origin: top;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        .new-field {
            background-color: #f9f871;
        }

        .navbar-light .navbar-nav .nav-link {
            color: black;
        }

        .navbar-light .navbar-nav .nav-link:hover {
            color: blueviolet;
        }

        .navbar-light .navbar-toggler {
            color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .table {
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

        .btn-outline-danger:hover,
        .btn-outline-danger:focus {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .text-red {
            color: red;
        }
    </style>
</head>

<body>
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
        <h2 class="center-text">Télécharger et Supprimer</h2>
        <form action="" method="post" enctype="multipart/form-data" class="my-4">
            <div class="mb-3">
                <label for="excelFile" class="form-label">Télécharger un fichier Excel ou CSV :</label>
                <input type="file" name="excelFile" id="excelFile" class="form-control" accept=".xls, .xlsx, .csv">
            </div>
            <button type="submit" name="upload" class="btn btn-primary">Télécharger le fichier</button>
        </form>

        <form method="post" class="my-4">
            <button type="submit" name="clear_table" class="btn btn-danger">Supprimer toutes les données</button>
        </form>

        <h2 class="center-text">Exporter les données</h2>
        <form method="post" class="my-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Date de début :</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Date de fin :</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="export" class="btn btn-success">Exporter les données</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
