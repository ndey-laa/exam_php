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
$requete_total_reservations = "SELECT COUNT(*) FROM reservations";
$total_reservations = $pdo->query($requete_total_reservations)->fetchColumn();
$total_pages = ceil($total_reservations / $par_page); 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$page = ($page > $total_pages) ? $total_pages : $page;
$offset = ($page - 1) * $par_page; 

$requete_reservations = "SELECT * FROM reservations LIMIT $par_page OFFSET $offset";
$reservations = $pdo->query($requete_reservations)->fetchAll();

if (isset($_GET['download']) && $_GET['download'] == 'reservations') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'ID Client');
    $sheet->setCellValue('B1', 'Voiture');
    $sheet->setCellValue('C1', 'Date Début');
    $sheet->setCellValue('D1', 'Date Fin');
    $sheet->setCellValue('E1', 'Statut');
    $sheet->setCellValue('F1', 'Montant');
    $sheet->setCellValue('G1', 'Statut Paiement');

    $row = 2;
    foreach ($reservations as $reservation) {
        $sheet->setCellValue('A' . $row, $reservation['id_client']);
        $sheet->setCellValue('B' . $row, $reservation['id_voiture']);
        $sheet->setCellValue('C' . $row, $reservation['date_debut']);
        $sheet->setCellValue('D' . $row, $reservation['date_fin']);
        $sheet->setCellValue('E' . $row, $reservation['statut']);
        $sheet->setCellValue('F' . $row, $reservation['montant']);
        $sheet->setCellValue('G' . $row, $reservation['statut_paiement']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="rapport_reservations.xlsx"');
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
    <title>Rapport des Réservations</title>
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
            min-width: 800px;
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

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-paid {
            background-color: #d1ecf1;
            color: #0c5460;
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
        <h2>Rapport des Réservations</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?>
                <i class="fas fa-user"></i>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <a href="?download=reservations" class="download-btn">
            <i class="fas fa-file-excel"></i> Télécharger Excel
        </a>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Voiture</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Statut</th>
                        <th>Montant</th>
                        <th>Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['id_client']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['id_voiture']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reservation['date_debut'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reservation['date_fin'])); ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php echo strtolower($reservation['statut']) === 'confirmée' ? 'status-confirmed' : 
                                          (strtolower($reservation['statut']) === 'annulée' ? 'status-cancelled' : 'status-pending'); ?>">
                                    <?php echo htmlspecialchars($reservation['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($reservation['montant'], 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <span class="status-badge 
                                    <?php echo strtolower($reservation['statut_paiement']) === 'payé' ? 'status-paid' : 'status-pending'; ?>">
                                    <?php echo htmlspecialchars($reservation['statut_paiement']); ?>
                                </span>
                            </td>
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