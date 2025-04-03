<?php
session_start();
include('db.php');

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['statut']) && isset($_POST['id_reservation'])) {
    $statut = $_POST['statut'];
    $id_reservation = $_POST['id_reservation'];

    // Mettre à jour le statut de la réservation
    $requete = "UPDATE reservations SET statut = :statut WHERE id_reservation = :id_reservation";
    $stmt = $pdo->prepare($requete);
    $stmt->execute(['statut' => $statut, 'id_reservation' => $id_reservation]);

    // Récupérer l'ID du client pour la notification
    $requete_client = "SELECT id_client FROM reservations WHERE id_reservation = :id_reservation";
    $stmt_client = $pdo->prepare($requete_client);
    $stmt_client->execute(['id_reservation' => $id_reservation]);
    $reservation = $stmt_client->fetch();

    if ($reservation) {
        $id_client = $reservation['id_client'];
        
        // Créer un message de notification selon le statut
        switch($statut) {
            case 'Confirmée':
                $message = "✅ Votre réservation #$id_reservation a été confirmée !";
                break;
            case 'Annulée':
                $message = "❌ Votre réservation #$id_reservation a été annulée.";
                break;
            case 'Terminée':
                $message = "🏁 Votre réservation #$id_reservation est terminée. Merci !";
                break;
            default:
                $message = "ℹ️ Le statut de votre réservation #$id_reservation a été mis à jour : $statut";
        }

        // Insérer la notification
        $requete_notif = "INSERT INTO notifications (id_client, message, date_creation, lu) 
                          VALUES (:id_client, :message, NOW(), 0)";
        $stmt_notif = $pdo->prepare($requete_notif);
        $stmt_notif->execute([
            'id_client' => $id_client,
            'message' => $message
        ]);
    }

    $_SESSION['notif'] = "Le statut a été mis à jour et le client a été notifié.";
    header("Location: tableau_de_bord_admin.php");
    exit;
} else {
    $_SESSION['error'] = "Paramètres manquants.";
    header("Location: tableau_de_bord_admin.php");
    exit;
}
?>