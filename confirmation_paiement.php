<?php
session_start();
include('db.php');

if (!isset($_SESSION['id_client']) || !isset($_GET['id_reservation'])) {
    header("Location: login.php");
    exit;
}

$id_reservation = $_GET['id_reservation'];

// Récupérer les détails de la réservation et du paiement
$requete = "SELECT r.*, v.marque, v.modele, p.mode_paiement, p.date_paiement
           FROM reservations r
           JOIN voitures v ON r.id_voiture = v.id_voiture
           JOIN paiements p ON r.id_reservation = p.id_reservation
           WHERE r.id_reservation = :id_reservation
           AND r.id_client = :id_client";
$stmt = $pdo->prepare($requete);
$stmt->execute([
    'id_reservation' => $id_reservation,
    'id_client' => $_SESSION['id_client']
]);
$reservation = $stmt->fetch();

if (!$reservation) {
    header("Location: tableau_de_bord_client.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Paiement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
        }
        .confirmation-box {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .confirmation-box i {
            font-size: 50px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .confirmation-box h2 {
            color: #28a745;
        }
        .details {
            text-align: left;
            margin: 20px 0;
            padding: 15px;
            background-color: #f1f8ff;
            border-radius: 5px;
        }
        .btn-dashboard {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn-dashboard:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-box">
            <i class="fas fa-check-circle"></i>
            <h2>Paiement Réussi !</h2>
            <p>Votre paiement a été traité avec succès.</p>
            
            <div class="details">
                <p><strong>Réservation #:</strong> <?php echo $reservation['id_reservation']; ?></p>
                <p><strong>Voiture:</strong> <?php echo $reservation['marque'] . ' ' . $reservation['modele']; ?></p>
                <p><strong>Montant:</strong> <?php echo number_format($reservation['montant'], 2, ',', ' ') . ' FCFA'; ?></p>
                <p><strong>Mode de paiement:</strong> <?php echo ucfirst(str_replace('_', ' ', $reservation['mode_paiement'])); ?></p>
                <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($reservation['date_paiement'])); ?></p>
            </div>
            
            <a href="tableau_de_bord_client.php" class="btn-dashboard">
                Retour au tableau de bord
            </a>
        </div>
    </div>
</body>
</html>