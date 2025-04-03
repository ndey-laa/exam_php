<?php
session_start();
include('db.php');

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}


$requete_admin = "SELECT prenom, nom FROM administrateurs WHERE id_admin = :id_admin";
$stmt_admin = $pdo->prepare($requete_admin);
$stmt_admin->execute(['id_admin' => $_SESSION['id_admin']]);
$admin = $stmt_admin->fetch();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_voiture = trim($_POST['id_voiture']); // Utilisation de trim pour supprimer les espaces
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $statut = $_POST['statut'];
    $plaque = $_POST['plaque'];
    $image = $_FILES['image']['name'];
    $tarif_journalier = $_POST['tarif_journalier'];



    // Vérification que tous les champs nécessaires sont remplis
    if (empty($id_voiture) || empty($marque) || empty($modele) || empty($annee) || empty($statut) || empty($tarif_journalier) || empty($plaque)) {
        $error_message = "Tous les champs doivent être remplis.";
    } else {
        // Vérification de l'existence de l'ID dans la base de données
        $query_check = "SELECT id_voiture FROM voitures WHERE id_voiture = ?";
        $stmt_check = $pdo->prepare($query_check);
        $stmt_check->execute([$id_voiture]);

        // Débogage : afficher la requête
        // echo "Requête exécutée pour ID : " . $id_voiture . "<br>"; // Décommenter pour vérifier les requêtes SQL

        // Si l'ID existe déjà dans la base de données
        if ($stmt_check->rowCount() > 0) {
            $error_message = "L'ID de la voiture existe déjà. Veuillez saisir un autre ID.";
        } else {
            // Traitement de l'image
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image_tmp_name = $_FILES['image']['tmp_name'];
                $image_name = $_FILES['image']['name'];
                $image_size = $_FILES['image']['size'];
                $image_type = $_FILES['image']['type'];

                // Vérification du type d'image (par exemple, jpeg, png)
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                if (in_array($image_type, $allowed_types)) {
                    // Définir le chemin où l'image sera stockée
                    $upload_dir = ''; // Assurez-vous que le dossier "images" existe
                    $image_path = $upload_dir . basename($image_name);

                    // Déplacer le fichier dans le répertoire
                    if (!move_uploaded_file($image_tmp_name, $image_path)) {
                        $error_message = "Erreur lors du téléchargement de l'image.";
                    }
                } else {
                    $error_message = "Seules les images JPEG ou PNG sont autorisées.";
                }
            }

            // Si aucune erreur n'est survenue, on insère dans la base de données
            if (!isset($error_message)) {
                $query = "INSERT INTO voitures (id_voiture, marque, modele, annee, statut, `plaque_immatriculation`, tarif_journalier, image) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$id_voiture, $marque, $modele, $annee, $statut, $plaque, $tarif_journalier, $image_path]);

                $success_message = "Voiture ajoutée avec succès!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Voiture</title>
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
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-container h3 {
            font-size: 1.3rem;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Groupes de formulaire */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .form-group input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .form-group input[type="submit"]:hover {
            background-color: #218838;
        }

        /* Messages */
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 0.95rem;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Actions */
        .actions {
            text-align: center;
            margin-top: 20px;
        }

        .actions a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .actions a:hover {
            background-color: #0056b3;
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
            
            .container {
                padding: 10px;
            }
            
            .form-container {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .form-group input,
            .form-group select {
                padding: 8px;
                font-size: 0.9rem;
            }
            
            .form-group input[type="submit"] {
                padding: 10px;
                font-size: 0.95rem;
            }
            
            .actions a {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h2>Ajouter une Voiture</h2>
        <div class="header-right">
            <div class="user-info">
                <?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?>
                <i class="fas fa-user"></i>
            </div>
            <a href="login.php" class="btn-deconnexion">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h3>Formulaire d'ajout</h3>

            <?php if (isset($error_message)) { ?>
                <div class="message error-message"><?php echo $error_message; ?></div>
            <?php } ?>
            <?php if (isset($success_message)) { ?>
                <div class="message success-message"><?php echo $success_message; ?></div>
            <?php } ?>

            <form method="POST" action="ajouter_voiture.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="id_voiture">ID de la Voiture</label>
                    <input type="text" name="id_voiture" id="id_voiture" required>
                </div>

                <div class="form-group">
                    <label for="marque">Marque</label>
                    <input type="text" name="marque" id="marque" required>
                </div>

                <div class="form-group">
                    <label for="modele">Modèle</label>
                    <input type="text" name="modele" id="modele" required>
                </div>

                <div class="form-group">
                    <label for="annee">Année</label>
                    <input type="number" name="annee" id="annee" required min="1900" max="2099">
                </div>

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <select name="statut" id="statut" required>
                        <option value="">Sélectionner...</option>
                        <option value="Disponible">Disponible</option>
                        <option value="Indisponible">Indisponible</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="plaque">Plaque d'immatriculation</label>
                    <input type="text" name="plaque" id="plaque" required>
                </div>

                <div class="form-group">
                    <label for="image">Image de la voiture</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="tarif_journalier">Tarif Journalier (FCFA)</label>
                    <input type="number" name="tarif_journalier" id="tarif_journalier" required min="0" step="100">
                </div>

                <div class="form-group">
                    <input type="submit" value="Ajouter la voiture">
                </div>
            </form>

            <div class="actions">
                <a href="tableau_de_bord_admin.php">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</body>
</html>
