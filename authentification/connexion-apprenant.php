<?php
session_start();

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yitro_learning";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    error_log("Connexion échouée: " . $conn->connect_error);
    $_SESSION['error'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
    header("Location: connexion.php");
    exit();
}

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des entrées
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    // Validation des champs
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header("Location: connexion.php");
        exit();
    }

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Adresse e-mail invalide.";
        header("Location: connexion.php");
        exit();
    }

    // Vérification des tentatives de connexion
    $max_attempts = 5;
    $lockout_time = 15 * 60; // 15 minutes en secondes
    if (isset($_SESSION['login_attempts'][$email]) && $_SESSION['login_attempts'][$email]['count'] >= $max_attempts) {
        if (time() - $_SESSION['login_attempts'][$email]['time'] < $lockout_time) {
            $_SESSION['error'] = "Trop de tentatives de connexion. Veuillez réessayer dans " 
            . ceil(($lockout_time - (time() - $_SESSION['login_attempts'][$email]['time'])) / 60) . " minutes.";
            header("Location: connexion.php");
            exit();
        } else {
            // Réinitialiser les tentatives après le temps de verrouillage
            unset($_SESSION['login_attempts'][$email]);
        }
    }

    // Requête pour vérifier l'utilisateur avec le rôle 'apprenant' et compte actif
    $sql = "SELECT id, nom, email, mot_de_passe, role, actif FROM utilisateurs WHERE email = ? AND role = 'apprenant'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Erreur de préparation de la requête: " . $conn->error);
        $_SESSION['error'] = "Erreur serveur. Veuillez réessayer plus tard.";
        header("Location: connexion.php");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Vérification des identifiants
    if ($user) {
        if (!$user['actif']) {
            error_log("Compte inactif pour l'email: " . $email);
            $_SESSION['error'] = "Votre compte est désactivé. Veuillez contacter l'administrateur.";
            header("Location: connexion.php");
            exit();
        }

        if (password_verify($password, $user['mot_de_passe'])) {
            // Réinitialiser les tentatives de connexion en cas de succès
            unset($_SESSION['login_attempts'][$email]);

            // Création de la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = 'apprenant';
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['success'] = "Connexion réussie ! Bienvenue, " . $user['nom'] . ".";
            error_log("Connexion réussie pour l'utilisateur: " . $email);
            header("Location: ../Espace/apprenant/espace_apprenant.php");
            exit();
        } else {
            // Enregistrer la tentative échouée
            if (!isset($_SESSION['login_attempts'][$email])) {
                $_SESSION['login_attempts'][$email] = ['count' => 0, 'time' => time()];
            }
            $_SESSION['login_attempts'][$email]['count']++;
            $_SESSION['login_attempts'][$email]['time'] = time();

            error_log("Mot de passe incorrect pour l'email: " . $email);
            $_SESSION['error'] = "Mot de passe incorrect. Tentatives restantes : "
             . ($max_attempts - $_SESSION['login_attempts'][$email]['count']) . ".";
            header("Location: connexion.php");
            exit();
        }
    } else {
        // Enregistrer la tentative échouée (email non trouvé ou rôle incorrect)
        if (!isset($_SESSION['login_attempts'][$email])) {
            $_SESSION['login_attempts'][$email] = ['count' => 0, 'time' => time()];
        }
        $_SESSION['login_attempts'][$email]['count']++;
        $_SESSION['login_attempts'][$email]['time'] = time();

        error_log("Échec de connexion pour l'email: " . $email);
        $_SESSION['error'] = "E-mail non trouvé ou compte non autorisé. Tentatives restantes : " . ($max_attempts - $_SESSION['login_attempts'][$email]['count']) . ".";
        header("Location: connexion.php");
        exit();
    }

    $stmt->close();
}

// Fermeture de la connexion
$conn->close();
?>