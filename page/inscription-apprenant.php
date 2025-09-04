<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yitro_learning";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des champs obligatoires
    if (empty($_POST['nom']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['pays']) || empty($_POST['langue']) || empty($_POST['type_cours']) || empty($_POST['niveau_formation'])) {
        echo "<script>alert('Erreur : Tous les champs obligatoires doivent être remplis.');</script>";
        exit;
    }

    // Nettoyage des entrées
    $nom = trim($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mot_de_passe = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telephone = trim($_POST['telephone']);
    $pays = trim($_POST['pays']);
    $langue = trim($_POST['langue']);
    $autre_langue = trim($_POST['autre_langue']);
    $type_cours = trim($_POST['type_cours']);
    $niveau_formation = trim($_POST['niveau_formation']);
    $niveau_etude = trim($_POST['niveau_etude']);
    $acces_internet = trim($_POST['acces_internet']);
    $appareil = isset($_POST['appareil']) ? trim($_POST['appareil']) : '';
    $accessibilite = trim($_POST['accessibilite']);
    $rgpd = isset($_POST['rgpd']) ? 1 : 0;
    $charte = isset($_POST['charte']) ? 1 : 0;

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Erreur : Adresse e-mail invalide.');</script>";
        exit;
    }

    // Vérifier si l'email existe déjà
    $sql_check_email = "SELECT email FROM utilisateurs WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        echo "<script>alert('Erreur : Cet e-mail est déjà utilisé.');</script>";
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Gestion des objectifs
    $objectifs = isset($_POST['objectifs']) ? implode(", ", $_POST['objectifs']) : '';

    // Gestion de l'upload de la photo
    $photo_path = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "Uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $maxFileSize = 5 * 1024 * 1024; // 5 Mo
        if ($_FILES['photo']['size'] > $maxFileSize) {
            echo "<script>alert('Erreur : La taille du fichier dépasse la limite de 5 Mo.');</script>";
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
            echo "<script>alert('Erreur : Seuls les formats JPEG, PNG et GIF sont autorisés.');</script>";
            exit;
        }

        $photo_name = basename($_FILES["photo"]["name"]);
        $photo_path = $target_dir . time() . "_" . $photo_name;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path)) {
            echo "<script>alert('Erreur : Échec du téléchargement de la photo.');</script>";
            exit;
        }
    }

    // Requête SQL d'insertion
    $sql = "INSERT INTO utilisateurs (
                nom, email, mot_de_passe, telephone, photo, pays, langue, autre_langue,
                objectifs, type_cours, niveau_formation, niveau_etude, acces_internet,
                appareil, accessibilite, rgpd, charte
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssssi",
        $nom, $email, $mot_de_passe, $telephone, $photo_path, $pays, $langue,
        $autre_langue, $objectifs, $type_cours, $niveau_formation, $niveau_etude,
        $acces_internet, $appareil, $accessibilite, $rgpd, $charte
    );

    if ($stmt->execute()) {
        echo "<script>window.location.href='merci.php';</script>";
    } else {
        error_log("Erreur SQL : " . $stmt->error);
        echo "<script>alert('Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="icon" href="../asset/images/Yitro_consulting.png" type="image/png">
    <link rel="stylesheet" href="../asset/css/inscription-apprenant.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="main-nav">
            <div class="container">
                <div class="logo">
                    <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                    <a href="../index.php" class="logo-text">Yitro Learning</a>
                </div>
                <ul class="nav-list">
                    <li class="dropdown">
                        <a href="#">À propos <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="decouvrir-yitro.php">Découvrir Yitro</a></li>
                            <li><a href="FAQ.php">FAQ</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="catalogue.php">Nos formations <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown">
                                <a href="#">Compétences Bureautiques & Outil <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Excel et analyse de données</a></li>
                                    <li><a href="#">Bureautique générale</a></li>
                                    <li><a href="#">Outils de collaboration et gestion</a></li>
                                    <li><a href="#">Compétences numériques fondamentales</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Marketing Digital <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">SEO / Référencement</a></li>
                                    <li><a href="#">Marketing des réseaux sociaux</a></li>
                                    <li><a href="#">Publicité en ligne</a></li>
                                    <li><a href="#">Content marketing & copywriting</a></li>
                                    <li><a href="#">Marketing d’affiliation ou influence</a></li>
                                    <li><a href="#">Création de contenu et personal branding</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Finance & Investissement <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Finance personnelle</a></li>
                                    <li><a href="#">Bourse et trading</a></li>
                                    <li><a href="#">Analyse financière et modélisation</a></li>
                                    <li><a href="#">Cryptomonnaies & blockchain</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Santé & Bien-être <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Formations Fitness</a></li>
                                    <li><a href="#">Yoga et Méditation</a></li>
                                    <li><a href="#">Nutrition & santé</a></li>
                                    <li><a href="#">Bien-être mental et vie saine</a></li>
                                    <li><a href="#">Éducation bienveillante (parents)</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Développement Personnel (Soft Skills, productivité) <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Productivité et gestion du temps</a></li>
                                    <li><a href="#">Leadership et communication</a></li>
                                    <li><a href="#">Gestion du stress et confiance en soi</a></li>
                                    <li><a href="#">Compétences créatives de réflexion</a></li>
                                    <li><a href="#">Relationnel et développement de carrière</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Arts, Design & Artisanat <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Arts visuels</a></li>
                                    <li><a href="#">Design & graphisme</a></li>
                                    <li><a href="#">Photographie et vidéo</a></li>
                                    <li><a href="#">Musique</a></li>
                                    <li><a href="#">Artisanat & DIY (Do It Yourself)</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Business & Entrepreneuriat</a></li>
                            <li><a href="formations/categories/tech-programmation.php">Technologie & Programmation</a></li>
                            <li><a href="#">Langues étrangères</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="auth-links">
                    <ul class="nav-list">
                        <li><a href="../page/connect-Apprenant.php" class="btn-primary">Connexion</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Section Hero -->
    <section class="heros">
        <div class="heros-content">
            <h1>Rejoignez Yitro Learning et commencez à apprendre autrement : à votre rythme, sur mobile, en toute liberté.</h1>
            <p>Apprenez, progressez, obtenez des badges et des certificats.</p>
            <a href="#registrationForm" class="cta-buttons">Inscrivez-vous maintenant</a>
        </div>
        <canvas id="heros-animation"></canvas>
    </section>

    <div class="container my-5">
        <form id="registrationForm" action="inscription-apprenant.php" method="POST" enctype="multipart/form-data">
            <!-- Informations de base -->
            <div class="form-section">
                <h4>1. Informations de base</h4>
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom et prénom *</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe *</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Numéro de téléphone (WhatsApp)</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone">
                </div>
            </div>

            <!-- Localisation & langue -->
            <div class="form-section">
                <h4>2. Localisation & langue</h4>
                <div class="mb-3">
                    <label for="pays" class="form-label">Pays de résidence *</label>
                    <select id="pays" class="form-select" name="pays" required>
                        <option value="">-- Sélectionner un pays --</option>
                        <option>Madagascar</option>
                        <option>France</option>
                        <option>Cameroun</option>
                        <option>Canada</option>
                        <option>Autre</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="langue" class="form-label">Langue préférée *</label>
                    <select id="langue" class="form-select" name="langue" required>
                        <option value="">-- Sélectionner --</option>
                        <option>Français</option>
                        <option>Anglais</option>
                        <option>Autre</option>
                    </select>
                    <input type="text" class="form-control mt-2" id="autreLangue" name="autre_langue" placeholder="Précisez si autre...">
                </div>
            </div>

            <!-- Objectif d'apprentissage -->
            <div class="form-section">
                <h4>3. Objectif d'apprentissage</h4>
                <label class="form-label">Pourquoi vous inscrivez-vous ? *</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="emploi" name="objectifs[]" value="Trouver un emploi ou me reconvertir">
                    <label class="form-check-label" for="emploi">Trouver un emploi ou me reconvertir</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="competencesPro" name="objectifs[]" value="Améliorer mes compétences pro">
                    <label class="form-check-label" for="competencesPro">Améliorer mes compétences pro</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="entreprise" name="objectifs[]" value="Créer mon activité / entreprise">
                    <label class="form-check-label" for="entreprise">Créer mon activité / entreprise</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="apprendre" name="objectifs[]" value="Apprendre pour moi-même">
                    <label class="form-check-label" for="apprendre">Apprendre pour moi-même</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="softSkills" name="objectifs[]" value="Développer mes soft skills">
                    <label class="form-check-label" for="softSkills">Développer mes soft skills</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="stress" name="objectifs[]" value="Mieux gérer mon temps / stress / vie perso">
                    <label class="form-check-label" for="stress">Mieux gérer mon temps / stress / vie perso</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="transition" name="objectifs[]" value="Agir pour l’inclusion / la transition">
                    <label class="form-check-label" for="transition">Agir pour l’inclusion / la transition</label>
                </div>
                <label class="form-label">Quels types de cours vous intéressent ? *</label>
                <input type="text" class="form-control" placeholder="Ex : Programmation, Marketing Digital, etc." name="type_cours" required>
            </div>

            <!-- Profil & niveau -->
            <div class="form-section">
                <h4>4. Profil & niveau</h4>
                <div class="mb-3">
                    <label for="niveauFormation" class="form-label">Niveau global de familiarité avec la formation en ligne *</label>
                    <select id="niveauFormation" class="form-select" name="niveau_formation" required>
                        <option value="">-- Sélectionner --</option>
                        <option>Je débute totalement</option>
                        <option>J’ai déjà suivi quelques cours</option>
                        <option>Je suis très à l’aise</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="etudes" class="form-label">Niveau d’étude ou de formation</label>
                    <select id="etudes" class="form-select" name="niveau_etude">
                        <option value="">-- Sélectionner --</option>
                        <option>Aucune formation formelle</option>
                        <option>Collège / lycée</option>
                        <option>Université / grandes écoles</option>
                        <option>Formation professionnelle</option>
                        <option>Autre</option>
                    </select>
                </div>
            </div>

            <!-- Accessibilité -->
            <div class="form-section">
                <h4>5. Accessibilité et conditions</h4>
                <div class="mb-3">
                    <label class="form-label">Avez-vous un accès régulier à Internet ? *</label>
                    <select class="form-select" name="acces_internet" required>
                        <option value="">-- Sélectionner --</option>
                        <option>Oui</option>
                        <option>Non</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Apprenez-vous plutôt via…</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="appareil" id="smartphone" value="smartphone">
                        <label class="form-check-label" for="smartphone">Un smartphone</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="appareil" id="ordinateur" value="ordinateur">
                        <label class="form-check-label" for="ordinateur">Un ordinateur</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="appareil" id="lesDeux" value="les deux">
                        <label class="form-check-label" for="lesDeux">Les deux</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Besoins spécifiques d’accessibilité</label>
                    <input type="text" class="form-control" name="accessibilite" placeholder="Ex : lecture facile, sous-titres, gros caractères…">
                </div>
            </div>

            <!-- Consentements -->
            <div class="form-section">
                <h4>6. Consentements et finalisation</h4>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="rgpd" name="rgpd" required>
                    <label class="form-check-label" for="rgpd">
                        Je consens à la gestion de mes données selon la politique RGPD de Yitro Learning
                        <a href="#">(voir la politique)</a>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="charte" name="charte" required>
                    <label class="form-check-label" for="charte">
                        Je m’engage à respecter la charte de bonne conduite de la communauté Yitro
                        <a href="#">(voir la charte)</a>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Créer mon compte</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Suivez-nous</h4>
                    <ul>
                        <li><a href="#">Suivez-nous sur les réseaux sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités.</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Yitro Learning</h4>
                    <ul>
                        <li><a href="#">Lot 304-D_240 Andafiatsimo Ambohitrinibe</a></li>
                        <li><a href="#">110 Antsirabe</a></li>
                        <li><a href="#">Lun – Ven | 08h à 14h à 18h</a></li>
                        <li><a href="#">Sam – Dim | Fermé</a></li>
                        <li><a href="#">contact@yitro-consulting.com</a></li>
                        <li><a href="#">+261 34 53 313 87</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>À propos</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="catalogue.php">Formations</a></li>
                        <li><a href="#">Certifications</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="#">Mentions légales</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Tous droits réservés – SK Yitro Consulting © 2025</p>
        </div>
    </footer>

    <script src="../asset/js/inscription-apprenant.js"></script>
    <script>
        // JavaScript pour gérer l'animation et la visibilité de la barre de navigation
        const topNav = document.getElementById('top-nav');
        let lastScrollTop = 0;
        let isAnimating = false;
        let scrollTimeout = null;

        // Fonction de debouncing pour limiter les appels pendant le défilement
        function handleScroll() {
            if (isAnimating) return; // Évite les interférences pendant l'animation

            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > 100) {
                // Défilement vers le bas ou loin du haut
                if (!topNav.classList.contains('hidden')) {
                    isAnimating = true;
                    topNav.classList.add('hidden');
                    // Attendre la fin de l'animation (500ms) avant d'appliquer display: none
                    setTimeout(() => {
                        topNav.classList.add('invisible');
                        isAnimating = false;
                    }, 500);
                }
            } else {
                // Près du haut de la page
                if (topNav.classList.contains('hidden') || topNav.classList.contains('invisible')) {
                    isAnimating = true;
                    topNav.classList.remove('invisible'); // Rétablit display: flex
                    // Forcer un reflow pour que l'animation de réapparition fonctionne
                    topNav.offsetHeight; // Trigger reflow
                    topNav.classList.remove('hidden');
                    setTimeout(() => {
                        isAnimating = false;
                    }, 500);
                }
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        }

        // Écouteur d'événement avec debouncing
        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(handleScroll, 100); // Délai de 100ms pour le debouncing
        });
    </script>
</body>
</html>