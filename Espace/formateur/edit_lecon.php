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

// Vérifier si l'ID de la leçon est fourni
if (!isset($_GET['id'])) {
    die("Leçon introuvable.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM lecons WHERE id = ?");
$stmt->execute([$id]);
$lecon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lecon) {
    die("Leçon non trouvée.");
}

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $format = $_POST['format'];
    $fichier = $lecon['fichier']; // Conserver l'ancien fichier par défaut

    // Gestion de l'upload de fichier
    if (!empty($_FILES['fichier']['name'])) {
        $allowed_extensions = [
            'pdf' => ['pdf'],
            'audio' => ['mp3', 'wav'],
            'video' => ['mp4', 'avi']
        ];

        $file = $_FILES['fichier'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Vérifier si l'extension correspond au format sélectionné
        if (!in_array($file_ext, $allowed_extensions[$format])) {
            $error = "Le fichier doit être au format " . implode(', ', $allowed_extensions[$format]) . " pour le format $format.";
        } else {
            // Créer le dossier Uploads/lecons si nécessaire
            $upload_dir = __DIR__ . '/../../Uploads/lecons/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Générer un nom de fichier unique
            $new_file_name = uniqid('lecon_') . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;

            // Déplacer le fichier
            if (move_uploaded_file($file_tmp, $destination)) {
                // Supprimer l'ancien fichier s'il existe
                if ($fichier && file_exists($upload_dir . $fichier)) {
                    unlink($upload_dir . $fichier);
                }
                $fichier = $new_file_name;
            } else {
                $error = "Erreur lors de l'upload du fichier.";
            }
        }
    }

    // Mettre à jour la leçon
    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE lecons SET titre = ?, format = ?, fichier = ? WHERE id = ?");
            $stmt->execute([$titre, $format, $fichier, $id]);
            header("Location: liste_cours.php?success=Leçon modifiée avec succès");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Modifier une leçon</title>
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
        .form-section {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            padding: 25px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-section h2 {
            color: #2c3e50;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 10px;
            display: block;
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
        .file-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .file-input-wrapper input[type="file"] {
            padding: 10px;
            font-size: 0.95rem;
        }
        .file-input-wrapper input[type="file"]::file-selector-button {
            background: linear-gradient(45deg, #01ae8f, #008f75);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        .file-input-wrapper input[type="file"]::file-selector-button:hover {
            background: linear-gradient(45deg, #008f75, #01ae8f);
            transform: translateY(-2px);
        }
        .current-file {
            color: #4a5568;
            font-size: 0.9rem;
            margin-top: 8px;
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
        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #5a6268);
            color: #fff;
            border: none;
        }
        .btn-secondary:hover {
            background: linear-gradient(45deg, #5a6268, #6c757d);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3);
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
        /* Responsive */
        @media (max-width: 768px) {
            .main--content { padding: 25px; }
            .header--title h2 { font-size: 1.6rem; }
            .form-section { padding: 20px; }
        }
        @media (max-width: 480px) {
            .main--content { padding: 15px; }
            .header--wrapper { flex-direction: column; align-items: flex-start; }
            .user--info { margin-top: 15px; }
            .btn { padding: 10px 20px; font-size: 0.9rem; }
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
                <span>Primary</span>
                <h2>Modifier une leçon</h2>
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

        <div class="form-section">
            <h2>Modifier la leçon</h2>
            <?php if (isset($error)): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titre">Titre de la leçon</label>
                    <input type="text" id="titre" name="titre" class="form-control" value="<?php echo htmlspecialchars($lecon['titre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="format">Format</label>
                    <select id="format" name="format" class="form-control" required>
                        <option value="pdf" <?php echo $lecon['format'] === 'pdf' ? 'selected' : ''; ?>>PDF</option>
                        <option value="audio" <?php echo $lecon['format'] === 'audio' ? 'selected' : ''; ?>>Audio</option>
                        <option value="video" <?php echo $lecon['format'] === 'video' ? 'selected' : ''; ?>>Vidéo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fichier">Fichier</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="fichier" name="fichier" class="form-control">
                    </div>
                    <?php if ($lecon['fichier']): ?>
                        <div class="current-file">Fichier actuel : <?php echo htmlspecialchars($lecon['fichier']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="liste_cours.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Animations GSAP
        gsap.from(".header--wrapper", { opacity: 0, y: -60, duration: 1.2, ease: "elastic.out(1, 0.5)" });
        gsap.from(".form-section", { opacity: 0, y: 40, duration: 1, ease: "power3.out", delay: 0.3 });
        gsap.from(".form-group", { opacity: 0, y: 30, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.5 });
        gsap.from(".btn", { opacity: 0, scale: 0.7, duration: 0.6, stagger: 0.1, ease: "back.out(2)", delay: 0.7 });
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
    </script>
</body>
</html>