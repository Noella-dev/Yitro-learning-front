
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

// Récupérer les cours auxquels l'utilisateur est inscrit
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare(
    "SELECT c.* 
     FROM cours c 
     JOIN inscriptions i ON c.id = i.cours_id 
     WHERE i.utilisateur_id = ? AND i.statut_paiement = 'paye'"
);
$stmt->execute([$user_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier le statut de complétion pour chaque cours
$cours_statuts = [];
foreach ($cours as $course) {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS total_modules, 
                SUM(CASE WHEN c.module_id IS NOT NULL THEN 1 ELSE 0 END) AS completed_modules
         FROM modules m
         LEFT JOIN completions c ON m.id = c.module_id 
             AND c.utilisateur_id = ? 
             AND c.cours_id = ?
         WHERE m.cours_id = ?"
    );
    $stmt->execute([$user_id, $course['id'], $course['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $total_modules = $result['total_modules'];
    $completed_modules = $result['completed_modules'];
    $cours_statuts[$course['id']] = [
        'is_completed' => $total_modules > 0 && $total_modules == $completed_modules,
        'progress' => $total_modules > 0 ? ($completed_modules / $total_modules * 100) : 0
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Yitro Learning</title>
    <link rel="stylesheet" href="../../asset/css/styles.css">
    <link rel="icon" href="asset/images/Yitro_consulting.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Header Styles */
        header {
            background: linear-gradient(109deg, #132F3F, #01ae8f);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 45px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.1);
        }

        .logo-text {
            text-decoration: none;
            font-weight: 700;
            font-size: 20px;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 25px;
            margin: 0;
            padding: 0;
        }

        .nav-list a {
            text-decoration: none;
            color: #e0e0e0;
            font-weight: 500;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .nav-list a:hover {
            color: #9b8227;
            transform: translateY(-2px);
        }

        .auth-links .nav-list {
            gap: 15px;
        }

        .auth-links .btn-primary {
            background-color: #9b8227;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .auth-links .btn-primary:hover {
            background-color: #e68c32;
            transform: scale(1.05);
        }

        /* Courses Section */
        .courses-section {
            padding: 60px 20px;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            font-size: 2.2em;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .course-card {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .course-img img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 3px solid #9b8227;
        }

        .course-content {
            padding: 20px;
        }

        .course-content h3 {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .course-content p {
            font-size: 0.95em;
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .course-content .price {
            font-size: 1.1em;
            font-weight: 700;
            color: #2ecc71;
            margin-bottom: 15px;
        }

        .course-content .course-status {
            font-size: 0.9em;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .course-status.completed {
            color: #2ecc71;
        }

        .course-status.in-progress {
            color: #9b8227;
        }

        .btn-learn {
            display: block;
            text-align: center;
            background: #9b8227;
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-learn:hover {
            background: #e68c32;
            transform: scale(1.03);
        }

        .no-courses {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 40px auto;
        }

        .no-courses p {
            font-size: 1.1em;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .no-courses .btn-learn {
            background: #9b8227;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .no-courses .btn-learn:hover {
            background: #e68c32;
            transform: scale(1.03);
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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
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

        /* Responsive Styles */
        @media (max-width: 768px) {
            .main-nav {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .nav-list {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }

            .auth-links .nav-list {
                width: 100%;
                justify-content: center;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }

            .course-img img {
                height: 180px;
            }

            .section-title {
                font-size: 1.8em;
            }

            .no-courses {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .logo img {
                height: 35px;
            }

            .logo-text {
                font-size: 18px;
            }

            .course-content h3 {
                font-size: 1.2em;
            }

            .course-content p {
                font-size: 0.9em;
            }

            .course-img img {
                height: 150px;
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
                <li><a href="espace_apprenant.php">Catalogues</a></li>
                <li><a href="mes_cours.php">Mes cours</a></li>

            </ul>
            <div class="auth-links">
                <ul class="nav-list">
                    <li>
                        <a href="#" class="btn-primary">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($user['nom']); ?>
                        </a>
                    </li>
                    <li><a href="../../authentification/logout.php" class="btn-primary">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="courses-section">
        <h2 class="section-title">Mes Cours</h2>
        <div class="courses-grid">
            <?php if (empty($cours)): ?>
                <div class="no-courses">
                    <p>Aucun cours disponible. Inscrivez-vous à un cours dès maintenant !</p>
                    <a href="espace_apprenant.php" class="btn-learn">Voir les cours</a>
                </div>
            <?php else: ?>
                <?php foreach ($cours as $c): ?>
                    <div class="course-card">
                        <div class="course-img">
                            <?php if ($c['photo']): ?>
                                <img src="../../Uploads/cours/<?php echo htmlspecialchars($c['photo']); ?>" alt="<?php echo htmlspecialchars($c['titre']); ?>">
                            <?php else: ?>
                                <img src="../../asset/images/default_course.jpg" alt="Image par défaut">
                            <?php endif; ?>
                        </div>
                        <div class="course-content">
                            <h3><?php echo htmlspecialchars($c['titre']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($c['description'], 0, 100)) . (strlen($c['description']) > 100 ? '...' : ''); ?></p>
                            <div class="price"><?php echo number_format($c['prix'], 2); ?> €</div>
                            <div class="course-status <?php echo $cours_statuts[$c['id']]['is_completed'] ? 'completed' : 'in-progress'; ?>">
                                Statut : <?php echo $cours_statuts[$c['id']]['is_completed'] ? 'Terminé' : 'En cours (' . round($cours_statuts[$c['id']]['progress']) . '%)'; ?>
                            </div>
                            <a href="cours_details.php?id=<?php echo $c['id']; ?>" class="btn-learn">
                                <?php echo $cours_statuts[$c['id']]['is_completed'] ? 'Voir les détails' : 'Continuer'; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-column">
                <h4>Suivez-nous</h4>
                <ul>
                    <li><a href="#">Suivez-nous sur les Réseaux Sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités.</a></li>
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
        <div class="footer-bottom">
            <p>Tous droits réservés – SK Yitro Consulting © 2024</p>
        </div>
    </footer>
</body>
</html>
