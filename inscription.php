<?php
include('db.php');

$erreur = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $numero_permis = $_POST['numero_permis'];

    $sql = "SELECT * FROM clients WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $erreur = "Cet email est déjà utilisé.";
    } else {
        $sql = "INSERT INTO clients (nom, prenom, email, mot_de_passe_hash, date_naissance, adresse, numero_permis) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :date_naissance, :adresse, :numero_permis)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', password_hash($mot_de_passe, PASSWORD_BCRYPT)); 
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':numero_permis', $numero_permis);

        if ($stmt->execute()) {
            header("Location: login.php"); 
            exit();
        } else {
            $erreur = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            padding-top: 60px; 
            padding-bottom: 60px; 
        }

        header, footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .register-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-top: 80px;
        }

        .register-container h2 {
            font-size: 2em;
            color: #007bff;
            margin-bottom: 20px;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .register-container label {
            text-align: left;
            font-weight: bold;
        }

        .register-container input {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .register-container button {
            margin-top: 15px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .register-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .login-link {
            margin-top: 10px;
            font-size: 0.9em;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .register-container {
                width: 90%;
            }
        }

    </style>
</head>
<body>
    <header>
        <h1>Inscrivez-vous et Louez Votre Voiture en Toute Simplicité</h1>
    </header>

    <div class="register-container">
        <h2>Inscription</h2>

        <?php if (isset($erreur) && $erreur != '') : ?>
            <p class="error-message"><?php echo $erreur; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance" required>

            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" required>

            <label for="numero_permis">Numéro de permis</label>
            <input type="text" id="numero_permis" name="numero_permis" required>

            <button type="submit">S'inscrire</button>
        </form>

        <p class="login-link">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </div>

    <footer>
        <p>&copy; 2025 Location de Voitures | Tous droits réservés</p>
    </footer>
</body>
</html>
