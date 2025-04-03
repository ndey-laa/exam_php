<?php
// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=location_db", "root", "");

// Récupérer tous les asministrateurs avec un mot de passe non haché
$query = $pdo->query("SELECT id_client, mot_de_passe FROM clients WHERE mot_de_passe_hash IS NULL");

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $hashedPassword = password_hash($row['mot_de_passe'], PASSWORD_DEFAULT);
    
    // Mettre à jour la base de données avec le mot de passe haché
    $update = $pdo->prepare("UPDATE clients SET mot_de_passe_hash = ? WHERE id_client = ?");
    $update->execute([$hashedPassword, $row['id_client']]);
}

echo "Migration terminée : mots de passe hachés avec succès.";
?>
