<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../Backend/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification des informations d'identification
    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user){
        if (password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirection en fonction du rôle
            if ($user['role'] === 'admin'){
                header("Location: ../page/backoffice.php");
                exit();
            } elseif ($user['role'] === 'apprenant') {
                header("Location: ../page/espace-apprenant.php");
                exit();
            }elseif ($user['role'] === 'formateur') {
                header("Location: ../page/espace-formateur.php");
                exit();
            } else {
                $erreur = "Rôle non reconnu.";
            }
            
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Aucun utilisateur trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning | Connexion & Inscription</title>
    <!-- Icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Police Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Feuille de style principale -->
    <link rel="stylesheet" href="../asset/css/styles.css">
    <!-- Feuille de style spécifique pour la page connexion/inscription -->
    <link rel="stylesheet" href="../asset/css/login-register.css">
    <link rel="icon" href="../asset/images/Yitro_consulting.png" type="image/png">
</head>
<body>


    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                <a href="../index.php" class="logo">Yitro Learning</a>
            </div>
            <div class="menu-toggle" id="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-list">
                <li><a href="../index.php">Accueil</a></li>
                <li class="dropdown">
                    <a href="../page/devenir-formateur.php">Devenir formateur</a>
                </li>
                <li><a href="../page/FAQ.php">FAQ</a></li>
                <li><a href="#" class="btn-login">Se connecter</a></li>
                <li><a href="register.php" class="btn-primary">S'inscrire</a></li>
            </ul>
        </div>
    </nav>

    <!-- Section Connexion -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <!-- Onglets -->
                <div class="auth-tabs">
                    <button class="tab active" data-tab="login">Connexion</button>
                </div>
                <?php if (!empty($erreur)): ?>
    <div style="color: white; background-color: red; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">
        <?php echo htmlspecialchars($erreur); ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div style="color: white; background-color: green; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

                <!-- Formulaire de Connexion -->
                <div class="auth-form active" id="login">
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <input type="email" id="login-email" name="email" required placeholder="Entrez votre email">
                        </div>
                        <div class="form-group">
                            <label for="login-password">Mot de passe</label>
                            <input type="password" id="login-password" name="password" required placeholder="Entrez votre mot de passe">
                        </div>
                        <div class="form-options">
                            <label>
                                <input type="checkbox" name="remember"> Se souvenir de moi
                            </label>
                            <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                        </div>
                        <button type="submit" class="btn-primary">Se connecter</button>
                    </form>
                </div>

                
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">Formations</a></li>
                        <li><a href="#">Certifications</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">CGU</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Contact</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> contact@yitro-consulting.com</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-map-marker-alt"></i> Paris, France</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 Yitro Consulting. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="../asset/js/script.js"></script>
    <script src="../asset/js/login-register.js"></script>
</body>
</html>