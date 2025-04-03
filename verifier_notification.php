<?php
session_start();
include('db.php');

if (!isset($_GET['id_client'])) {
    die(json_encode(['nouvelles' => false]));
}

$id_client = $_GET['id_client'];

// Vérifier les nouvelles notifications non lues
$requete = "SELECT COUNT(*) as count FROM notifications 
            WHERE id_client = :id_client AND lu = 0";
$stmt = $pdo->prepare($requete);
$stmt->execute(['id_client' => $id_client]);
$result = $stmt->fetch();

echo json_encode(['nouvelles' => $result['count'] > 0]);
?>