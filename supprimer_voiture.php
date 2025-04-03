<?php
session_start();
include('db.php');

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Vérifie si un ID de voiture est passé en paramètre
if (!isset($_GET['id'])) {
    echo "ID de la voiture manquant.";
    exit;
}

$id_voiture = $_GET['id'];

// Vérifier si la voiture existe avant de la supprimer
$query = "SELECT * FROM voitures WHERE id_voiture = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_voiture]);
$voiture = $stmt->fetch();

if (!$voiture) {
    echo "Voiture introuvable.";
    exit;
}

// Suppression de la voiture
$delete_query = "DELETE FROM voitures WHERE id_voiture = ?";
$stmt = $pdo->prepare($delete_query);
$stmt->execute([$id_voiture]);

// Rediriger vers le tableau de bord avec un message de succès
$_SESSION['message'] = "Voiture supprimée avec succès.";
header("Location: tableau_de_bord_admin.php");
exit;
?>
