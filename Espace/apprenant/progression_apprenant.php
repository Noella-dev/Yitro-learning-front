<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Récupérer le nom de l'utilisateur
$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Récupérer les cours inscrits et leur progression basée sur les quiz
$stmt = $pdo->prepare("
    SELECT c.id, c.titre,
           COUNT(DISTINCT q.id) AS total_quiz,
           COUNT(DISTINCT CASE WHEN rq.score >= q.score_minimum THEN rq.id END) AS quiz_reussis
    FROM inscriptions i
    JOIN cours c ON i.cours_id = c.id
    LEFT JOIN modules m ON m.cours_id = c.id
    LEFT JOIN quiz q ON q.module_id = m.id
    LEFT JOIN resultats_quiz rq ON rq.quiz_id = q.id AND rq.utilisateur_id = ?
    WHERE i.utilisateur_id = ? AND i.statut_paiement = 'paye'
    GROUP BY c.id, c.titre
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Progression - Yitro Learning</title>
    <link rel="stylesheet" href="../../asset/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        /* En-tête */
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

        /* Section Progression */
        .progression-section {
            padding: 40px 20px;
            background: #f3f3f3;
            min-height: 100vh;
        }

        .progression-section .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .progression-section h1 {
            font-size: 2em;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .course-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .course-card h3 {
            font-size: 1.3em;
            color: #34495e;
            margin-bottom: 15px;
        }

        .progress-bar-container {
            background: #e0e0e0;
            border-radius: 8px;
            height: 20px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            background: #2ecc71;
            height: 100%;
            width: 0;
            transition: width 1s ease-in-out;
        }

        .progress-text {
            font-size: 0.95em;
            color: #2c3e50;
            margin-top: 10px;
            font-weight: 500;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: scale(1.03);
        }

        .no-courses {
            font-size: 1em;
            color: #7f8c8d;
            text-align: center;
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .progression-section h1 {
                font-size: 1.8em;
            }

            .course-card h3 {
                font-size: 1.2em;
            }
        }

        @media (max-width: 480px) {
            .progression-section {
                padding: 20px 10px;
            }

            .btn-back {
                width: 100%;
                margin: 10px 0;
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
                    <a href="espace_apprenant.php">Catalogues</a>
                </li>
                <li class="dropdown">
                    <a href="mes_cours.php">Mes cours</a>
                </li>
                <li class="dropdown">
                    <a href="progression_apprenant.php">Ma progression</a>
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

    <section class="progression-section">
        <div class="container">
            <h1>Ma Progression</h1>
            <?php if (empty($cours)): ?>
                <p class="no-courses">Vous n'êtes inscrit à aucun cours pour le moment.</p>
            <?php else: ?>
                <?php foreach ($cours as $c): ?>
                    <?php
                    $progression = $c['total_quiz'] > 0 ? ($c['quiz_reussis'] / $c['total_quiz']) * 100 : 0;
                    ?>
                    <div class="course-card">
                        <h3><?php echo htmlspecialchars($c['titre']); ?></h3>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: <?php echo $progression; ?>%;"></div>
                        </div>
                        <div class="progress-text">
                            <?php echo $c['quiz_reussis']; ?> / <?php echo $c['total_quiz']; ?> quiz réussis (<?php echo round($progression, 1); ?>%)
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="espace_apprenant.php" class="btn-back">Retour à l'espace apprenant</a>
        </div>
    </section>

    <script>
        // Animations GSAP
        gsap.from(".progression-section h1", {
            opacity: 0,
            y: 50,
            duration: 1,
            ease: "power3.out"
        });

        gsap.from(".course-card", {
            opacity: 0,
            y: 30,
            duration: 0.8,
            stagger: 0.2,
            ease: "power2.out",
            scrollTrigger: {
                trigger: ".course-card",
                start: "top 80%",
            }
        });

        gsap.from(".progress-bar", {
            width: 0,
            duration: 1.5,
            ease: "power3.inOut",
            stagger: 0.3,
            delay: 0.5
        });

        gsap.from(".btn-back", {
            opacity: 0,
            scale: 0.8,
            duration: 0.5,
            ease: "back.out(1.7)",
            delay: 0.7,
            onComplete: () => {
                gsap.to(".btn-back", {
                    scale: 1.1,
                    duration: 0.2,
                    repeat: -1,
                    yoyo: true,
                    ease: "power1.inOut",
                    paused: true,
                    onStart: function() { this.targets().forEach(btn => btn.addEventListener('mouseenter', () => this.play())) },
                    onComplete: function() { this.targets().forEach(btn => btn.addEventListener('mouseleave', () => this.pause())) }
                });
            }
        });
    </script>
</body>
</html>