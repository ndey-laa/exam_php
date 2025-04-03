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

// Récupération des informations de l'admin
$requete_admin = "SELECT prenom, nom FROM administrateurs WHERE id_admin = :id_admin";
$stmt_admin = $pdo->prepare($requete_admin);
$stmt_admin->execute(['id_admin' => $_SESSION['id_admin']]);
$admin = $stmt_admin->fetch();

// Filtres
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'mois';
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';
$mode_paiement = isset($_GET['mode_paiement']) ? $_GET['mode_paiement'] : '';

// Requête pour les revenus
$sql = "SELECT 
            r.id_reservation,
            c.nom AS client_nom,
            c.prenom AS client_prenom,
            v.marque AS voiture_marque,
            v.modele AS voiture_modele,
            r.date_debut,
            r.date_fin,
            p.montant,
            p.mode_paiement,
            p.date_paiement AS date_reservation
        FROM reservations r
        JOIN clients c ON r.id_client = c.id_client
        JOIN voitures v ON r.id_voiture = v.id_voiture
        JOIN paiements p ON r.id_reservation = p.id_reservation
        WHERE r.statut_paiement = 'Payé'";

// Application des filtres

$stmt = $pdo->prepare($sql);

if (!empty($date_debut) && !empty($date_fin)) {
    $stmt->bindValue(':date_debut', $date_debut);
    $stmt->bindValue(':date_fin', $date_fin);
}

if (!empty($mode_paiement)) {
    $stmt->bindValue(':mode_paiement', $mode_paiement);
}

$stmt->execute();
$revenus = $stmt->fetchAll();

// Calcul des totaux
$total_general = 0;
foreach ($revenus as $revenu) {
    $total_general += $revenu['montant'];
}

// Export Excel
if (isset($_GET['download']) && $_GET['download'] == 'revenus') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // En-têtes
    $sheet->setCellValue('A1', 'ID Réservation');
    $sheet->setCellValue('B1', 'Client');
    $sheet->setCellValue('C1', 'Voiture');
    $sheet->setCellValue('D1', 'Date Début');
    $sheet->setCellValue('E1', 'Date Fin');
    $sheet->setCellValue('F1', 'Montant (FCFA)');
    $sheet->setCellValue('G1', 'Mode Paiement');
    $sheet->setCellValue('H1', 'Date Paiement');

    // Données
    $row = 2;
    foreach ($revenus as $revenu) {
        $sheet->setCellValue('A' . $row, $revenu['id_reservation']);
        $sheet->setCellValue('B' . $row, $revenu['client_prenom'] . ' ' . $revenu['client_nom']);
        $sheet->setCellValue('C' . $row, $revenu['voiture_marque'] . ' ' . $revenu['voiture_modele']);
        $sheet->setCellValue('D' . $row, $revenu['date_debut']);
        $sheet->setCellValue('E' . $row, $revenu['date_fin']);
        $sheet->setCellValue('F' . $row, $revenu['montant']);
        $sheet->setCellValue('G' . $row, $revenu['mode_paiement']);
        $sheet->setCellValue('H' . $row, $revenu['date_reservation']);
        $row++;
    }

    // Total
    $sheet->setCellValue('E' . $row, 'TOTAL:');
    $sheet->setCellValue('F' . $row, $total_general);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="rapport_revenus.xlsx"');
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
    <title>Rapport des Revenus - AutoLoc</title>
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
      
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        select, input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        /* Statistiques */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }

        /* Tableau */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
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

        tr.total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        /* Bouton d'export */
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
        <h2>Rapport des Revenus</h2>
        <div class="header-right">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?>
            </div>
            <a href="logout.php" class="btn-deconnexion">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </header>

    <div class="container">
        
        <!-- Statistiques -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Revenus totaux</h3>
                <div class="value"><?php echo number_format($total_general, 0, ',', ' '); ?> FCFA</div>
            </div>
            <div class="stat-card">
                <h3>Nombre de transactions</h3>
                <div class="value"><?php echo count($revenus); ?></div>
            </div>
            <div class="stat-card">
                <h3>Moyenne par transaction</h3>
                <div class="value">
                    <?php 
                        $moyenne = count($revenus) > 0 ? $total_general / count($revenus) : 0;
                        echo number_format($moyenne, 0, ',', ' ') . ' FCFA';
                    ?>
                </div>
            </div>
        </div>

        <!-- Bouton d'export -->
        <a href="?download=revenus<?php 
            echo !empty($periode) ? '&periode=' . urlencode($periode) : '';
            echo !empty($date_debut) ? '&date_debut=' . urlencode($date_debut) : '';
            echo !empty($date_fin) ? '&date_fin=' . urlencode($date_fin) : '';
            echo !empty($mode_paiement) ? '&mode_paiement=' . urlencode($mode_paiement) : '';
        ?>" class="download-btn">
            <i class="fas fa-file-excel"></i> Exporter vers Excel
        </a>

        <!-- Tableau -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Réservation</th>
                        <th>Client</th>
                        <th>Voiture</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Montant (FCFA)</th>
                        <th>Mode Paiement</th>
                        <th>Date Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenus as $revenu): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($revenu['id_reservation']); ?></td>
                            <td><?php echo htmlspecialchars($revenu['client_prenom'] . ' ' . $revenu['client_nom']); ?></td>
                            <td><?php echo htmlspecialchars($revenu['voiture_marque'] . ' ' . $revenu['voiture_modele']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($revenu['date_debut'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($revenu['date_fin'])); ?></td>
                            <td><?php echo number_format($revenu['montant'], 0, ',', ' '); ?></td>
                            <td><?php echo htmlspecialchars($revenu['mode_paiement']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($revenu['date_reservation'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="5">TOTAL</td>
                        <td><?php echo number_format($total_general, 0, ',', ' '); ?> FCFA</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Afficher/masquer les champs de date selon la période sélectionnée
        document.getElementById('periode').addEventListener('change', function() {
            const showDateFilters = this.value === 'custom';
            document.querySelectorAll('.date-filters').forEach(el => {
                el.style.display = showDateFilters ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>