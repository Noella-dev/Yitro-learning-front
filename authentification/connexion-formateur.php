<?php
// Démarrer la session
session_start();

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si le formateur est déjà connecté
if (isset($_SESSION['formateur_id'])) {
    header("Location: ../Espace/formateur/espace_formateur.php");
    exit();
}

// Connexion à la base de données
$host = "localhost";
$dbname = "yitro_learning";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    $_SESSION['error'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
    header("Location: connexion.php");
    exit();
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer et sécuriser les données
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"] ?? '');

    // Vérifications
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header("Location: connexion.php");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Adresse e-mail invalide.";
        header("Location: connexion.php");
        exit();
    } else {
        // Vérifier si l'email existe dans la table formateurs
        $stmt = $pdo->prepare("SELECT id, email, password, nom_prenom FROM formateurs WHERE email = ? AND password IS NOT NULL");
        $stmt->execute([$email]);
        $formateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($formateur && password_verify($password, $formateur['password'])) {
            // Connexion réussie : initialiser la session
            $_SESSION['formateur_id'] = $formateur['id'];
            $_SESSION['formateur_email'] = $formateur['email'];
            $_SESSION['formateur_nom_prenom'] = $formateur['nom_prenom'];
            $_SESSION['success'] = "Connexion réussie ! Bienvenue, " . $formateur['nom_prenom'] . ".";
            header("Location: ../Espace/formateur/espace_formateur.php");
            exit();
        } else {
            error_log("Échec de connexion pour l'email: " . $email);
            $_SESSION['error'] = "E-mail ou mot de passe incorrect.";
            header("Location: connexion.php");
            exit();
        }
    }
}
?>