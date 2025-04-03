<?php
session_start();
include('db.php');

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

$par_page = 10;
$requete_total_voitures = "SELECT COUNT(*) FROM voitures";
$total_voitures = $pdo->query($requete_total_voitures)->fetchColumn();
$total_pages = ceil($total_voitures / $par_page); 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page > $total_pages) ? $total_pages : $page;
$offset = ($page - 1) * $par_page; 

$requete_voitures = "SELECT * FROM voitures LIMIT $par_page OFFSET $offset";
$voitures = $pdo->query($requete_voitures)->fetchAll();

$requete_admin = "SELECT prenom, nom FROM administrateurs WHERE id_admin = :id_admin";
$stmt_admin = $pdo->prepare($requete_admin);
$stmt_admin->execute(['id_admin' => $_SESSION['id_admin']]);
$admin = $stmt_admin->fetch();

// Récupération des réservations
$requete_reservations = "SELECT r.id_reservation, c.nom, c.prenom, v.marque, v.modele, r.date_debut, r.date_fin, r.statut
                        FROM reservations r
                        JOIN clients c ON r.id_client = c.id_client
                        JOIN voitures v ON r.id_voiture = v.id_voiture";
$reservations = $pdo->query($requete_reservations)->fetchAll();

// Récupération des paiements
$requete_paiements = "SELECT p.id_paiement, r.id_reservation, p.montant, p.date_paiement, p.mode_paiement
                      FROM paiements p
                      JOIN reservations r ON p.id_reservation = r.id_reservation";
$paiements = $pdo->query($requete_paiements)->fetchAll();

// On récupère l'id de la réservation confirmée par l'admin
if (isset($_GET['id_reservation'])) {
    $id_reservation = $_GET['id_reservation'];

    // On met à jour le statut de la réservation à 'confirmée'
    $update_query = "UPDATE reservations SET statut = 'confirmée' WHERE id_reservation = :id_reservation";
    $stmt = $pdo->prepare($update_query);
    $stmt->execute(['id_reservation' => $id_reservation]);

    // On envoie une notification au client
    $notification_message = "✅ Votre réservation a été confirmée avec succès !";
    $client_query = "UPDATE reservations SET notification_client = :notification_message WHERE id_reservation = :id_reservation";
    $stmt = $pdo->prepare($client_query);
    $stmt->execute(['notification_message' => $notification_message, 'id_reservation' => $id_reservation]);

    // Redirige l'admin vers le tableau de bord avec un message de succès
    $_SESSION['notif'] = "✅ La réservation a été confirmée avec succès.";
    header("Location: tableau_de_bord_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur</title>
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

        .dashboard {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .dashboard h3 {
            font-size: 1.3rem;
            color: #007bff;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Tableaux responsive */
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
            padding: 10px;
            text-align: left;
            font-size: 0.9rem;
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

        /* Boutons d'action */
        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .buttons button {
            padding: 6px 10px;
            background-color: rgb(0, 106, 255);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .buttons button:hover {
            background-color: rgb(5, 92, 173);
        }

        /* Actions principales */
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }

        .actions a {
            background-color: rgb(0, 106, 255);
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .actions a:hover {
            background-color: rgb(5, 92, 173);
            transform: translateY(-2px);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            background-color: #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            font-size: 0.9rem;
        }

        .pagination a.active {
            background-color: rgb(26, 117, 255);
            color: white;
        }

        /* Formulaire de statut */
        .change-status-form select {
            width: 100%;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
        }

        /* Messages vides */
        .empty-message {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            
            .container {
                padding: 10px;
            }
            
            .dashboard {
                padding: 15px;
            }
            
            .actions {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            th, td {
                padding: 8px 6px;
                font-size: 0.8rem;
            }
            
            .buttons button {
                padding: 5px 8px;
                font-size: 0.8rem;
            }
            
            .actions a {
                font-size: 0.9rem;
                padding: 8px;
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
        <h2>Tableau de bord Admin</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?>
                <i class="fas fa-user" id="user-icon"></i>
                <div id="notification-menu" class="notification-menu">
                    <h4>Notifications</h4>
                    <ul>
                        <li>Aucune notification</li>
                    </ul>
                </div>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <!-- Section Voitures -->
        <div class="dashboard">
            <h3>Liste des Voitures</h3>

            <div class="actions">
                <a href="ajouter_voiture.php">Ajouter une voiture</a>
                <a href="rapport_voitures.php">Rapport Voitures</a>
                <a href="rapport_reservation.php">Rapport Réservations</a>
                <a href="rapport_revenus.php">Rapport Revenus</a>
            </div>

            <?php if (count($voitures) > 0) { ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Marque</th>
                                <th>Modèle</th>
                                <th>Année</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($voitures as $voiture) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($voiture['marque']); ?></td>
                                    <td><?php echo htmlspecialchars($voiture['modele']); ?></td>
                                    <td><?php echo htmlspecialchars($voiture['annee']); ?></td>
                                    <td><?php echo htmlspecialchars($voiture['statut']); ?></td>
                                    <td class="buttons">
                                        <button onclick="location='modifier_voiture.php?id=<?php echo $voiture['id_voiture']; ?>'">Modifier</button>
                                        <button onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette voiture ?') ? location='supprimer_voiture.php?id=<?php echo $voiture['id_voiture']; ?>' : null">Supprimer</button>
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
            <?php } else { ?>
                <div class="empty-message">
                    <p>Aucune voiture enregistrée</p>
                </div>
            <?php } ?>
        </div>

        <!-- Section Réservations -->
        <div class="dashboard">
            <h3>Liste des Réservations</h3>
            
            <?php if (count($reservations) > 0) { ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Voiture</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation) { ?>
                                <tr id="reservation_<?php echo $reservation['id_reservation']; ?>">
                                    <td><?php echo htmlspecialchars($reservation['prenom'] . ' ' . htmlspecialchars($reservation['nom'])); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['marque'] . ' ' . htmlspecialchars($reservation['modele'])); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['date_debut']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['date_fin']); ?></td>
                                    <td>
                                        <form class="change-status-form" data-reservation-id="<?php echo $reservation['id_reservation']; ?>">
                                            <select name="statut">
                                                <option value="En attente" <?php echo ($reservation['statut'] == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                                                <option value="Confirmée" <?php echo ($reservation['statut'] == 'Confirmée') ? 'selected' : ''; ?>>Confirmée</option>
                                                <option value="Annulée" <?php echo ($reservation['statut'] == 'Annulée') ? 'selected' : ''; ?>>Annulée</option>
                                                <option value="Terminée" <?php echo ($reservation['statut'] == 'Terminée') ? 'selected' : ''; ?>>Terminée</option>
                                            </select>
                                            <input type="hidden" name="id_reservation" value="<?php echo $reservation['id_reservation']; ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="empty-message">
                    <p>Aucune réservation trouvée</p>
                </div>
            <?php } ?>
        </div>

        <!-- Section Paiements -->
        <div class="dashboard">
            <h3>Liste des Paiements</h3>
            
            <?php if (count($paiements) > 0) { ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Réservation</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paiements as $paiement) { ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($paiement['id_reservation']); ?></td>
                                    <td><?php echo htmlspecialchars($paiement['montant']); ?> FCFA</td>
                                    <td><?php echo htmlspecialchars($paiement['date_paiement']); ?></td>
                                    <td><?php echo htmlspecialchars($paiement['mode_paiement']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="empty-message">
                    <p>Aucun paiement enregistré</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // Gestion du changement de statut
        document.querySelectorAll('.change-status-form select').forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                const formData = new FormData(form);
                const reservationId = form.dataset.reservationId;

                fetch('changer_statut_reservation.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        alert("Statut mis à jour avec succès");
                    } else {
                        alert("Erreur lors de la mise à jour");
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert("Une erreur est survenue");
                });
            });
        });

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
    </script>
</body>
</html>