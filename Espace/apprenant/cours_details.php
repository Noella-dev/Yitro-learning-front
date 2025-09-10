<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Récupérer le nom de l'utilisateur pour l'affichage
$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Vérifier si l'ID du cours est fourni
if (!isset($_GET['id'])) {
    header("Location: espace_apprenant.php");
    exit();
}

$cours_id = $_GET['id'];
$utilisateur_id = $_SESSION['user_id'];

// Récupérer les détails du cours
$stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ?");
$stmt->execute([$cours_id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    header("Location: espace_apprenant.php");
    exit();
}

// Vérifier si l'utilisateur est inscrit au cours
$stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE utilisateur_id = ? AND cours_id = ? AND statut_paiement = 'paye'");
$stmt->execute([$utilisateur_id, $cours_id]);
$is_enrolled = $stmt->fetch(PDO::FETCH_ASSOC) !== false;

// Récupérer le nom du formateur
$stmt = $pdo->prepare("SELECT nom_prenom FROM formateurs WHERE id = ?");
$stmt->execute([$cours['formateur_id']]);
$formateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les modules
$stmt = $pdo->prepare("SELECT * FROM modules WHERE cours_id = ?");
$stmt->execute([$cours_id]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les complétions de l'utilisateur pour ce cours
$stmt = $pdo->prepare("SELECT module_id FROM completions WHERE utilisateur_id = ? AND cours_id = ?");
$stmt->execute([$utilisateur_id, $cours_id]);
$completed_modules = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les leçons pour chaque module
$lecons = [];
foreach ($modules as $module) {
    $stmt = $pdo->prepare("SELECT * FROM lecons WHERE module_id = ?");
    $stmt->execute([$module['id']]);
    $lecons[$module['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les quiz pour chaque module et vérifier les complétions
$quiz = [];
$completed_quizzes = [];
foreach ($modules as $module) {
    $stmt = $pdo->prepare("SELECT id, titre, description, score_minimum FROM quiz WHERE module_id = ?");
    $stmt->execute([$module['id']]);
    $quiz[$module['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier les complétions des quiz
    $stmt = $pdo->prepare("SELECT quiz_id FROM resultats_quiz WHERE utilisateur_id = ? AND quiz_id IN (SELECT id FROM quiz WHERE module_id = ?)");
    $stmt->execute([$utilisateur_id, $module['id']]);
    $completed_quizzes[$module['id']] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Déterminer si le cours est gratuit ou si l'utilisateur est inscrit
$is_free = $cours['prix'] == 0;
$can_access = $is_free || $is_enrolled;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du cours - Yitro Learning</title>
    <link rel="stylesheet" href="../../asset/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

        /* Styles pour l'en-tête */
        header {
            background: linear-gradient(109deg, #132F3F, #01ae8f);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 40px;
            border-radius: 50%;
        }

        .logo-text {
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
            color: #ffffff;
        }

        .nav-list {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-list li {
            position: relative;
        }

        .nav-list a {
            text-decoration: none;
            color: #cbcbcb;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s ease;
        }

        .nav-list a:hover {
            color: #9b8227;
        }

        .dropdown {
            position: relative;
        }

        .dropdown > a {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown > a i {
            font-size: 12px;
            color: #cbcbcb;
            transition: color 0.2s ease;
        }

        .dropdown:hover > a i {
            color: #9b8227;
        }

        .dropdown > a i.fa-chevron-down {
            transition: transform 0.2s ease;
        }

        .dropdown:hover > a i.fa-chevron-down {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #132F3F;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            list-style: none;
            padding: 8px 0;
            margin: 0;
            min-width: 160px;
            z-index: 1001;
        }

        .dropdown:hover > .dropdown-menu {
            display: block;
        }

        .dropdown-menu li {
            padding: 6px 15px;
            position: relative;
        }

        .dropdown-menu a {
            color: #cbcbcb;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown-menu a:hover {
            color: #9b8227;
        }

        .dropdown-menu .dropdown > a i.fa-chevron-right {
            font-size: 10px;
        }

        .dropdown-menu .dropdown:hover > .dropdown-menu {
            display: block;
            top: 0;
            left: 100%;
        }

        .auth-links .nav-list {
            gap: 12px;
        }

        .auth-links .btn-primary {
            background-color: #9b8227;
            color: white;
            padding: 6px 14px;
            border-radius: 18px;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .auth-links .btn-primary:hover {
            background-color: #e68c32;
        }

        /* Footer Styles */
        .footer {
            background: #132F3F;
            color: #e0e0e0;
            padding: 40px 20px;
        }

        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .footer-links {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
        }

        .footer-column h4 {
            font-size: 1.2em;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #cbcbcb;
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: #9b8227;
        }

        .footer-bottom {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #2a3f50;
            margin-top: 20px;
            font-size: 0.9em;
            color: #cbcbcb;
        }

        /* Styles pour la section des détails du cours */
        .course-details {
            padding: 40px 20px;
            background: rgb(243, 243, 243);
        }

        .course-details .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .course-info {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 30px;
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .course-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .course-image img:hover {
            transform: scale(1.05);
        }

        .course-text {
            flex: 1;
        }

        .course-text h1 {
            font-size: 2em;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .course-text .price {
            font-size: 1.2em;
            font-weight: 700;
            color: #2ecc71;
            margin-bottom: 15px;
        }

        .course-text .course-description {
            font-size: 1em;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .course-text .formateur {
            font-size: 0.9em;
            font-style: italic;
            color: #7f8c8d;
            margin-top: 10px;
        }

        .module {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .module h3 {
            font-size: 1.5em;
            color: #34495e;
            margin-bottom: 10px;
        }

        .module p {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 15px;
        }

        .module .completion-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .module .module-completion {
            accent-color: #2ecc71;
            width: 20px;
            height: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .module .completion-label {
            font-size: 0.9em;
            color: #2c3e50;
            font-weight: 500;
        }

        .module .completion-message {
            font-size: 0.9em;
            margin-top: 10px;
            display: none;
        }

        .module .completion-message.success {
            color: #2ecc71;
        }

        .module .completion-message.error {
            color: #e74c3c;
        }

        .lecon {
            border-top: 1px solid #eee;
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .lecon a {
            color: #007bff;
            text-decoration: none;
        }

        .lecon a:hover {
            text-decoration: underline;
        }

        .quiz {
            border-top: 1px solid #eee;
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quiz a {
            color: #01ae8f;
            text-decoration: none;
            font-weight: 500;
        }

        .quiz a:hover {
            text-decoration: underline;
        }

        .quiz .completed {
            color: #2ecc71;
            font-size: 0.9em;
            font-style: italic;
        }

        /* Masquer les contenus des leçons et quiz par défaut */
        .lesson-content, .quiz-content {
            display: none;
        }

        /* Afficher les contenus pour les cours gratuits ou payés */
        .course-free .lesson-content, .course-free .quiz-content {
            display: block;
        }

        /* Styles pour la vidéo */
        .lecon video {
            max-width: 100%;
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Styles pour l'audio */
        .lecon audio {
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
        }

        .btn-enroll {
            display: inline-block;
            background: #9b8227;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-enroll:hover {
            background: #e68c32;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        /* Styles pour la modale de paiement */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1002;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-content h3 {
            font-size: 1.6em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-content .close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.2em;
            color: #7f8c8d;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-content .close:hover {
            color: #9b8227;
        }

        .modal-content .form-group {
            margin-bottom: 20px;
        }

        .modal-content label {
            font-size: 0.95em;
            font-weight: 500;
            color: #2c3e50;
            display: block;
            margin-bottom: 8px;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            font-size: 0.95em;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }

        .modal-content input:focus {
            border-color: #9b8227;
            outline: none;
        }

        .modal-content .card-details {
            display: flex;
            gap: 15px;
        }

        .modal-content .card-details .form-group {
            flex: 1;
        }

        .modal-content button {
            width: 100%;
            background: #9b8227;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .modal-content button:hover {
            background: #e68c32;
            transform: scale(1.03);
        }

        .modal-content .error {
            color: red;
            font-size: 0.9em;
            margin-top: 10px;
            display: none;
        }

        .modal-content .success {
            color: #2ecc71;
            font-size: 0.9em;
            margin-top: 10px;
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-nav {
                flex-wrap: wrap;
                padding: 10px 15px;
            }

            .nav-list {
                gap: 15px;
            }

            .auth-links .nav-list {
                gap: 10px;
            }

            .dropdown-menu .dropdown:hover > .dropdown-menu {
                left: 0;
                top: 100%;
            }

            .footer-links {
                grid-template-columns: 1fr !important;
            }

            .course-info {
                flex-direction: column;
                align-items: center;
            }

            .course-image img {
                width: 120px;
                height: 120px;
            }

            .course-text h1 {
                font-size: 1.8em;
                text-align: center;
            }

            .course-text .price {
                text-align: center;
            }

            .course-text .course-description {
                text-align: center;
            }

            .course-text .formateur {
                text-align: center;
            }

            .module .completion-checkbox {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                padding: 20px;
                width: 95%;
            }

            .modal-content h3 {
                font-size: 1.4em;
            }

            .modal-content .card-details {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="logo">
                <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                <a href="espace_apprenant.php" class="logo-text">Yitro Learning</a>
            </div>
            <ul class="nav-list">
                <li class="dropdown">
                    <a href="#">Catalogues</a>
                </li>
                <li class="dropdown">
                    <a href="mes_cours.php">Mes cours</a>
                </li>
            </ul>
            <div class="auth-links">
                <ul class="nav-list">
                    <li>
                        <a href="#" class="btn-primary" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($user['nom']); ?>
                        </a>
                    </li>
                    <li><a href="../../authentification/logout.php" class="btn-primary">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="course-details<?php echo $can_access ? ' course-free' : ''; ?>">
        <div class="container">
            <div class="course-info">
                <?php if ($cours['photo']): ?>
                    <div class="course-image">
                        <img src="../../Uploads/cours/<?php echo htmlspecialchars($cours['photo']); ?>" alt="<?php echo htmlspecialchars($cours['titre']); ?>">
                    </div>
                <?php endif; ?>
                <div class="course-text">
                    <h1><?php echo htmlspecialchars($cours['titre']); ?></h1>
                    <div class="price"><?php echo number_format($cours['prix'], 2); ?> €</div>
                    <div class="course-description"><?php echo nl2br(htmlspecialchars($cours['description'])); ?></div>
                    <?php if ($formateur && $formateur['nom_prenom']): ?>
                        <p class="formateur">Formateur : <?php echo htmlspecialchars($formateur['nom_prenom']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <h2>Modules et Leçons</h2>
            <?php if (empty($modules)): ?>
                <p>Aucun module disponible pour ce cours.</p>
            <?php else: ?>
                <?php foreach ($modules as $module): ?>
                    <div class="module">
                        <h3><?php echo htmlspecialchars($module['titre']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($module['description'])); ?></p>
                        <?php if (isset($lecons[$module['id']]) && is_array($lecons[$module['id']]) && !empty($lecons[$module['id']])): ?>
                            <?php foreach ($lecons[$module['id']] as $index => $lecon): ?>
                                <div class="lecon">
                                    <strong><?php echo htmlspecialchars($lecon['titre']); ?></strong> (<?php echo strtoupper(htmlspecialchars($lecon['format'])); ?>)
                                    <?php
                                    $is_video = in_array(strtolower($lecon['format']), ['video']);
                                    $is_audio = in_array(strtolower($lecon['format']), ['audio']);
                                    $is_pdf = in_array(strtolower($lecon['format']), ['pdf']);

                                    $filePath = "../../uploads/lecons/" . rawurlencode($lecon['fichier']);
                                
                                    ?>
                   
                                    <div class="lesson-content">
                                        <?php if ($is_video): ?>
                                            <video controls width="600">
                                                <source src="<?php echo $filePath; ?>" type="video/mp4">
                                                Votre navigateur ne prend pas en charge la lecture de vidéos.
                                            </video>
                                        <?php elseif ($is_audio): ?>
                                            <audio controls>
                                                <source src="<?php echo $filePath; ?>" type="audio/mpeg">
                                                Votre navigateur ne prend pas en charge la lecture d'audio.
                                            </audio>
                                        <?php elseif ($is_pdf): ?>
                                            <a href="<?php echo $filePath; ?>" target="_blank">Voir le PDF</a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($index === count($lecons[$module['id']]) - 1): ?>
                                        <div class="completion-checkbox">
                                            <input type="checkbox" class="module-completion" data-module-id="<?php echo $module['id']; ?>" data-cours-id="<?php echo $cours_id; ?>" <?php echo in_array($module['id'], $completed_modules) ? 'checked' : ''; ?>>
                                            <label class="completion-label">Marquer comme terminé</label>
                                        </div>
                                        <p class="completion-message"></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="error-message">Aucune leçon disponible pour ce module.</p>
                        <?php endif; ?>
                        <?php if (isset($quiz[$module['id']]) && is_array($quiz[$module['id']]) && !empty($quiz[$module['id']])): ?>
                            <div class="quiz-content">
                                <h4>Quiz</h4>
                                <?php foreach ($quiz[$module['id']] as $q): ?>
                                    <div class="quiz">
                                        <strong><?php echo htmlspecialchars($q['titre']); ?></strong> (Score minimum : <?php echo htmlspecialchars($q['score_minimum']); ?>%)
                                        <?php if ($can_access): ?>
                                            <div>
                                                <a href="take_quiz.php?id=<?php echo $q['id']; ?>">Passer le quiz</a>
                                                <?php if (in_array($q['id'], $completed_quizzes[$module['id']])): ?>
                                                    <span class="completed"> (Complété)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!$can_access): ?>
                <a href="#" class="btn-enroll" onclick="openPaymentModal()">S'inscrire au cours</a>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!$can_access): ?>
        <div class="modal" id="paymentModal">
            <div class="modal-content">
                <span class="close" onclick="closePaymentModal()">×</span>
                <h3><i class="fas fa-credit-card"></i> Paiement du cours</h3>
                <form id="paymentForm">
                    <div class="form-group">
                        <label for="card-number"><i class="fas fa-credit-card"></i> Numéro de carte</label>
                        <input type="text" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>
                    <div class="card-details">
                        <div class="form-group">
                            <label for="expiry-date"><i class="fas fa-calendar"></i> Date d'expiration</label>
                            <input type="text" id="expiry-date" placeholder="MM/AA" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv"><i class="fas fa-lock"></i> CVV</label>
                            <input type="text" id="cvv" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="card-holder"><i class="fas fa-user"></i> Nom du titulaire</label>
                        <input type="text" id="card-holder" name="card-holder" placeholder="Nom du titulaire" required>
                    </div>
                    <button type="submit">Payer <?php echo number_format($cours['prix'], 2); ?> €</button>
                    <p class="error" id="paymentError">Veuillez remplir tous les champs correctement.</p>
                    <p class="success" id="paymentSuccess">Paiement effectué avec succès ! Vous pouvez maintenant accéder au contenu.</p>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Suivez-nous</h4>
                    <ul>
                        <li><a href="#">Suivez-nous sur les Réseaux Sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités. Rejoignez-nous sur nos réseaux sociaux.</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Yitro Learning</h4>
                    <ul>
                        <li><a href="#">Lot 304-D_240 Andafiatsimo Ambohitrinibe</a></li>
                        <li><a href="#">110 Antsirabe</a></li>
                        <li><a href="#">Lun – Ven | 08h – 12h | 14h – 18h</a></li>
                        <li><a href="#">Sam – Dim | Fermé</a></li>
                        <li><a href="#">contact@yitro-consulting.com</a></li>
                        <li><a href="#">+261 34 53 313 87</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>À propos</h4>
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
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Tous droits réservés – SK Yitro Consulting © 2024</p>
        </div>
    </footer>

    <script>
        function openPaymentModal() {
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentError').style.display = 'none';
            document.getElementById('paymentSuccess').style.display = 'none';
            document.getElementById('paymentForm').reset();
        }

        // Gestion du formulaire de paiement
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiryDate = document.getElementById('expiry-date').value;
            const cvv = document.getElementById('cvv').value;
            const cardHolder = document.getElementById('card-holder').value.trim();

            const error = document.getElementById('paymentError');
            const success = document.getElementById('paymentSuccess');

            // Réinitialisation des messages
            error.style.display = 'none';
            success.style.display = 'none';

            // Validation des champs de paiement
            const cardNumberRegex = /^\d{16}$/;
            const expiryDateRegex = /^(0[1-9]|1[0-2])\/(20)?\d{2}$/; // Nouvelle regex pour la date
            const cvvRegex = /^\d{3,4}$/;

            if (cardNumber === '') {
                error.textContent = 'Le numéro de carte ne peut pas être vide.';
                error.style.display = 'block';
                return;
            }
            if (!cardNumberRegex.test(cardNumber)) {
                error.textContent = 'Le numéro de carte est invalide. Il doit contenir 16 chiffres.';
                error.style.display = 'block';
                return;
            }

            if (expiryDate === '') {
                error.textContent = 'La date d\'expiration ne peut pas être vide.';
                error.style.display = 'block';
                return;
            }
            if (!expiryDateRegex.test(expiryDate)) {
                error.textContent = 'La date d\'expiration est invalide. Utilisez le format MM/AA ou MM/AAAA.';
                error.style.display = 'block';
                return;
            }

            if (cvv === '') {
                error.textContent = 'Le CVV ne peut pas être vide.';
                error.style.display = 'block';
                return;
            }
            if (!cvvRegex.test(cvv)) {
                error.textContent = 'Le CVV est invalide. Il doit contenir 3 ou 4 chiffres.';
                error.style.display = 'block';
                return;
            }

            if (cardHolder === '') {
                error.textContent = 'Le nom du titulaire ne peut pas être vide.';
                error.style.display = 'block';
                return;
            }

            fetch('enroll_course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cours_id=<?php echo $cours_id; ?>`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau ou réponse serveur incorrecte');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    error.style.display = 'none';
                    success.style.display = 'block';
                    setTimeout(() => {
                        closePaymentModal();
                        window.location.reload();
                    }, 2000);
                } else {
                    error.textContent = data.message || 'Une erreur est survenue lors de l\'inscription.';
                    error.style.display = 'block';
                    success.style.display = 'none';
                }
            })
            .catch(err => {
                console.error('Erreur lors de la requête:', err);
                const errorMessage = document.getElementById('paymentError');
                errorMessage.textContent = 'Une erreur de connexion est survenue. Veuillez réessayer.';
                errorMessage.style.display = 'block';
            });
        });

        // Formatage du numéro de carte
        document.getElementById('card-number')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });

        // Formatage de la date d'expiration
        document.getElementById('expiry-date')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 3) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Gestion des cases à cocher pour la complétion des modules
        document.querySelectorAll('.module-completion').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const moduleId = this.dataset.moduleId;
                const coursId = this.dataset.coursId;
                const isChecked = this.checked;
                const messageElement = this.closest('.lecon').querySelector('.completion-message');

                fetch('complete_module.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `module_id=${moduleId}&cours_id=${coursId}&is_checked=${isChecked}`
                })
                .then(response => response.json())
                .then(data => {
                    messageElement.style.display = 'block';
                    messageElement.className = 'completion-message';
                    if (data.success) {
                        messageElement.classList.add('success');
                        messageElement.textContent = data.message;
                    } else {
                        messageElement.classList.add('error');
                        messageElement.textContent = data.message;
                        this.checked = !isChecked;
                    }
                    setTimeout(() => {
                        messageElement.style.display = 'none';
                    }, 3000);
                })
                .catch(error => {
                    messageElement.style.display = 'block';
                    messageElement.classList.add('error');
                    messageElement.textContent = 'Erreur réseau : ' + error.message;
                    this.checked = !isChecked;
                    setTimeout(() => {
                        messageElement.style.display = 'none';
                    }, 3000);
                });
            });
        });

        // Animation GSAP pour les quiz
        document.querySelectorAll('.quiz').forEach(quiz => {
            gsap.from(quiz, {
                opacity: 0,
                y: 20,
                duration: 0.5,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: quiz,
                    start: "top 80%",
                }
            });
        });
    </script>
</body>
</html>