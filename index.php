<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoLoc - Location de Voitures</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #007bff; /* Bleu original */
            --secondary: #343a40; /* Noir original */
            --light: #f8f9fa; /* Gris clair original */
            --dark: #333; /* Texte foncé original */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Header */
        header {
            background-color: var(--secondary);
            color: white;
            text-align: center;
            padding: 80px 20px;
            position: relative;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: inline-block;
        }

        .logo span {
            color: var(--primary);
        }

        header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        header p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Navigation */
        nav {
            background-color: var(--primary);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 25px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: opacity 0.3s;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .cta-button {
            display: inline-block;
            background-color: white;
            color: var(--primary);
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin: 0 10px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cta-button.secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .cta-button.secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .button-group {
            margin-top: 20px;
        }

        /* Main Content */
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--primary);
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 3px;
            background: var(--primary);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Image Gallery */
        .image-gallery {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 40px 0;
            flex-wrap: wrap;
        }

        .image-gallery img {
            width: 100%;
            max-width: 350px;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .image-gallery img:hover {
            transform: scale(1.03);
        }

        /* How It Works */
        .how-it-works {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .step {
            background-color: white;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .step:hover {
            transform: translateY(-10px);
        }

        .step-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .step h3 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        /* Footer */
        footer {
            background-color: var(--secondary);
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-top: 60px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            list-style: none;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .footer-links li {
            margin: 0 15px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .social-icons {
            margin: 20px 0;
        }

        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 60px 20px;
            }

            header h1 {
                font-size: 2.2rem;
            }

            .nav-container {
                flex-direction: column;
            }

            .nav-links {
                margin-top: 20px;
            }

            .nav-links li {
                margin: 0 10px;
            }

            .how-it-works {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.8rem;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .image-gallery img {
                height: 200px;
            }

            .button-group {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .cta-button {
                margin: 5px 0;
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">Auto<span>Loc</span></div>
        <h1>Réservez Votre Voiture de Location en Toute Simplicité</h1>
        <p>Choisissez parmi une large gamme de voitures modernes et bien entretenues pour répondre à vos besoins de mobilité</p>
        <div class="button-group">
            <a href="inscription.php" class="cta-button">S'inscrire</a>
            <a href="login.php" class="cta-button secondary">Se connecter</a>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <ul class="nav-links">
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Intro Section -->
        <section>
            <div class="section-title">
                <h2>Explorez Nos Véhicules</h2>
            </div>
            <p style="text-align: center; max-width: 800px; margin: 0 auto 30px;">
                Découvrez notre sélection de véhicules soigneusement entretenus pour tous vos déplacements, que ce soit pour un court trajet ou un long voyage.
            </p>
            
            <!-- Image Gallery (conservant vos images originales) -->
            <div class="image-gallery">
                <img src="./images/8585294-rendu-3d-sport-voiture-bleue-sur-blanc-bakcground-jpg-gratuit-photo.jpg" alt="Véhicule Sportif">
                <img src="./images/fila-autos-honda-fila_786255-11724.avif" alt="Véhicule Familial">
                <img src="./images/456x342.webp" alt="Véhicule Économique">
            </div>
        </section>

        <!-- How It Works -->
        <section>
            <div class="section-title">
                <h2>Comment ça marche</h2>
            </div>
            
            <div class="how-it-works">
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Créez un compte</h3>
                    <p>Inscrivez-vous simplement et accédez à nos offres exclusives en quelques minutes.</p>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Choisissez votre voiture</h3>
                    <p>Sélectionnez parmi notre variété de modèles adaptés à vos besoins et budget.</p>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Réservez et payez</h3>
                    <p>Effectuez votre réservation en ligne et payez de manière sécurisée.</p>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-road"></i>
                    </div>
                    <h3>Profitez de votre voyage</h3>
                    <p>Récupérez votre voiture et partez à l'aventure en toute tranquillité.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="logo">Auto<span>Loc</span></div>
        
        <p>&copy; 2025 AutoLoc - Tous droits réservés</p>
    </footer>
</body>
</html>