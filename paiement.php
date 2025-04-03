<?php
session_start();
include('db.php');

if (!isset($_GET['id_reservation'])) {
    header("Location: tableau_de_bord_client.php");
    exit;
}

// Récupérer les informations du client connecté
$requete_client = "SELECT prenom, nom FROM clients WHERE id_client = :id_client";
$stmt_client = $pdo->prepare($requete_client);
$stmt_client->execute(['id_client' => $_SESSION['id_client']]);
$client = $stmt_client->fetch();

$id_reservation = $_GET['id_reservation'];

$requete_reservation = "SELECT r.*, v.marque, v.modele 
                        FROM reservations r
                        JOIN voitures v ON r.id_voiture = v.id_voiture
                        WHERE r.id_reservation = :id_reservation AND r.id_client = :id_client";
$stmt_reservation = $pdo->prepare($requete_reservation);
$stmt_reservation->execute([
    'id_reservation' => $id_reservation,
    'id_client' => $_SESSION['id_client']
]);

$reservation = $stmt_reservation->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    header("Location: tableau_de_bord_client.php");
    exit;
}


if (isset($_POST['payer'])) {
    // Vérifier que le mode de paiement est sélectionné
    if (empty($_POST['mode_paiement'])) {
        $_SESSION['error'] = "Veuillez sélectionner un mode de paiement";
        header("Location: paiement.php?id_reservation=$id_reservation");
        exit;
    }

    // Enregistrer le paiement dans la base de données
  // Modifiez cette partie du code
  $requete_paiement = "INSERT INTO paiements (
    id_reservation, 
    montant, 
    mode_paiement, 
    date_paiement
) VALUES (
    :id_reservation,
    :montant,
    :mode_paiement,
    NOW()
)";
    
    $stmt_paiement = $pdo->prepare($requete_paiement);
    $stmt_paiement->execute([
        'id_reservation' => $id_reservation,
        'montant' => $reservation['montant'],
        'mode_paiement' => $_POST['mode_paiement']
    ]);

    // Mettre à jour le statut de la réservation
    $requete_update = "UPDATE reservations SET statut_paiement = 'payé' WHERE id_reservation = :id_reservation";
    $stmt_update = $pdo->prepare($requete_update);
    $stmt_update->execute(['id_reservation' => $id_reservation]);

    // Ajouter une notification pour le client
    $message = "Paiement confirmé pour la réservation #$id_reservation (".$_POST['mode_paiement'].")";
    $requete_notif = "INSERT INTO notifications (id_client, message, date_creation) 
                     VALUES (:id_client, :message, NOW())";
    $stmt_notif = $pdo->prepare($requete_notif);
    $stmt_notif->execute([
        'id_client' => $_SESSION['id_client'],
        'message' => $message
    ]);

    header("Location: confirmation_paiement.php?id_reservation=$id_reservation");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement de la Réservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles de base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header responsive */
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
            position: relative;
        }

        .user-info i {
            color: white;
            font-size: 1.2rem;
        }

        .btn-deconnexion {
            background-color: rgb(0, 106, 255);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-deconnexion:hover {
            background-color: rgb(5, 92, 173);
        }

        /* Contenu principal */
        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-container h3 {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        .reservation-details {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .reservation-details p {
            margin: 10px 0;
        }

        .payment-options {
            margin: 25px 0;
        }

        .payment-method {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #007bff;
            background-color: #f0f7ff;
        }

        .payment-method input[type="radio"] {
            margin-right: 15px;
        }

        .payment-method img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
        }

        .payment-method label {
            font-weight: bold;
            cursor: pointer;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            display: inline-block;
            padding: 10px 20px;
            color: #007bff;
            text-decoration: none;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .back-link a:hover {
            background-color: #007bff;
            color: white;
        }

        /* Messages d'erreur */
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            header h2 {
                width: 100%;
                text-align: center;
            }
            
            .header-right {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .container {
                padding: 0 15px;
            }
            
            .form-container {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .payment-method {
                flex-direction: column;
                text-align: center;
            }
            
            .payment-method img {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .form-container h3 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h2>Paiement de la Réservation</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?>
                <i class="fas fa-user"></i>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h3>Veuillez procéder au paiement</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="reservation-details">
                <p><strong>Voiture :</strong> <?php echo htmlspecialchars($reservation['marque'] . ' ' . $reservation['modele']); ?></p>
                <p><strong>Dates :</strong> Du <?php echo date('d/m/Y', strtotime($reservation['date_debut'])); ?> au <?php echo date('d/m/Y', strtotime($reservation['date_fin'])); ?></p>
                <p><strong>Montant :</strong> <?php echo number_format($reservation['montant'], 0, ',', ' '); ?> FCFA</p>
            </div>

            <form method="POST">
                <div class="payment-options">
                    <h4>Choisissez votre mode de paiement :</h4>
                    
                    <div class="payment-method">
                        <input type="radio" name="mode_paiement" value="orange_money" id="orange_money" required>
                        <img src="images/Orange-Money-logo.png" alt="Orange Money">
                        <label for="orange_money">Orange Money</label>
                    </div>
                    
                    <div class="payment-method">
                        <input type="radio" name="mode_paiement" value="wave" id="wave" required>
                        <img src="images/wave@221@-P-2021-06-30_00-18-27wave_logo_2.png" alt="Wave">
                        <label for="wave">Wave</label>
                    </div>
                    
                    <div class="payment-method">
                        <input type="radio" name="mode_paiement" value="paypal" id="paypal" required>
                        <img src="images/1666211884.png" alt="PayPal">
                        <label for="paypal">PayPal</label>
                    </div>
                </div>

                <button type="submit" name="payer">Payer maintenant</button>
            </form>
        </div>

        <div class="back-link">
            <a href="tableau_de_bord_client.php">Retour au tableau de bord</a>
        </div>
    </div>
</body>
</html>

