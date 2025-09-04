<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../Backend/config.php'; // Fichier de connexion à la BDD (PDO)

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $modules = $_POST['modules'];
    $price = $_POST['price'];

    // Préparation des fichiers à uploader
    $uploadedFiles = [];

    if (!empty($_FILES['files']['name'][0])) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['files']['name'] as $key => $filename) {
            $tmpName = $_FILES['files']['tmp_name'][$key];
            $uniqueName = uniqid() . '_' . basename($filename);
            $destination = $uploadDir . $uniqueName;

            if (move_uploaded_file($tmpName, $destination)) {
                $uploadedFiles[] = $destination;
            } else {
                $erreur .= "Erreur lors de l'upload du fichier : $filename<br>";
            }
        }
    }

    if (empty($erreur)) {
        $files_str = implode(',', $uploadedFiles);

        $sql = "INSERT INTO courses (title, description, category, modules, files, price)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$title, $description, $category, $modules, $files_str, $price]);
            echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cours créé</title>
    <link rel="icon" href="../asset/images/Yitro_consulting.png" type="image/png">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: "Segoe UI", sans-serif;
        }

        .success-popup {
            background-color: #e6ffed;
            color: #0f5132;
            padding: 20px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            font-size: 20px;
            font-weight: 600;
            animation: popup 0.3s ease-out;
            text-align: center;
        }

        @keyframes popup {
            from {
                transform: scale(0.7);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="success-popup">✅ Cours créé avec succès ! </br> Atendez la confirmation de l\'admin pour la publication de cette cour </div>

    <script>
        setTimeout(function() {
            window.location.href = "espace-formateur.php";
        }, 4000);
    </script>
</body>
</html>
';
        } catch (PDOException $e) {
            echo "❌ Erreur de base de données : " . $e->getMessage();
        }
    } else {
        echo "⚠️ Erreurs détectées : <br>" . $erreur;
    }
} else {
    echo "⚠️ Vérifiez votre formulaire.";
}
?>
