<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../authentification/connexion-admin.php");
    exit();
}

// Initialiser les messages
$success_message = '';
$error_message = '';

// Récupérer les détails du forum
$forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
if ($forum_id) {
    $stmt = $pdo->prepare("SELECT f.*, c.titre AS cours_titre FROM forum f JOIN cours c ON f.cours_id = c.id WHERE f.id = ?");
    $stmt->execute([$forum_id]);
    $forum = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$forum) {
        header("Location: gestion_forum.php");
        exit();
    }
    
    // Récupérer les messages du forum avec le nom correct (formateur ou apprenant)
    $stmt = $pdo->prepare("
        SELECT p.*, 
               COALESCE(f.nom_prenom, u.nom) AS auteur_nom,
               CASE 
                   WHEN f.id IS NOT NULL THEN 1 
                   ELSE 0 
               END AS is_formateur
        FROM post p 
        LEFT JOIN utilisateurs u ON p.auteur_id = u.id 
        LEFT JOIN formateurs f ON u.email = f.email 
        WHERE p.forum_id = ? 
        ORDER BY p.date_post ASC
    ");
    $stmt->execute([$forum_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enregistrer l'activité dans journal_activite
    $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], 'Visualisation des messages', "Forum ID: $forum_id"]);
} else {
    header("Location: gestion_forum.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Messages du Forum</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
    <style>
        .main--content {
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .header--wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .header--title h2 {
            color: #01ae8f;
            font-weight: 600;
            margin: 0;
        }

        .header--title span {
            color: #777;
            font-size: 0.9rem;
        }

        .forum-section {
            margin-bottom: 50px;
        }

        .forum-section h3 {
            color: #2c3e50;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .forum-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.4s, box-shadow 0.4s;
        }

        .forum-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .forum-card h4 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .forum-card p {
            color: #4a5568;
            font-size: 1rem;
            margin-bottom: 12px;
        }

        .post-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
            max-height: 400px;
            overflow-y: auto;
            padding: 20px 30px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .post-item {
            max-width: 70%;
            padding: 15px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .post-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
        }

        .post-apprenant {
            margin-right: auto;
            background: linear-gradient(145deg, #f1f5f9, #e6f0fa);
            color: #2c3e50;
            border-radius: 16px 4px 16px 16px;
        }

        .post-formateur {
            margin-left: auto;
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: #fff;
            border-radius: 16px 16px 4px 16px;
        }

        .post-item .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1em;
        }

        .post-apprenant .avatar {
            background: linear-gradient(45deg, #01ae8f, #008f75);
        }

        .post-formateur .avatar {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
        }

        .post-item .author {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .post-apprenant .author i {
            color: #01ae8f;
        }

        .post-formateur .author i {
            color: #fff;
        }

        .post-item .date {
            font-size: 0.8rem;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .post-apprenant .date {
            color: #6b7280;
        }

        .post-formateur .date {
            color: #e6f0fa;
        }

        .post-item .content {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .btn-back {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            background: linear-gradient(45deg, #9b8227, #e68c32);
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
        }

        .btn-back:hover {
            background: linear-gradient(45deg, #e68c32, #9b8227);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(155, 130, 39, 0.3);
        }

        .btn-back:active {
            transform: scale(0.95);
            transition: transform 0.1s;
        }

        .success, .error {
            padding: 12px 20px;
            border-radius: 12px;
            margin: 25px auto;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 600px;
        }

        .success {
            background: #d1fae5;
            color: #2ecc71;
        }

        .error {
            background: #fee2e2;
            color: #dc3545;
        }

        .no-posts {
            color: #4a5568;
            font-size: 0.95rem;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .main--content {
                padding: 25px;
            }

            .forum-section h3 {
                font-size: 1.3rem;
            }

            .post-list {
                padding: 15px 20px;
            }

            .post-item {
                max-width: 80%;
            }

            .success, .error {
                margin: 25px 15px;
            }

            .no-posts {
                margin: 20px 15px;
            }
        }

        @media (max-width: 480px) {
            .main--content {
                padding: 15px;
            }

            .forum-section h3 {
                font-size: 1.2rem;
            }

            .post-list {
                padding: 10px 15px;
            }

            .post-item {
                max-width: 90%;
                padding: 10px;
            }

            .post-item .author {
                font-size: 0.85rem;
            }

            .post-item .content {
                font-size: 0.9rem;
            }

            .post-item .date {
                font-size: 0.75rem;
            }

            .btn-back {
                padding: 10px 20px;
                font-size: 0.9rem;
            }

            .success, .error {
                margin: 20px 10px;
                font-size: 0.9rem;
            }

            .no-posts {
                margin: 20px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li>
                <a href="backoffice.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            </li>
            <li>
                <a href="gestion_utilisateurs/gestion_utilisateur.php"><i class="fas fa-user-cog"></i><span>Gestion utilisateur</span></a>
            </li>
            <li class="active">
                <a href="../gestion_forum.php"><i class="fas fa-comments"></i><span>Forum</span></a>
            </li>

            <li class="logout">
                <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </li>
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Administration</span>
                <h2>Messages du Forum</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <img src="../asset/images/lito.jpg" alt="User Profile">
            </div>
        </div>

        <div class="forum-section">
            <?php if ($success_message): ?>
                <div class="success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="forum-card">
                <a href="gestion_forum.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour aux forums</a>
                <h4><?php echo htmlspecialchars($forum['titre']); ?></h4>
                <p>Cours : <?php echo htmlspecialchars($forum['cours_titre']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($forum['description'])); ?></p>
                <p class="date">Créé le : <?php echo date('d/m/Y H:i', strtotime($forum['date_creation'])); ?></p>
                <div class="post-list">
                    <?php if (empty($posts)): ?>
                        <div class="no-posts">Aucun message dans ce forum pour le moment.</div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="post-item <?php echo $post['is_formateur'] ? 'post-formateur' : 'post-apprenant'; ?>">
                                <div class="author">
                                    <div class="avatar">
                                        <i class="fas <?php echo $post['is_formateur'] ? 'fa-user' : 'fa-user-graduate'; ?>"></i>
                                    </div>
                                    <?php echo htmlspecialchars($post['auteur_nom'] ?? 'Inconnu'); ?>
                                </div>
                                <div class="date"><?php echo date('d/m/Y H:i', strtotime($post['date_post'])); ?></div>
                                <div class="content"><?php echo nl2br(htmlspecialchars($post['contenu'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animations GSAP
        gsap.from(".header--wrapper", { 
            opacity: 0, 
            y: -20, 
            duration: 0.8, 
            ease: "power3.out" 
        });
        gsap.from(".forum-card", { 
            opacity: 0, 
            y: 40, 
            duration: 1, 
            ease: "power3.out",
            delay: 0.2 
        });
        gsap.from(".post-apprenant", { 
            opacity: 0, 
            x: -20, 
            duration: 0.6, 
            stagger: 0.1, 
            ease: "power2.out",
            delay: 0.4 
        });
        gsap.from(".post-formateur", { 
            opacity: 0, 
            x: 20, 
            duration: 0.6, 
            stagger: 0.1, 
            ease: "power2.out",
            delay: 0.4 
        });
        gsap.from(".success, .error", { 
            opacity: 0, 
            x: -30, 
            duration: 1, 
            ease: "power3.out",
            delay: 0.3 
        });
        gsap.from(".no-posts", { 
            opacity: 0, 
            x: -30, 
            duration: 1, 
            ease: "power3.out",
            delay: 0.3 
        });

        // Animation bouton survol et clic
        document.querySelectorAll(".btn-back").forEach(btn => {
            btn.addEventListener("mouseenter", () => {
                gsap.to(btn, { scale: 1.05, boxShadow: "0 6px 12px rgba(0,0,0,0.2)", duration: 0.3 });
            });
            btn.addEventListener("mouseleave", () => {
                gsap.to(btn, { scale: 1, boxShadow: "0 4px 10px rgba(0,0,0,0.15)", duration: 0.3 });
            });
            btn.addEventListener("click", () => {
                gsap.to(btn, { scale: 0.95, duration: 0.1, yoyo: true, repeat: 1 });
            });
        });

        // Faire défiler post-list vers le bas
        document.querySelectorAll(".post-list").forEach(postList => {
            postList.scrollTop = postList.scrollHeight;
        });

        // Défiler vers le bas après chargement (si succès ou erreur)
        <?php if ($success_message || $error_message): ?>
            document.querySelectorAll(".post-list").forEach(postList => {
                gsap.to(postList, {
                    scrollTo: { y: "max" },
                    duration: 0.5,
                    ease: "power2.out"
                });
            });
        <?php endif; ?>
    </script>
</body>
</html>