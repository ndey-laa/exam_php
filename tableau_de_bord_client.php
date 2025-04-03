<?php
session_start();
include('db.php');


// Gestion des marquages de notifications comme lues
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'marquer_lues' && isset($_SESSION['id_client'])) {
        $requete = "UPDATE notifications SET lu = 1 WHERE id_client = :id_client";
        $stmt = $pdo->prepare($requete);
        $stmt->execute(['id_client' => $_SESSION['id_client']]);
        exit; // On arrête le script ici pour les requêtes AJAX
    }
    
    if ($_GET['action'] == 'verifier_notifs' && isset($_SESSION['id_client'])) {
        $requete = "SELECT COUNT(*) as count FROM notifications 
                   WHERE id_client = :id_client AND lu = 0";
        $stmt = $pdo->prepare($requete);
        $stmt->execute(['id_client' => $_SESSION['id_client']]);
        $result = $stmt->fetch();
        echo json_encode(['nouvelles' => $result['count'] > 0]);
        exit;
    }
}


if (!isset($_SESSION['id_client'])) {
    header("Location: login.php");
    exit;
}



// Récupérer les informations du client connecté
$requete_client = "SELECT prenom, nom FROM clients WHERE id_client = :id_client";
$stmt_client = $pdo->prepare($requete_client);
$stmt_client->execute(['id_client' => $_SESSION['id_client']]);
$client = $stmt_client->fetch();

// Récupérer les réservations du client
$requete = "SELECT r.id_reservation, r.date_debut, r.date_fin, r.statut, v.marque, v.modele, v.image 
            FROM reservations r 
            INNER JOIN voitures v ON r.id_voiture = v.id_voiture 
            WHERE r.id_client = :id_client";
$stmt = $pdo->prepare($requete);
$stmt->execute(['id_client' => $_SESSION['id_client']]);
$reservations = $stmt->fetchAll();

// Récupérer les notifications du client
$requete_notifs = "SELECT message, date_creation, lu FROM notifications WHERE id_client = :id_client ORDER BY date_creation DESC";
$stmt_notifs = $pdo->prepare($requete_notifs);
$stmt_notifs->execute(['id_client' => $_SESSION['id_client']]);
$notifications = $stmt_notifs->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Client</title>
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
            background-color: #f4f4f9;
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
            cursor: pointer;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .container h3 {
            font-size: 1.3rem;
            color: #007bff;
            margin: 15px 0;
            text-align: center;
        }

        /* Tableau responsive */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        tr:hover td {
            background-color: #e9ecef;
        }

        td img {
            width: 80px;
            height: auto;
            border-radius: 4px;
        }

        /* Notifications */
        .notification-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 280px;
            max-width: 90vw;
            z-index: 1001;
        }

        .notification-menu h4 {
            background: #007bff;
            color: white;
            padding: 10px;
            margin: 0;
            text-align: center;
            font-size: 1rem;
            position: sticky;
            top: 0;
        }

        .notification-menu ul {
            list-style: none;
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-menu li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        .notification-menu li:last-child {
            border-bottom: none;
        }

        .notification-menu li span {
            display: block;
            color: #333;
            margin-bottom: 3px;
        }

        .notification-menu li small {
            display: block;
            font-size: 0.8rem;
            color: #666;
        }

        /* Boutons */
        .btn-reservation {
            display: inline-block;
            background-color: rgb(0, 106, 255);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px 0;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-reservation:hover {
            background-color: rgb(5, 92, 173);
            transform: translateY(-2px);
        }

        /* Message quand pas de réservations */
        .no-reservations {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 20px 0;
            text-align: center;
        }

        /* Badge de notification */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
            
            .user-info {
                margin-right: 0;
            }
            
            th, td {
                padding: 8px;
                font-size: 0.9rem;
            }
            
            td img {
                width: 60px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            .header-right {
                flex-direction: column;
                align-items: center;
            }
            
            .notification-menu {
                width: 250px;
                right: 50%;
                transform: translateX(50%);
            }
        }
    </style>
</head>
<body>
    <header>
        <h2>Tableau de bord</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?>
                <i class="fas fa-user" id="user-icon"></i>
                <div id="notification-menu" class="notification-menu">
                    <h4>Notifications</h4>
                    <ul>
                        <?php if (count($notifications) > 0) { ?>
                            <?php foreach ($notifications as $notif) { ?>
                                <li>
                                    <span><?php echo htmlspecialchars($notif['message']); ?></span>
                                    <small><?php echo date('d/m/Y H:i', strtotime($notif['date_creation'])); ?></small>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li>Aucune notification</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <h3>Vos réservations</h3>
        
        <?php if (count($reservations) > 0) { ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Voiture</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation) { ?>
                            <tr>
                                <td>
                                    <img src="./images/<?php echo htmlspecialchars($reservation['image']); ?>" alt="<?php echo htmlspecialchars($reservation['marque']); ?>">
                                    <?php echo htmlspecialchars($reservation['marque'] . ' ' . htmlspecialchars($reservation['modele'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($reservation['date_debut']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['date_fin']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['statut']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="no-reservations">
                <p>Vous n'avez aucune réservation pour le moment.</p>
            </div>
        <?php } ?>

        <a href="reservation.php" class="btn-reservation">Faire une réservation</a>
    </div>

    <script>
        // Gestion des notifications
        const userIcon = document.getElementById("user-icon");
        const notificationMenu = document.getElementById("notification-menu");
        
        userIcon.addEventListener("click", function(event) {
            event.stopPropagation();
            notificationMenu.style.display = notificationMenu.style.display === "block" ? "none" : "block";
        });
        
        document.addEventListener("click", function() {
            notificationMenu.style.display = "none";
        });
        
        // Vérifier les nouvelles notifications
        function checkNotifications() {
            fetch('?action=verifier_notifs')
                .then(response => response.json())
                .then(data => {
                    if(data.nouvelles) {
                        // Ajouter un badge
                        let badge = userIcon.querySelector('.notification-badge');
                        if (!badge) {
                            badge = document.createElement('span');
                            badge.className = 'notification-badge';
                            badge.textContent = '!';
                            userIcon.style.position = 'relative';
                            userIcon.appendChild(badge);
                        }
                    }
                });
        }
        
        // Marquer comme lues
        notificationMenu.addEventListener("click", function() {
            fetch('?action=marquer_lues');
            const badge = userIcon.querySelector('.notification-badge');
            if (badge) badge.remove();
        });
        
        // Vérifier toutes les 30 secondes
        setInterval(checkNotifications, 30000);
        checkNotifications(); // Vérifier au chargement
    </script>
</body>
</html>