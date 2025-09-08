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

// Récupérer les cours du formateur
$stmt = $pdo->prepare("SELECT id, titre, description, prix, photo FROM cours WHERE formateur_id = ?");
$stmt->execute([$formateur_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les modules, leçons et forums pour chaque cours
$modules = [];
$lecons = [];
$forums = [];
foreach ($cours as $c) {
    // Modules
    $stmt = $pdo->prepare("SELECT id, titre, description FROM modules WHERE cours_id = ?");
    $stmt->execute([$c['id']]);
    $modules[$c['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Leçons
    foreach ($modules[$c['id']] as $module) {
        $stmt = $pdo->prepare("SELECT id, titre, format, fichier FROM lecons WHERE module_id = ?");
        $stmt->execute([$module['id']]);
        $lecons[$module['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Forums
    $stmt = $pdo->prepare("SELECT id, titre FROM forum WHERE cours_id = ?");
    $stmt->execute([$c['id']]);
    $forums[$c['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Mes cours</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
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
        .course-section {
            margin-bottom: 50px;
        }
        .course-section h3 {
            color: #2c3e50;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 25px;
        }
        .course-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.4s, box-shadow 0.4s;
        }
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .course-header {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .course-image {
            width: 220px;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #01ae8f;
            transition: transform 0.3s;
        }
        .course-image:hover {
            transform: scale(1.05);
        }
        .course-info {
            flex: 1;
        }
        .course-info h4 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .course-info p {
            color: #4a5568;
            font-size: 1rem;
            margin-bottom: 12px;
        }
        .course-info .price {
            color: #2ecc71;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .course-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .module-list {
            margin-top: 25px;
            padding-left: 25px;
        }
        .module-item {
            background: #e6f0fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #d1e0f0;
        }
        .module-item h5 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 12px;
        }
        .lesson-list {
            margin-top: 15px;
            padding-left: 25px;
        }
        .lesson-item {
            background: linear-gradient(145deg, #f1f5f9 0%, #e6f0fa 100%);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            font-size: 0.95rem;
            border: 2px solid #d1e0f0;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        .lesson-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        .lesson-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        .lesson-info i {
            color: #01ae8f;
            font-size: 1.2rem;
        }
        .lesson-info span {
            color: #2c3e50;
            font-weight: 500;
        }
        .lesson-info .file-link {
            color: #4a5568;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .lesson-info .file-link:hover {
            color: #01ae8f;
        }
        .lesson-info .file-missing {
            color: #dc3545;
            font-size: 0.85rem;
            font-style: italic;
        }
        .forum-badge {
            background: linear-gradient(45deg, #01ae8f, #008f75);
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #9b8227, #e68c32);
            color: #fff;
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #e68c32, #9b8227);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(155, 130, 39, 0.3);
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
        .btn-danger {
           

 background: linear-gradient(45deg, #dc3545, #c82333);
            color: #fff;
            border: none;
        }
        .btn-danger:hover {
            background: linear-gradient(45deg, #c82333, #dc3545);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
        .btn-info {
            background: linear-gradient(45deg, #01ae8f, #008f75);
            color: #fff;
            border: none;
        }
        .btn-info:hover {
            background: linear-gradient(45deg, #008f75, #01ae8f);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(1, 174, 143, 0.3);
        }
        .btn:active {
            transform: scale(0.95);
            transition: transform 0.1s;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        .no-courses {
            text-align: center;
            color: #4a5568;
            font-size: 1.3rem;
            font-weight: 500;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        /* Responsive */
        @media (max-width: 768px) {
            .main--content { padding: 25px; }
            .header--title h2 { font-size: 1.6rem; }
            .course-section h3 { font-size: 1.3rem; }
            .course-header { flex-direction: column; align-items: flex-start; }
            .course-image { width: 100%; height: 140px; }
            .course-actions { flex-direction: column; gap: 10px; }
            .search--box input { width: 120px; }
            .search--box input:focus { width: 150px; }
            .lesson-list { padding-left: 15px; }
            .lesson-item { flex-direction: column; align-items: flex-start; gap: 10px; }
            .lesson-info i { font-size: 1rem; }
            .btn-sm { padding: 6px 12px; font-size: 0.85rem; }
        }
        @media (max-width: 480px) {
            .main--content { padding: 15px; }
            .header--wrapper { flex-direction: column; align-items: flex-start; }
            .user--info { margin-top: 15px; }
            .course-image { height: 120px; }
            .btn { padding: 8px 16px; font-size: 0.9rem; }
            .search--box input { width: 100px; }
            .search--box input:focus { width: 120px; }
            .lesson-list { padding-left: 10px; }
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
                <a href="#"><i class="fas fa-folder-open"></i><span>Mes cours</span></a>
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
                <span>Primary</span>
                <h2>Mes cours</h2>
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

        <div class="course-section">
            <h3>Mes cours</h3>
            <?php if (empty($cours)): ?>
                <div class="no-courses">
                    Aucun cours créé pour le moment. <a href="create_cours.php">Créer un cours</a>.
                </div>
            <?php else: ?>
                <?php foreach ($cours as $c): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <img src="<?php echo htmlspecialchars($c['photo'] && file_exists(__DIR__ . '/../../Uploads/cours/' . $c['photo']) ? '../../Uploads/cours/' . $c['photo'] : '../../asset/images/default_course.jpg'); ?>" alt="Photo du cours" class="course-image">
                            <div class="course-info">
                                <h4><?php echo htmlspecialchars($c['titre']); ?> <span class="forum-badge"><?php echo count($forums[$c['id']]); ?> Forum(s)</span></h4>
                                <p><?php echo htmlspecialchars(substr($c['description'], 0, 100)) . (strlen($c['description']) > 100 ? '...' : ''); ?></p>
                                <p class="price"><?php echo number_format($c['prix'], 2); ?> €</p>
                                <div class="course-actions">
                                    <a href="edit_cours.php?id=<?php echo $c['id']; ?>" class="btn btn-success"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="delete_cours.php?id=<?php echo $c['id']; ?>" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce cours ?');"><i class="fas fa-trash"></i> Supprimer</a>
                                    <a href="forum_cours.php?id=<?php echo $c['id']; ?>" class="btn btn-info"><i class="fas fa-comments"></i> Accéder au forum</a>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($modules[$c['id']])): ?>
                            <div class="module-list">
                                <?php foreach ($modules[$c['id']] as $module): ?>
                                    <div class="module-item">
                                        <h5><?php echo htmlspecialchars($module['titre']); ?></h5>
                                        <p><?php echo htmlspecialchars(substr($module['description'], 0, 80)) . (strlen($module['description']) > 80 ? '...' : ''); ?></p>
                                        <div class="course-actions">
                                            <a href="edit_module.php?id=<?php echo $module['id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                                            <a href="delete_module.php?id=<?php echo $module['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce module ?');"><i class="fas fa-trash"></i> Supprimer</a>
                                        </div>
                                        <?php if (!empty($lecons[$module['id']])): ?>
                                            <div class="lesson-list">
                                                <?php foreach ($lecons[$module['id']] as $lecon): ?>
                                                    <div class="lesson-item">
                                                        <div class="lesson-info">
                                                            <i class="fas <?php
                                                                echo $lecon['format'] === 'pdf' ? 'fa-file-pdf' :
                                                                    ($lecon['format'] === 'audio' ? 'fa-file-audio' : 'fa-file-video');
                                                            ?>"></i>
                                                            <span><?php echo htmlspecialchars($lecon['titre']) . ' (' . $lecon['format'] . ')'; ?></span>
                                                            <?php if ($lecon['fichier'] && file_exists(__DIR__ . '/../../Uploads/lecons/' . $lecon['fichier'])): ?>
                                                                <a href="../../Uploads/lecons/<?php echo htmlspecialchars($lecon['fichier']); ?>" class="file-link" target="_blank"><?php echo htmlspecialchars($lecon['fichier']); ?></a>
                                                            <?php else: ?>
                                                                <span class="file-missing">Fichier non trouvé</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="course-actions">
                                                            <?php if ($lecon['fichier'] && file_exists(__DIR__ . '/../../Uploads/lecons/' . $lecon['fichier'])): ?>
                                                                <a href="../../Uploads/lecons/<?php echo htmlspecialchars($lecon['fichier']); ?>" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-eye"></i> Voir</a>
                                                            <?php endif; ?>
                                                            <a href="edit_lecon.php?id=<?php echo $lecon['id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                                                            <a href="delete_lecon.php?id=<?php echo $lecon['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette leçon ?');"><i class="fas fa-trash"></i> Supprimer</a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Animations GSAP
        gsap.from(".header--wrapper", { opacity: 0, y: -60, duration: 1.2, ease: "elastic.out(1, 0.5)" });
        gsap.from(".course-card", { opacity: 0, y: 40, rotationX: 10, duration: 1, stagger: 0.2, ease: "power3.out", delay: 0.3 });
        gsap.from(".module-item", { opacity: 0, y: 30, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.5 });
        gsap.from(".lesson-item", { opacity: 0, x: 30, rotationX: 5, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.7 });
        gsap.from(".btn", { opacity: 0, scale: 0.7, duration: 0.6, stagger: 0.1, ease: "back.out(2)", delay: 0.9 });
        gsap.from(".no-courses", { opacity: 0, y: 30, duration: 1, ease: "power3.out", delay: 0.3 });
        // Animation bouton survol
        document.querySelectorAll(".btn").forEach(btn => {
            btn.addEventListener("mouseenter", () => {
                gsap.to(btn, { scale: 1.05, boxShadow: "0 6px 12px rgba(0,0,0,0.2)", duration: 0.3 });
            });
            btn.addEventListener("mouseleave", () => {
                gsap.to(btn, { scale: 1, boxShadow: "0 4px 10px rgba(0,0,0,0.15)", duration: 0.3 });
            });
        });
    </script>
</body>
</html>