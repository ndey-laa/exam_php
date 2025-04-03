<?php
session_start();
include('db.php');
require 'vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

$requete_admin = "SELECT prenom, nom FROM administrateurs WHERE id_admin = :id_admin";
$stmt_admin = $pdo->prepare($requete_admin);
$stmt_admin->execute(['id_admin' => $_SESSION['id_admin']]);
$admin = $stmt_admin->fetch();

$par_page = 10; 
$requete_total_voitures = "SELECT COUNT(*) FROM voitures";
$total_voitures = $pdo->query($requete_total_voitures)->fetchColumn();
$total_pages = ceil($total_voitures / $par_page); 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$page = ($page > $total_pages) ? $total_pages : $page;
$offset = ($page - 1) * $par_page; 

$requete_voitures = "SELECT * FROM voitures LIMIT $par_page OFFSET $offset";
$voitures = $pdo->query($requete_voitures)->fetchAll();

if (isset($_GET['download']) && $_GET['download'] == 'voitures') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Marque');
    $sheet->setCellValue('B1', 'Modèle');
    $sheet->setCellValue('C1', 'Année');
    $sheet->setCellValue('D1', 'Immatriculation');
    $sheet->setCellValue('E1', 'Statut');
    $sheet->setCellValue('F1', 'Tarif Journalier');

    $row = 2;
    foreach ($voitures as $voiture) {
        $sheet->setCellValue('A' . $row, $voiture['marque']);
        $sheet->setCellValue('B' . $row, $voiture['modele']);
        $sheet->setCellValue('C' . $row, $voiture['annee']);
        $sheet->setCellValue('D' . $row, $voiture['plaque_immatriculation']);
        $sheet->setCellValue('E' . $row, $voiture['statut']);
        $sheet->setCellValue('F' . $row, $voiture['tarif_journalier']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="rapport_voitures.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Voitures</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #343a40;
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            flex-wrap: wrap;
        }

        header h2 {
            font-size: 1.3rem;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-deconnexion {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
            padding: 12px 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .download-btn i {
            margin-right: 8px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            background-color: #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
            }
            
            .header-right {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <header>
        <h2>Rapport des Voitures</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?>
                <i class="fas fa-user"></i>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <a href="?download=voitures" class="download-btn">
            <i class="fas fa-file-excel"></i> Télécharger Excel
        </a>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Année</th>
                        <th>Immatriculation</th>
                        <th>Statut</th>
                        <th>Tarif (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voitures as $voiture) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voiture['marque']); ?></td>
                            <td><?php echo htmlspecialchars($voiture['modele']); ?></td>
                            <td><?php echo htmlspecialchars($voiture['annee']); ?></td>
                            <td><?php echo htmlspecialchars($voiture['plaque_immatriculation']); ?></td>
                            <td><?php echo htmlspecialchars($voiture['statut']); ?></td>
                            <td><?php echo number_format($voiture['tarif_journalier'], 0, ',', ' '); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1) { ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>