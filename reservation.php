<?php
session_start();
include('db.php');

if (!isset($_SESSION['id_client'])) {
    header("Location: login.php");
    exit;
}

// Récupérer les informations du client connecté
$requete_client = "SELECT prenom, nom FROM clients WHERE id_client = :id_client";
$stmt_client = $pdo->prepare($requete_client);
$stmt_client->execute(['id_client' => $_SESSION['id_client']]);
$client = $stmt_client->fetch();

if (isset($_POST['reserver'])) {
    $id_voiture = $_POST['id_voiture'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    $requete_tarif = "SELECT tarif_journalier FROM voitures WHERE id_voiture = :id_voiture";
    $stmt_tarif = $pdo->prepare($requete_tarif);
    $stmt_tarif->execute(['id_voiture' => $id_voiture]);
    $tarif_voiture = $stmt_tarif->fetch(PDO::FETCH_ASSOC);

    $date_debut = new DateTime($date_debut);
    $date_fin = new DateTime($date_fin);
    $interval = $date_debut->diff($date_fin);
    $jours_location = $interval->days;

    $montant = $jours_location * $tarif_voiture['tarif_journalier'];

    $requete = "INSERT INTO reservations (id_client, id_voiture, date_debut, date_fin, montant, statut_paiement) 
                VALUES (:id_client, :id_voiture, :date_debut, :date_fin, :montant, 'en attente')";
    $stmt = $pdo->prepare($requete);
    $stmt->execute([
        'id_client' => $_SESSION['id_client'],
        'id_voiture' => $id_voiture,
        'date_debut' => $date_debut->format('Y-m-d'),
        'date_fin' => $date_fin->format('Y-m-d'),
        'montant' => $montant
    ]);

    $id_reservation = $pdo->lastInsertId();
    header("Location: paiement.php?id_reservation=$id_reservation");
    exit;
}

$requete = "SELECT * FROM voitures WHERE statut = 'disponible'";
$voitures = $pdo->query($requete)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une voiture</title>
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
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .container h3 {
            font-size: 1.3rem;
            color: #007bff;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Liste des voitures */
        .voitures-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .voiture-item {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .voiture-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .voiture-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .voiture-item p {
            margin: 8px 0;
            font-size: 0.95rem;
        }

        /* Formulaire */
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        select, input[type="date"], button {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 0.95rem;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 12px;
            font-weight: bold;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Lien de retour */
        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            display: inline-block;
            font-size: 1rem;
            color: #007bff;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #007bff;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            background-color: #007bff;
            color: white;
        }

        /* Animation pour le chargement */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .voiture-item, .form-container {
            animation: fadeIn 0.5s ease-out forwards;
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
            
            .voitures-list {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            .voitures-list {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 15px;
            }
            
            button {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h2>Réservation d'une voiture</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?>
                <i class="fas fa-user"></i>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <h3>Voici les voitures disponibles</h3>
        <div class="voitures-list">
            <?php foreach ($voitures as $voiture) { ?>
                <div class="voiture-item">
                    <img src="images/<?php echo htmlspecialchars($voiture['image']); ?>" alt="<?php echo htmlspecialchars($voiture['marque'] . ' ' . htmlspecialchars($voiture['modele'])); ?>">
                    <p><?php echo htmlspecialchars($voiture['marque'] . ' ' . htmlspecialchars($voiture['modele'])); ?></p>
                    <p>Tarif journalier : <?php echo number_format($voiture['tarif_journalier'], 2, ',', ' ') . ' FCFA'; ?></p>
                </div>
            <?php } ?>
        </div>

        <div class="form-container">
            <h3>Réservez votre voiture</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="id_voiture">Voiture :</label>
                    <select name="id_voiture" id="id_voiture" required>
                        <?php foreach ($voitures as $voiture) { ?>
                            <option value="<?php echo htmlspecialchars($voiture['id_voiture']); ?>">
                                <?php echo htmlspecialchars($voiture['marque'] . ' ' . htmlspecialchars($voiture['modele'])); ?> - 
                                <?php echo number_format($voiture['tarif_journalier'], 2, ',', ' ') . ' FCFA'; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" name="date_debut" id="date_debut" required>
                </div>
                
                <div class="form-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" name="date_fin" id="date_fin" required>
                </div>

                <button type="submit" name="reserver">Réserver</button>
            </form>
        </div>

        <div class="back-link">
            <a href="tableau_de_bord_client.php">Retour au tableau de bord</a>
        </div>
    </div>
</body>
</html>