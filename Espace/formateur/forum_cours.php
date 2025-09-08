<?php
session_start();
require_once '../config/db.php';

// Vérifier si le formateur est connecté
if (!isset($_SESSION['formateur_id'])) {
    header("Location: ../../authentification/logout.php");
    exit;
}

$formateur_id = $_SESSION['formateur_id'];

// Récupérer le nom du formateur
$trainer_name = "Formateur";
try {
    $stmt = $pdo->prepare("SELECT nom_prenom FROM formateurs WHERE id = ?");
    $stmt->execute([$formateur_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $trainer_name = htmlspecialchars($row['nom_prenom']);
    }
} catch (PDOException $e) {
    error_log("Erreur de requête : " . $e->getMessage());
}

// Vérifier si l'ID du cours est fourni
if (!isset($_GET['id'])) {
    die("Cours introuvable.");
}

$cours_id = $_GET['id'];

// Vérifier que le cours appartient au formateur
$stmt = $pdo->prepare("SELECT titre FROM cours WHERE id = ? AND formateur_id = ?");
$stmt->execute([$cours_id, $formateur_id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cours) {
    die("Cours non trouvé ou vous n'avez pas l'autorisation d'y accéder.");
}

// Récupérer les forums du cours
$stmt = $pdo->prepare("SELECT id, titre, description, date_creation FROM forum WHERE cours_id = ?");
$stmt->execute([$cours_id]);
$forums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les posts pour chaque forum avec indicateur formateur/apprenant
$posts = [];
foreach ($forums as $forum) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.contenu, p.date_post, COALESCE(f.nom_prenom, u.nom) AS auteur_nom,
               CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS is_formateur
        FROM post p
        JOIN utilisateurs u ON p.auteur_id = u.id
        LEFT JOIN formateurs f ON u.email = f.email
        WHERE p.forum_id = ?
        ORDER BY p.date_post ASC
    ");
    $stmt->execute([$forum['id']]);
    $posts[$forum['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Trouver l'ID de l'utilisateur correspondant au formateur (via email)
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = (SELECT email FROM formateurs WHERE id = ?)");
$stmt->execute([$formateur_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_id = $user ? $user['id'] : null;

if (!$user_id) {
    die("Utilisateur correspondant au formateur non trouvé.");
}

// Gérer l'ajout d'un nouveau post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forum_id'], $_POST['contenu'])) {
    $forum_id = $_POST['forum_id'];
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO post (auteur_id, forum_id, contenu, date_post) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user_id, $forum_id, $contenu]);
            header("Location: forum_cours.php?id=$cours_id&success=Post ajouté avec succès");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du post : " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "Le contenu du post ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Forum du cours</title>
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
            margin-bottom: 40px;
            position: relative;
        }
        .header--title {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header--title h2 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .header--title span {
            color: #01ae8f;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .header--title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 0;
            height: 4px;
            background: linear-gradient(to right, #01ae8f, #2ecc71);
            animation: progressBar 2s forwards;
        }
        @keyframes progressBar {
            to { width: 100px; }
        }
        .user--info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .search--box {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .search--box:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .search--box i {
            color: #01ae8f;
            transition: transform 0.3s;
        }
        .search--box:hover i {
            transform: scale(1.2);
        }
        .search--box input {
            border: none;
            outline: none;
            font-size: 0.95rem;
            margin-left: 12px;
            width: 150px;
            transition: width 0.3s;
        }
        .search--box input:focus {
            width: 200px;
            border-bottom: 2px solid #01ae8f;
        }
        .trainer-name {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            background: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            margin-top: 20px;
            padding: 0 15px;
            max-height: 400px;
            overflow-y: auto;
        }
        .post-item {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 16px;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .post-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .post-formateur {
            margin-left: auto;
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: #fff;
            border-radius: 16px 16px 4px 16px;
        }
        .post-apprenant {
            margin-right: auto;
            background: linear-gradient(145deg, #f1f5f9, #e6f0fa);
            color: #2c3e50;
            border-radius: 16px 4px 16px 16px;
        }
        .post-item .author {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .post-formateur .author i {
            color: #fff;
        }
        .post-apprenant .author i {
            color: #01ae8f;
        }
        .post-item .date {
            font-size: 0.8rem;
            margin-bottom: 8px;
            opacity: 0.8;
        }
        .post-formateur .date {
            color: #e6f0fa;
        }
        .post-apprenant .date {
            color: #6b7280;
        }
        .post-item .content {
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .form-group {
            margin-top: 20px;
            padding: 15px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
            width: 100%;
        }
        .form-control:focus {
            border-color: #01ae8f;
            box-shadow: 0 0 10px rgba(1, 174, 143, 0.3);
            transform: scale(1.01);
            outline: none;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back {
            background: linear-gradient(45deg, #01ae8f, #008f75);
            color: #fff;
            border: none;
        }
        .btn-back:hover {
            background: linear-gradient(45deg, #008f75, #01ae8f);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(1, 174, 143, 0.3);
        }
        .btn-success {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: #fff;
            border: none;
        }
        .btn-success:hover {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
        }
        .btn:active {
            transform: scale(0.95);
            transition: transform 0.1s;
        }
        .error, .success {
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .error {
            background: #fee2e2;
            color: #dc3545;
        }
        .success {
            background: #d1fae5;
            color: #2ecc71;
        }
        .no-posts {
            color: #4a5568;
            font-size: 0.95rem;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 12px;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .main--content { padding: 25px; }
            .header--title h2 { font-size: 1.6rem; }
            .forum-section h3 { font-size: 1.3rem; }
            .post-item { max-width: 80%; }
            .form-group { padding: 12px; }
            .btn-back { padding: 10px 20px; }
        }
        @media (max-width: 480px) {
            .main--content { padding: 15px; }
            .header--wrapper { flex-direction: column; align-items: flex-start; gap: 15px; }
            .header--title { flex-direction: column; align-items: flex-start; gap: 10px; }
            .user--info { margin-top: 10px; }
            .btn { padding: 10px 20px; font-size: 0.9rem; }
            .btn-back { padding: 8px 16px; font-size: 0.85rem; }
            .post-item { max-width: 90%; padding: 10px; }
            .post-item .author { font-size: 0.85rem; }
            .post-item .date { font-size: 0.75rem; }
            .post-item .content { font-size: 0.9rem; }
            .search--box input { width: 100px; }
            .search--box input:focus { width: 120px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li>
                <a href="espace_formateur.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            </li>
            <li>
                <a href="create_cours.php"><i class="fas fa-user-cog"></i><span>Créer un cours</span></a>
            </li>
            <li class="active">
                <a href="liste_cours.php"><i class="fas fa-folder-open"></i><span>Mes cours</span></a>
            </li>
            <li>
                <a href="progression_apprenants.php"><i class="fas fa-chart-line"></i><span>Progression des apprenants</span></a>
            </li>
            <li>
                <a href="liste_quiz.php"><i class="fas fa-question-circle"></i><span>Gestion des quiz</span></a>
            </li>
            <li class="logout">
                <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </li>
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <a href="liste_cours.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
                <div>
                    <span>Primary</span>
                    <h2>Forum du cours : <?php echo htmlspecialchars($cours['titre']); ?></h2>
                </div>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <i class="fas fa-user-circle"></i>
                <span class="trainer-name"><?php echo $trainer_name; ?></span>
            </div>
        </div>

        <div class="forum-section">
            <h3>Forums</h3>
            <?php if (empty($forums)): ?>
                <div class="no-posts">
                    Aucun forum disponible pour ce cours.
                </div>
            <?php else: ?>
                <?php if (isset($error)): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php endif; ?>
                <?php foreach ($forums as $forum): ?>
                    <div class="forum-card">
                        <h4><?php echo htmlspecialchars($forum['titre']); ?></h4>
                        <p><?php echo htmlspecialchars($forum['description'] ?? 'Aucune description'); ?></p>
                        <p class="date">Créé le : <?php echo date('d/m/Y H:i', strtotime($forum['date_creation'])); ?></p>
                        <div class="post-list">
                            <?php if (empty($posts[$forum['id']])): ?>
                                <div class="no-posts">Aucun post dans ce forum.</div>
                            <?php else: ?>
                                <?php foreach ($posts[$forum['id']] as $post): ?>
                                    <div class="post-item <?php echo $post['is_formateur'] ? 'post-formateur' : 'post-apprenant'; ?>">
                                        <div class="author">
                                            <i class="fas <?php echo $post['is_formateur'] ? 'fa-user' : 'fa-user-graduate'; ?>"></i>
                                            <?php echo htmlspecialchars($post['auteur_nom']); ?>
                                        </div>
                                        <div class="date"><?php echo date('d/m/Y H:i', strtotime($post['date_post'])); ?></div>
                                        <div class="content"><?php echo htmlspecialchars($post['contenu']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <form method="post" class="form-group">
                            <input type="hidden" name="forum_id" value="<?php echo $forum['id']; ?>">
                            <label for="contenu-<?php echo $forum['id']; ?>">Répondre au forum</label>
                            <textarea name="contenu" id="contenu-<?php echo $forum['id']; ?>" class="form-control" rows="4" required placeholder="Votre réponse..."></textarea>
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Publier</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Animations GSAP
        gsap.from(".header--wrapper", { opacity: 0, y: -60, duration: 1.2, ease: "elastic.out(1, 0.5)" });
        gsap.from(".btn-back", { opacity: 0, x: -20, duration: 0.8, ease: "power2.out", delay: 0.3 });
        gsap.from(".forum-card", { opacity: 0, y: 40, duration: 1, stagger: 0.2, ease: "power3.out", delay: 0.3 });
        gsap.from(".post-formateur", { opacity: 0, x: 20, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.5 });
        gsap.from(".post-apprenant", { opacity: 0, x: -20, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.5 });
        gsap.from(".form-group", { opacity: 0, y: 30, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.7 });
        gsap.from(".btn-success", { opacity: 0, scale: 0.7, duration: 0.6, stagger: 0.1, ease: "back.out(2)", delay: 0.9 });
        gsap.from(".error, .success", { opacity: 0, x: -30, duration: 1, ease: "power3.out", delay: 0.3 });

        // Animation bouton survol
        document.querySelectorAll(".btn").forEach(btn => {
            btn.addEventListener("mouseenter", () => {
                gsap.to(btn, { scale: 1.05, boxShadow: "0 6px 12px rgba(0,0,0,0.2)", duration: 0.3 });
            });
            btn.addEventListener("mouseleave", () => {
                gsap.to(btn, { scale: 1, boxShadow: "0 4px 10px rgba(0,0,0,0.15)", duration: 0.3 });
            });
        });

        // Faire défiler chaque post-list vers le bas
        document.querySelectorAll(".post-list").forEach(postList => {
            postList.scrollTop = postList.scrollHeight;
        });

        // Défiler vers le bas après ajout d'un post (si succès)
        <?php if (isset($_GET['success'])): ?>
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