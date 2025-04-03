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

// Récupérer les informations de la voiture
$query = "SELECT * FROM voitures WHERE id_voiture = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_voiture]);
$voiture = $stmt->fetch();

if (!$voiture) {
    echo "Voiture introuvable.";
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $statut = $_POST['statut'];

    // Mettre à jour la voiture dans la base de données
    $update_query = "UPDATE voitures SET marque = ?, modele = ?, annee = ?, statut = ? WHERE id_voiture = ?";
    $stmt = $pdo->prepare($update_query);
    $stmt->execute([$marque, $modele, $annee, $statut, $id_voiture]);

    // Rediriger vers le tableau de bord
    header("Location: tableau_de_bord_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Voiture</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            position: sticky;
            top: 0;
            width: 100%;
        }

        header h2 {
            margin: 0;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        form label {
            font-size: 1.1em;
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        form input, form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        form button {
            padding: 12px 20px;
            background-color: #006aff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #055cac;
        }

        .alert {
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 1em;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        footer {
            margin-top: 30px;
            padding: 20px;
            background-color: #343a40;
            text-align: center;
            color: white;
        }

        footer a {
            color: #fff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h2>Modifier la Voiture</h2>
    </header>

    <div class="container">
        <?php
        // Affichage des messages d'erreur ou de succès
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }

        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="POST">
            <label for="marque">Marque</label>
            <input type="text" id="marque" name="marque" value="<?php echo htmlspecialchars($voiture['marque']); ?>" required>

            <label for="modele">Modèle</label>
            <input type="text" id="modele" name="modele" value="<?php echo htmlspecialchars($voiture['modele']); ?>" required>

            <label for="annee">Année</label>
            <input type="text" id="annee" name="annee" value="<?php echo htmlspecialchars($voiture['annee']); ?>" required>

            <label for="statut">Statut</label>
            <select id="statut" name="statut" required>
                <option value="Disponible" <?php echo $voiture['statut'] == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                <option value="Réservée" <?php echo $voiture['statut'] == 'Réservée' ? 'selected' : ''; ?>>Réservée</option>
                <option value="Indisponible" <?php echo $voiture['statut'] == 'Indisponible' ? 'selected' : ''; ?>>Indisponible</option>
            </select>

            <button type="submit">Sauvegarder les modifications</button>
        </form>
    </div>

    <footer>
    <p>&copy; 2025 Location de Voitures | Tous droits réservés</p>
    </footer>
</body>
</html>
