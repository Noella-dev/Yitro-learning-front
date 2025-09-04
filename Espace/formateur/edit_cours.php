<?php
require_once '../config/db.php';
session_start();

// Vérifier si le formateur est connecté
if (!isset($_SESSION['formateur_id'])) {
    header("Location: ../../authentification/login.php");
    exit;
}

$formateur_id = $_SESSION['formateur_id'];

// Récupérer le nom du formateur
$trainer_name = "Formateur";
if (isset($pdo)) {
    try {
        $query = "SELECT nom_prenom FROM formateurs WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $formateur_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $trainer_name = htmlspecialchars($row['nom_prenom']);
        }
    } catch (PDOException $e) {
        error_log("Erreur de requête : " . $e->getMessage());
    }
}

// Vérifier si l'ID du cours est fourni
if (!isset($_GET['id'])) {
    die("Cours introuvable.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ? AND formateur_id = ?");
$stmt->execute([$id, $formateur_id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    die("Cours non trouvé ou vous n'avez pas l'autorisation de le modifier.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $photo = $cours['photo'];
    $upload_dir = dirname(__FILE__) . '/../../Uploads/cours/';
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5MB

    try {
        // Créer le dossier d'upload s'il n'existe pas
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("Impossible de créer le dossier : $upload_dir");
            }
        }

        // Vérifier si le dossier est accessible en écriture
        if (!is_writable($upload_dir)) {
            throw new Exception("Le dossier $upload_dir n'est pas accessible en écriture.");
        }

        // Gérer l'upload de la nouvelle photo
        if (isset($_FILES['photo_cours']) && $_FILES['photo_cours']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo_cours'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_type = $file['type'];

            // Vérifier le type et la taille
            if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $photo = 'course_' . time() . '.' . $extension;
                $dest = $upload_dir . $photo;

                // Supprimer l'ancienne photo si elle existe
                if ($cours['photo'] && file_exists($upload_dir . $cours['photo'])) {
                    if (!unlink($upload_dir . $cours['photo'])) {
                        throw new Exception("Impossible de supprimer l'ancienne photo.");
                    }
                }

                // Uploader la nouvelle photo
                if (!move_uploaded_file($file_tmp, $dest)) {
                    throw new Exception("Échec de l'upload de la photo vers $dest.");
                }
            } else {
                throw new Exception("Type de fichier non autorisé ou taille excessive.");
            }
        }

        // Mettre à jour le cours
        $stmt = $pdo->prepare("UPDATE cours SET titre = ?, description = ?, prix = ?, photo = ? WHERE id = ?");
        $stmt->execute([$titre, $description, $prix, $photo, $id]);

        header("Location: liste_cours.php?success=Cours mis à jour avec succès");
        exit;
    } catch (Exception $e) {
        $error = "Erreur : " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Modifier un cours</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        .main--content {
            padding: 40px;
            background: #f5f7fa;
            min-height: 100vh;
        }
        h2 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
            font-family: 'Poppins', sans-serif;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 12px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        .form-control:focus {
            border-color: #01ae8f;
            box-shadow: 0 0 8px rgba(1, 174, 143, 0.2);
            outline: none;
        }
        .form-control[type="file"] {
            padding: 8px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s, background-color 0.3s;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }
        .btn-success {
            background-color: #2ecc71;
            border: none;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
            display: block;
        }
        .course-image {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            display: block;
        }
        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 10px;
        }
        .user--info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .trainer-name {
            font-size: 0.9em;
            font-weight: 600;
            color: #2c3e50;
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
                <h2>Modifier un cours</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <i class="fas fa-users"></i>
                <span class="trainer-name"><?php echo $trainer_name; ?></span>
            </div>
        </div>
        <h2>Modifier le cours : <?php echo htmlspecialchars($cours['titre']); ?></h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="edit_cours.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titre">Titre du cours</label>
                <input type="text" name="titre" id="titre" class="form-control" value="<?php echo htmlspecialchars($cours['titre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description du cours</label>
                <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($cours['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="prix">Prix du cours (€)</label>
                <input type="number" name="prix" id="prix" class="form-control" step="0.01" value="<?php echo htmlspecialchars($cours['prix']); ?>" required>
            </div>
            <div class="form-group">
                <label for="photo_cours">Photo du cours (jpg, jpeg, png)</label>
                <?php if ($cours['photo']): ?>
                    <img src="../../Uploads/cours/<?php echo htmlspecialchars($cours['photo']); ?>" alt="Photo du cours" class="course-image">
                <?php endif; ?>
                <input type="file" name="photo_cours" id="photo_cours" class="form-control" accept="image/jpeg,image/jpg,image/png">
            </div>
            <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
            <a href="liste_cours.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script>
        gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
        gsap.from(".form-group", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
        gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)", delay: 0.5 });
        gsap.from(".course-image", { opacity: 0, scale: 0.9, duration: 0.7, ease: "power2.out", delay: 0.3 });
    </script>
</body>
</html>