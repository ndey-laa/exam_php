<?php

include('db.php');

session_start(); 

if (isset($_POST['connexion'])) {

    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérification pour les clients
    $queryClient = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
    $queryClient->execute(['email' => $email]);
    $client = $queryClient->fetch();

    // Vérification pour les administrateurs
    $queryAdmin = $pdo->prepare("SELECT * FROM administrateurs WHERE email = :email");
    $queryAdmin->execute(['email' => $email]);
    $admin = $queryAdmin->fetch();

    // Vérification du mot de passe pour le client
    if ($client && password_verify($mot_de_passe, $client['mot_de_passe_hash'])) {
        $_SESSION['id_client'] = $client['id_client'];
        $_SESSION['prenom'] = $client['prenom'];
        $_SESSION['nom'] = $client['nom'];
        header("Location: tableau_de_bord_client.php");
        exit();
    }

    // Vérification du mot de passe pour l'administrateur
    elseif ($admin && password_verify($mot_de_passe, $admin['mot_de_passe_hash'])) {
        $_SESSION['id_admin'] = $admin['id_admin'];
        $_SESSION['nom_utilisateur'] = $admin['nom_utilisateur'];
        header("Location: tableau_de_bord_admin.php");
        exit();
    } else {
        // Message d'erreur si les identifiants sont incorrects
        $erreur = "Identifiants incorrects.";
    }

    if ($client) {
        var_dump($client);  // Ajoutez cette ligne pour vérifier le contenu de $client
        if (password_verify($mot_de_passe, $client['mot_de_passe_hash'])) {
            $_SESSION['id_client'] = $client['id_client'];
            $_SESSION['prenom'] = $client['prenom'];
            $_SESSION['nom'] = $client['nom'];
            header("Location: tableau_de_bord_client.php");
            exit();
        }
    } else {
        $erreur = "Client non trouvé.";
    }
    
    if ($admin) {
        var_dump($admin);  // Ajoutez cette ligne pour vérifier le contenu de $admin
        if (password_verify($mot_de_passe, $admin['mot_de_passe_hash'])) {
            $_SESSION['id_admin'] = $admin['id_admin'];
            $_SESSION['nom_utilisateur'] = $admin['nom_utilisateur'];
            header("Location: tableau_de_bord_admin.php");
            exit();
        }
    } else {
        $erreur = "Administrateur non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        header {
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        header h1 {
            font-size: 2em;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-top: 100px;
            margin-bottom: 50px;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            font-size: 2em;
            color: #007bff;
            margin-bottom: 20px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .login-container label {
            text-align: left;
            font-weight: bold;
        }

        .login-container input {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
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

        .login-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .register-link {
            margin-top: 10px;
            font-size: 0.9em;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        @media (max-width: 500px) {
            .login-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Réservez Votre Voiture de Location en Toute Simplicité</h1>
    </header>
    
    <div class="container">
        <div class="login-container">
            <h2>Connexion</h2>

            <?php if (isset($erreur)) : ?>
                <p class="error-message"><?php echo $erreur; ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Email :</label>
                <input type="email" name="email" required>

                <label>Mot de passe :</label>
                <input type="password" name="mot_de_passe" required>

                <button type="submit" name="connexion">Se connecter</button>
            </form>

            <p class="register-link">Pas encore inscrit ? <a href="inscription.php">Créer un compte</a></p>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Location de Voitures | Tous droits réservés</p>
    </footer>
</body>
</html>
