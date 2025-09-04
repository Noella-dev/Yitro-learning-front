<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription réussie</title>
    <link rel="stylesheet" href="../asset/css/styles.css">
    <style>
        #success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 20px;
            margin-top: 30px;
            border: 2px solid #c3e6cb;
            border-radius: 10px;
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div id="success-message">
            <h5>Bienvenue sur Yitro Learning !</h5>
            <p>Votre compte est activé. Vous pouvez maintenant explorer les cours, suivre vos premiers modules, et rejoindre notre communauté.</p>
            <a href="../index.php" class="btn btn-success mt-2">Revenir à l'accueil</a>
            <a href="../authentification/connexion.php" class="btn btn-outline-success mt-2 ms-2">Se connecter</a>
        </div>
    </div>
</body>
</html>