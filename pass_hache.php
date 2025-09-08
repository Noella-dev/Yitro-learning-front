<?php
echo password_hash('emmmanuelitorandria@gmail.com', PASSWORD_DEFAULT);
?>




j'ai un dossier nommé Espace et dedans il y a un sous dossier nommée formateur et dedans il y a des fichiers : create_cours.php "<?php
session_start();
require_once '../config/db.php';

// Fetch trainer's name
$trainer_name = "Formateur"; // Default fallback
if (!isset($pdo)) {
    die("Erreur : Connexion à la base de données non établie.");
}
if (isset($_SESSION['formateur_id'])) {
    $formateur_id = $_SESSION['formateur_id'];
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yitro Learning - Créer un cours</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    .main--content { padding: 30px; }
    h2 { color: #333; font-weight: 600; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 8px;
      padding: 12px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
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
    }
    .btn-primary { background-color: #007bff; border: none; }
    .btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }
    .btn-secondary { background-color: #6c757d; border: none; }
    .btn-secondary:hover { background-color: #5a6268; transform: translateY(-2px); }
    .btn-outline-info { border-color: #17a2b8; color: #17a2b8; }
    .btn-outline-info:hover { background-color: #17a2b8; color: #fff; transform: translateY(-2px); }
    .module-container {
      border: 1px solid #e9ecef;
      padding: 20px;
      margin-top: 20px;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    .lesson-container {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }
    label { font-weight: 500; color: #555; margin-bottom: 8px; display: block; }
    a { text-decoration: none; color: #007bff; transition: color 0.3s, transform 0.2s; }
    a:hover { color: #0056b3; transform: translateY(-2px); }
    .error { color: #dc3545; font-size: 0.9em; }
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
      <li class="active">
        <a href="#"><i class="fas fa-user-cog"></i><span>Créer un cours</span></a>
      </li>
      <li>
        <a href="liste_cours.php"><i class="fas fa-folder-open"></i><span>Mes cours</span></a>
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
        <h2>Créer un cours</h2>
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
    <h2>Créer un nouveau cours</h2>

    <form action="submit_cours.php" method="POST" enctype="multipart/form-data" id="courseForm">
      <div class="form-group">
        <label for="titre_cours">Titre du cours</label>
        <input type="text" name="titre_cours" id="titre_cours" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="description_cours">Description du cours</label>
        <textarea name="description_cours" id="description_cours" class="form-control" rows="3" required></textarea>
      </div>
      <div class="form-group">
        <label for="prix_cours">Prix du cours (€)</label>
        <input type="number" name="prix_cours" id="prix_cours" class="form-control" step="0.01" required>
      </div>
      <div class="form-group">
        <label for="photo_cours">Photo du cours (jpg, jpeg, png)</label>
        <input type="file" name="photo_cours" id="photo_cours" class="form-control" accept="image/jpeg,image/jpg,image/png">
      </div>

      <div id="modules-container"></div>

      <button type="button" class="btn btn-secondary mt-3" onclick="ajouterModule()">+ Ajouter un module</button>
      <button type="submit" class="btn btn-primary mt-3">Enregistrer le cours</button>
    </form>
  </div>

  <script>
    let moduleIndex = 0;

    function ajouterModule() {
      const modulesContainer = document.getElementById('modules-container');
      const moduleHTML = `
        <div class="module-container" id="module-${moduleIndex}">
          <h5>Module ${moduleIndex + 1}</h5>
          <div class="form-group">
            <label>Titre du module</label>
            <input type="text" name="modules[${moduleIndex}][titre]" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Description du module</label>
            <textarea name="modules[${moduleIndex}][description]" class="form-control" rows="2" required></textarea>
          </div>
          <div class="lessons-container" id="lessons-${moduleIndex}"></div>
          <button type="button" class="btn btn-outline-info btn-sm mt-2" onclick="ajouterLecon(${moduleIndex})">+ Ajouter une leçon</button>
        </div>
      `;
      modulesContainer.insertAdjacentHTML('beforeend', moduleHTML);

      gsap.from(`#module-${moduleIndex}`, {
        opacity: 0,
        y: 20,
        duration: 0.5,
        ease: "power2.out"
      });

      moduleIndex++;
    }

    function ajouterLecon(indexModule) {
      const lessonsContainer = document.getElementById(`lessons-${indexModule}`);
      const leconIndex = lessonsContainer.querySelectorAll('.lesson-container').length;
      const leconHTML = `
        <div class="lesson-container">
          <h6>Leçon ${leconIndex + 1}</h6>
          <div class="form-group">
            <label>Titre de la leçon</label>
            <input type="text" name="modules[${indexModule}][lecons][${leconIndex}][titre]" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Format de la leçon</label>
            <select name="modules[${indexModule}][lecons][${leconIndex}][format]" class="form-control" required>
              <option value="pdf">PDF</option>
              <option value="audio">Audio</option>
              <option value="video">Vidéo</option>
            </select>
          </div>
          <div class="form-group">
            <label>Fichier de la leçon</label>
            <input type="file" name="modules[${indexModule}][lecons][${leconIndex}][fichier]" class="form-control" required>
          </div>
        </div>
      `;
      lessonsContainer.insertAdjacentHTML('beforeend', leconHTML);

      gsap.from(lessonsContainer.lastElementChild, {
        opacity: 0,
        x: 20,
        duration: 0.5,
        ease: "power2.out"
      });
    }

    // Validation du formulaire
    document.getElementById('courseForm').addEventListener('submit', function(e) {
      const titre = document.getElementById('titre_cours').value.trim();
      const description = document.getElementById('description_cours').value.trim();
      const prix = document.getElementById('prix_cours').value.trim();

      if (!titre || !description || !prix) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires : Titre, Description, Prix.');
      }
    });

    gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
    gsap.from(".form-group", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
    gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)", delay: 0.5 });
  </script>
</body>
</html>"  delete_cours.php "<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID cours manquant.");
}

$id = $_GET['id'];

// Supprimer les leçons liées aux modules de ce cours
$stmtLecons = $pdo->prepare("
    DELETE lecons FROM lecons 
    INNER JOIN modules ON lecons.module_id = modules.id 
    WHERE modules.cours_id = ?
");
$stmtLecons->execute([$id]);

// Supprimer les modules
$stmtModules = $pdo->prepare("DELETE FROM modules WHERE cours_id = ?");
$stmtModules->execute([$id]);

// Supprimer le cours
$stmt = $pdo->prepare("DELETE FROM cours WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cours.php");
exit;
" ; il a aussi delete_lecon.php "<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID leçon manquant.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM lecons WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cours.php");
exit;
" ; et aussi delete_module.php "<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID module manquant.");
}

$id = $_GET['id'];

// Supprimer les leçons liées au module
$stmtLecons = $pdo->prepare("DELETE FROM lecons WHERE module_id = ?");
$stmtLecons->execute([$id]);

// Supprimer le module
$stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cours.php");
exit;
" ; edit_cours.php "<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("Cours introuvable.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ?");
$stmt->execute([$id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    die("Cours non trouvé.");
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

        header("Location: liste_cours.php");
        exit;
    } catch (Exception $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage());
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
    .main--content { padding: 30px; }
    h2 { color: #333; font-weight: 600; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 8px;
      padding: 12px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
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
    }
    .btn-success { background-color: #28a745; border: none; }
    .btn-success:hover { background-color: #218838; transform: translateY(-2px); }
    .btn-secondary { background-color: #6c757d; border: none; }
    .btn-secondary:hover { background-color: #5a6268; transform: translateY(-2px); }
    label { font-weight: 500; color: #555; margin-bottom: 8px; display: block; }
    a { text-decoration: none; color: #007bff; transition: color 0.3s, transform 0.2s; }
    a:hover { color: #0056b3; transform: translateY(-2px); }
    .course-image {
      max-width: 200px;
      height: auto;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      margin-bottom: 15px;
      display: block;
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
        <img src="../asset/images/lito.jpg" alt="User Profile">
      </div>
    </div>
    <h2>Modifier le cours</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>Titre du cours</label>
        <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($cours['titre']) ?>" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($cours['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Prix</label>
        <input type="number" name="prix" class="form-control" value="<?= $cours['prix'] ?>" step="0.01" required>
      </div>
      <div class="form-group">
        <label>Photo actuelle du cours</label>
        <?php if ($cours['photo']): ?>
          <img src="../../Uploads/cours/<?= htmlspecialchars($cours['photo']) ?>" alt="Photo du cours" class="course-image">
        <?php else: ?>
          <p>Aucune photo disponible.</p>
        <?php endif; ?>
      </div>
      <div class="form-group">
        <label for="photo_cours">Nouvelle photo du cours (jpg, jpeg, png)</label>
        <input type="file" name="photo_cours" id="photo_cours" class="form-control" accept="image/jpeg,image/jpg,image/png">
      </div>
      <button type="submit" class="btn btn-success">Enregistrer</button>
      <a href="liste_cours.php" class="btn btn-secondary">Annuler</a>
    </form>
  </div>

  <script>
    gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
    gsap.from(".form-group", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
    gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)", delay: 0.5 });
    gsap.from(".course-image", { opacity: 0, scale: 0.9, duration: 0.6, ease: "power2.out", delay: 0.3 });
  </script>
</body>
</html>" ; edit_lecon.php "<?php
require_once '../config/db.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $format = $_POST['format'];
    $fichier = $_POST['fichier'];
    $stmt = $pdo->prepare("UPDATE lecons SET titre = ?, format = ?, fichier = ? WHERE id = ?");
    $stmt->execute([$titre, $format, $fichier, $id]);
    header("Location: liste_cours.php");
    exit;
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    .main--content { padding: 30px; }
    h2 { color: #333; font-weight: 600; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 8px;
      padding: 12px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
      outline: none;
    }
    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: transform 0.2s, background-color 0.3s;
      text-decoration: none;
    }
    .btn-success { background-color: #28a745; border: none; }
    .btn-success:hover { background-color: #218838; transform: translateY(-2px); }
    .btn-secondary { background-color: #6c757d; border: none; }
    .btn-secondary:hover { background-color: #5a6268; transform: translateY(-2px); }
    label { font-weight: 500; color: #555; margin-bottom: 8px; display: block; }
    a { text-decoration: none; color: #007bff; transition: color 0.3s, transform 0.2s; }
    a:hover { color: #0056b3; transform: translateY(-2px); }
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
        <img src="../asset/images/lito.jpg" alt="User Profile">
      </div>
    </div>
    <h2>Modifier la leçon</h2>
    <form method="post">
      <div class="form-group">
        <label>Titre de la leçon</label>
        <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($lecon['titre']) ?>" required>
      </div>
      <div class="form-group">
        <label>Format</label>
        <select name="format" class="form-control" required>
          <option value="pdf" <?= $lecon['format'] === 'pdf' ? 'selected' : '' ?>>PDF</option>
          <option value="audio" <?= $lecon['format'] === 'audio' ? 'selected' : '' ?>>Audio</option>
          <option value="video" <?= $lecon['format'] === 'video' ? 'selected' : '' ?>>Vidéo</option>
        </select>
      </div>
      <div class="form-group">
        <label>Fichier</label>
        <input type="text" name="fichier" class="form-control" value="<?= htmlspecialchars($lecon['fichier']) ?>">
      </div>
      <button type="submit" class="btn btn-success">Enregistrer</button>
      <a href="liste_cours.php" class="btn btn-secondary">Annuler</a>
    </form>
  </div>

  <script>
    gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
    gsap.from(".form-group", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
    gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)", delay: 0.5 });
  </script>
</body>
</html>" ; edit_module_.php "<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("Module introuvable.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->execute([$id]);
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$module) {
    die("Module non trouvé.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $stmt = $pdo->prepare("UPDATE modules SET titre = ?, description = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $id]);
    header("Location: liste_cours.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yitro Learning - Modifier un module</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    .main--content { padding: 30px; }
    h2 { color: #333; font-weight: 600; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 8px;
      padding: 12px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
      outline: none;
    }
    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: transform 0.2s, background-color 0.3s;
      text-decoration: none;
    }
    .btn-success { background-color: #28a745; border: none; }
    .btn-success:hover { background-color: #218838; transform: translateY(-2px); }
    .btn-secondary { background-color: #6c757d; border: none; }
    .btn-secondary:hover { background-color: #5a6268; transform: translateY(-2px); }
    label { font-weight: 500; color: #555; margin-bottom: 8px; display: block; }
    a { text-decoration: none; color: #007bff; transition: color 0.3s, transform 0.2s; }
    a:hover { color: #0056b3; transform: translateY(-2px); }
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
      <li class="logout">
        <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
      </li>
    </ul>
  </div>
  <div class="main--content">
    <div class="header--wrapper">
      <div class="header--title">
        <span>Primary</span>
        <h2>Modifier un module</h2>
      </div>
      <div class="user--info">
        <div class="search--box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Rechercher...">
        </div>
        <img src="../asset/images/lito.jpg" alt="User Profile">
      </div>
    </div>
    <h2>Modifier le module</h2>
    <form method="post">
      <div class="form-group">
        <label>Titre du module</label>
        <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($module['titre']) ?>" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($module['description']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-success">Enregistrer</button>
      <a href="liste_cours.php" class="btn btn-secondary">Annuler</a>
    </form>
  </div>

  <script>
    gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
    gsap.from(".form-group", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
    gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)", delay: 0.5 });
  </script>
</body>
</html>" ; espace_formateur.php "<?php
session_start();
require_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yitro Learning - Tableau de bord formateur</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    .main--content { padding: 30px; }
    .dashboard-welcome {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      text-align: center;
      margin-top: 20px;
    }
    .dashboard-welcome h2 { color: #333; font-weight: 600; }
    .dashboard-welcome p { color: #555; font-size: 1.1em; margin: 10px 0; }
    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: transform 0.2s, background-color 0.3s;
    }
    .btn-primary { background-color: #007bff; border: none; }
    .btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo"></div>
    <ul class="menu">
      <li class="active">
        <a href="#"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
      </li>
      <li>
        <a href="create_cours.php"><i class="fas fa-user-cog"></i><span>Créer un cours</span></a>
      </li>
      <li>
        <a href="liste_cours.php"><i class="fas fa-folder-open"></i><span>Mes cours</span></a>
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
        <h2>Tableau de bord</h2>
      </div>
      <div class="user--info">
        <div class="search--box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Rechercher...">
        </div>
        <img src="../asset/images/lito.jpg" alt="User Profile">
      </div>
    </div>
    
     
</body>
</html>" ; liste_cours.php "<?php
session_start();
require_once '../config/db.php';

// Fetch trainer's name
$trainer_name = "Formateur"; // Default fallback
if (!isset($pdo)) {
    die("Erreur : Connexion à la base de données non établie.");
}
if (isset($_SESSION['formateur_id'])) {
    $formateur_id = $_SESSION['formateur_id'];
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

$stmt = $pdo->prepare("SELECT * FROM cours WHERE formateur_id = ?");
$stmt->execute([$formateur_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yitro Learning - Mes cours</title>
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
    h1 { 
      color: #2d3748; 
      font-weight: 700; 
      font-size: 2rem; 
      margin-bottom: 40px; 
      font-family: 'Segoe UI', Tahoma, sans-serif; 
    }
    .card {
      border: none;
      border-radius: 16px;
      background: linear-gradient(145deg, #ffffff, #f9fbfc);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover { 
      transform: translateY(-8px); 
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12); 
    }
    .card-body { 
      padding: 30px; 
    }
    .card-title { 
      color: #2d3748; 
      font-weight: 600; 
      font-size: 1.5rem; 
      margin-bottom: 15px; 
    }
    .card-text { 
      color: #4a5568; 
      font-size: 1rem; 
      line-height: 1.6; 
      margin-bottom: 20px; 
    }
    .badge {
      padding: 10px 16px;
      border-radius: 20px;
      font-size: 0.95rem;
      font-weight: 500;
      background: linear-gradient(90deg, #3b82f6, #60a5fa);
      color: #fff;
    }
    .btn {
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 500;
      font-size: 0.95rem;
      transition: transform 0.2s, background-color 0.3s, box-shadow 0.3s;
      text-decoration: none;
      margin-right: 10px;
      margin-bottom: 12px;
    }
    .btn-warning { 
      background-color: #f59e0b; 
      border: none; 
      color: #fff; 
    }
    .btn-warning:hover { 
      background-color: #d97706; 
      transform: translateY(-3px); 
      box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); 
    }
    .btn-danger { 
      background-color: #ef4444; 
      border: none; 
      color: #fff; 
    }
    .btn-danger:hover { 
      background-color: #dc2626; 
      transform: translateY(-3px); 
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); 
    }
    .btn-outline-warning { 
      border: 2px solid #f59e0b; 
      color: #f59e0b; 
      background: transparent; 
    }
    .btn-outline-warning:hover { 
      background-color: #f59e0b; 
      color: #fff; 
      transform: translateY(-3px); 
      box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); 
    }
    .btn-outline-danger { 
      border: 2px solid #ef4444; 
      color: #ef4444; 
      background: transparent; 
    }
    .btn-outline-danger:hover { 
      background-color: #ef4444; 
      color: #fff; 
      transform: translateY(-3px); 
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); 
    }
    .btn-info { 
      background-color: #06b6d4; 
      border: none; 
      color: #fff; 
    }
    .btn-info:hover { 
      background-color: #0891b2; 
      transform: translateY(-3px); 
      box-shadow: 0 4px 10px rgba(6, 182, 212, 0.3); 
    }
    .list-group {
      margin-top: 20px;
      margin-bottom: 10px;
    }
    .list-group-item {
      border: none;
      border-radius: 12px;
      background: #f1f5f9;
      margin-bottom: 15px;
      padding: 20px;
      transition: background-color 0.3s;
    }
    .list-group-item:hover { 
      background: #e2e8f0; 
    }
    .list-group-item .btn { 
      margin-left: 15px; 
    }
    .text-muted { 
      color: #6b7280 !important; 
      font-size: 0.9rem; 
    }
    a { 
      text-decoration: none; 
      color: #3b82f6; 
      transition: color 0.3s, transform 0.2s; 
    }
    a:hover { 
      color: #2563eb; 
      transform: translateY(-3px); 
    }
    hr { 
      margin: 25px 0; 
      border-color: #e5e7eb; 
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
    .no-courses {
      text-align: center;
      color: #4a5568;
      font-size: 1.2rem;
      font-weight: 500;
      padding: 30px;
      background: linear-gradient(145deg, #ffffff, #f9fbfc);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
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
        <i class="fas fa-users"></i>
        <span class="trainer-name"><?php echo $trainer_name; ?></span>
      </div>
    </div>
    <h1>Mes cours</h1>

    <?php if (empty($cours)): ?>
      <div class="no-courses">
        Aucun cours disponible. Créez votre premier cours dès maintenant !
      </div>
    <?php else: ?>
      <?php foreach ($cours as $c): ?>
        <div class="card">
          <div class="card-body">
            <?php if ($c['photo']): ?>
              <img src="../../Uploads/cours/<?= htmlspecialchars($c['photo']) ?>" alt="Photo du cours" class="course-image">
            <?php else: ?>
              <img src="../assets/images/default_course.jpg" alt="Photo par défaut" class="course-image">
            <?php endif; ?>
            <h4 class="card-title">
              <?= htmlspecialchars($c['titre']) ?> 
              <span class="badge"><?= number_format($c['prix'], 2) ?> €</span>
            </h4>
            <p class="card-text"><?= nl2br(htmlspecialchars($c['description'])) ?></p>
            <a href="edit_cours.php?id=<?= $c['id'] ?>" class="btn btn-warning">Modifier</a>
            <a href="delete_cours.php?id=<?= $c['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a>
            <hr>
            <?php
            $stmtModules = $pdo->prepare("SELECT * FROM modules WHERE cours_id = ?");
            $stmtModules->execute([$c['id']]);
            $modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php foreach ($modules as $m): ?>
              <div class="card mb-2 ms-3">
                <div class="card-body">
                  <h5 class="card-title">Module : <?= htmlspecialchars($m['titre']) ?></h5>
                  <p class="card-text"><?= nl2br(htmlspecialchars($m['description'])) ?></p>
                  <a href="edit_module.php?id=<?= $m['id'] ?>" class="btn btn-outline-warning">Modifier</a>
                  <a href="delete_module.php?id=<?= $m['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer ce module ?')">Supprimer</a>
                  <?php
                  $stmtLecons = $pdo->prepare("SELECT * FROM lecons WHERE module_id = ?");
                  $stmtLecons->execute([$m['id']]);
                  $lecons = $stmtLecons->fetchAll(PDO::FETCH_ASSOC);
                  ?>
                  <ul class="list-group list-group-flush">
                    <?php foreach ($lecons as $l): ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($l['titre']) ?> 
                        <span>
                          <small class="text-muted">(<?= strtoupper($l['format']) ?>)</small> |
                          <a href="../../Uploads/lecons/<?= $l['fichier'] ?>" target="_blank" class="btn btn-info">Voir</a>
                          <a href="edit_lecon.php?id=<?= $l['id'] ?>" class="btn btn-outline-warning">Modifier</a>
                          <a href="delete_lecon.php?id=<?= $l['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer cette leçon ?')">Supprimer</a>
                        </span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script>
    gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
    gsap.from(".card, .no-courses", { opacity: 0, y: 30, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
    gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.05, ease: "back.out(1.7)", delay: 0.5 });
    gsap.from(".course-image", { opacity: 0, scale: 0.9, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.3 });
  </script>
</body>
</html>" ; submit_cours.php "<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['formateur_id'])) {
    header("Location: ../../authentification/login.php");
    exit;
}

$formateur_id = $_SESSION['formateur_id'];
$upload_dir = '../../Uploads/cours/';
$lecon_upload_dir = '../../Uploads/lecons/';
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
$allowed_lecon_types = ['application/pdf', 'audio/mpeg', 'video/mp4'];
$max_size = 5 * 1024 * 1024; // 5MB

try {
    // Créer les dossiers d'upload s'ils n'existent pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!is_dir($lecon_upload_dir)) {
        mkdir($lecon_upload_dir, 0755, true);
    }

    // Vérifier les champs principaux
    if (!isset($_POST['titre_cours']) || !isset($_POST['description_cours']) || !isset($_POST['prix_cours'])) {
        throw new Exception("Tous les champs obligatoires doivent être remplis : Titre, Description, Prix.");
    }

    $titre = $_POST['titre_cours'];
    $description = $_POST['description_cours'];
    $prix = $_POST['prix_cours'];
    $photo = null;

    // Gérer l'upload de la photo
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

            if (!move_uploaded_file($file_tmp, $dest)) {
                throw new Exception("Échec de l'upload de la photo.");
            }
        } else {
            throw new Exception("Type de fichier non autorisé ou taille excessive pour la photo.");
        }
    }

    // Insérer le cours
    $stmt = $pdo->prepare("INSERT INTO cours (formateur_id, titre, description, prix, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$formateur_id, $titre, $description, $prix, $photo]);
    $cours_id = $pdo->lastInsertId();

    // Gérer les modules et leçons
    if (isset($_POST['modules']) && is_array($_POST['modules'])) {
        foreach ($_POST['modules'] as $module_index => $module) {
            if (!isset($module['titre']) || !isset($module['description'])) {
                continue; // Ignorer les modules incomplets
            }

            $module_titre = $module['titre'];
            $module_description = $module['description'];

            // Insérer le module
            $stmt = $pdo->prepare("INSERT INTO modules (cours_id, titre, description) VALUES (?, ?, ?)");
            $stmt->execute([$cours_id, $module_titre, $module_description]);
            $module_id = $pdo->lastInsertId();

            // Gérer les leçons
            if (isset($module['lecons']) && is_array($module['lecons'])) {
                foreach ($module['lecons'] as $lecon_index => $lecon) {
                    if (!isset($lecon['titre']) || !isset($lecon['format']) || !isset($_FILES['modules']['name'][$module_index]['lecons'][$lecon_index]['fichier'])) {
                        continue; // Ignorer les leçons incomplètes
                    }

                    $lecon_titre = $lecon['titre'];
                    $lecon_format = $lecon['format'];
                    $lecon_file = $_FILES['modules']['name'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_tmp = $_FILES['modules']['tmp_name'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_size = $_FILES['modules']['size'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_type = $_FILES['modules']['type'][$module_index]['lecons'][$lecon_index]['fichier'];

                    if ($lecon_file && $lecon_tmp && $lecon_size <= $max_size && in_array($lecon_type, $allowed_lecon_types)) {
                        $lecon_extension = pathinfo($lecon_file, PATHINFO_EXTENSION);
                        $lecon_filename = 'lecon_' . $cours_id . '_' . $module_id . '_' . time() . '.' . $lecon_extension;
                        $lecon_dest = $lecon_upload_dir . $lecon_filename;

                        if (move_uploaded_file($lecon_tmp, $lecon_dest)) {
                            $stmt = $pdo->prepare("INSERT INTO lecons (module_id, titre, format, fichier) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$module_id, $lecon_titre, $lecon_format, $lecon_filename]);
                        } else {
                            throw new Exception("Échec de l'upload du fichier de la leçon.");
                        }
                    }
                }
            }
        }
    }

    header("Location: liste_cours.php");
    exit;
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>" et il y a aussi un ficher dans Espace/config/db.php "<?php
$host = 'localhost';
$db   = 'yitro_learning';
$user = 'root';
$pass = ''; // ou ton mot de passe

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
" et aussi Espace/assets/css/styles.css "@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap");

* {
  margin: 0;
  padding: 0;
  border: none;
  outline: none;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

body {
  display: flex;
}

.sidebar {
  position: sticky;
  top: 0;
  left: 0;
  bottom: 0;
  width: 110px;
  height: 100vh;
  padding: 0 1.7rem;
  color: #fff;
  overflow: hidden;
  transition: all 0.5s linear;
  background: linear-gradient(109deg, #132F3F, #01ae8f);
}

.sidebar:hover {
  width: 240px;
  transition: 0.5s;
}

.logo {
  height: 80px;
  padding: 16px;
}

.menu {
  height: 88%;
  position: relative;
  list-style: none;
  padding: 0;
}

.menu li {
  padding: 1rem;
  margin: 8px 0;
  border-radius: 8px;
  transition: all 0.5s ease-in-out;
}

.menu li:hover,
.active {
  background-color: #e0e0e058;
}

.menu a {
  color: #fff;
  font-size: 14px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.menu a span {
  overflow: hidden;
}

.menu a i {
  font-size: 1.2rem;
}

.logout {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
}

.main--content {
  position: relative;
  background: #ebe9e9;
  width: 100%;
  padding: 1rem;
}

.header--wrapper img {
  width: 50px;
  height: 50px;
  cursor: pointer;
  border-radius: 50%;
}

.header--wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 1rem;
  padding: 10px 2rem;
  background-color: #fff;
}

.header--title {
  color: rgba(113, 99, 186, 255);
}

.user--info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search--box {
  background: rgb(237, 237, 237);
  border-radius: 15px;
  color: rgb(113, 99, 186, 255);
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 4px 12px;
}

.search--box input {
  background: transparent;
  padding: 10px;
}

.search--box i {
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.5s ease-out;
}

.search--box i:hover {
  color: #01ae8f;
  transform: scale(1.2);
}

/* Styles pour la gestion des utilisateurs */
.user-management {
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.tab {
  padding: 10px 20px;
  border-radius: 20px;
  background: #e0e0e0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab.active {
  background: #01ae8f;
  color: #fff;
}

.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.filters input,
.filters select,
.filters button {
  padding: 8px 12px;
  border-radius: 5px;
  border: 1px solid #ddd;
}

.filters button {
  background: #01ae8f;
  color: #fff;
  cursor: pointer;
  transition: background 0.3s;
}

.filters button:hover {
  background: #028f76;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th,
.user-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.user-table th {
  background: #f4f4f4;
}

.user-table td button {
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
  margin-right: 5px;
}

.user-table td button.view {
  background: #01ae8f;
  color: #fff;
}

.user-table td button.toggle {
  background: #ff9800;
  color: #fff;
}

.user-table td button.delete {
  background: #f44336;
  color: #fff;
}

/* Styles pour la modale */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  width: 80%;
  max-width: 800px;
  max-height: 80vh;
  overflow-y: auto;
  position: relative;
}

.close-modal {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
  color: #333;
}

.modal-body h3 {
  margin-top: 1.5rem;
  color: #01ae8f;
}

.modal-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.modal-table th,
.modal-table td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.modal-table th {
  background: #f4f4f4;
}

#toggle-statut {
  margin-top: 1rem;
  padding: 8px 16px;
  background: #ff9800;
  color: #fff;
  border-radius: 5px;
  cursor: pointer;
}

#toggle-statut:hover {
  background: #e68900;
}". et j'ai deja une base de donnée "-- Création de la base de données
CREATE DATABASE IF NOT EXISTS yitro_learning
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Utilisation de la base de données
USE yitro_learning;

-- Création de la table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    photo VARCHAR(255),
    pays VARCHAR(100),
    langue VARCHAR(100),
    autre_langue VARCHAR(100),
    objectifs TEXT,
    type_cours VARCHAR(100),
    niveau_formation VARCHAR(100),
    niveau_etude VARCHAR(100),
    acces_internet VARCHAR(50),
    appareil VARCHAR(100),
    accessibilite TEXT,
    rgpd TINYINT(1) DEFAULT 0,
    charte TINYINT(1) DEFAULT 0,
    role ENUM('apprenant', 'admin', 'moderator') DEFAULT 'apprenant',
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formateurs
CREATE TABLE IF NOT EXISTS formateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    ville_pays VARCHAR(255),
    linkedin VARCHAR(255),
    intitule_metier VARCHAR(255),
    experience_formation TEXT,
    detail_experience TEXT,
    cv VARCHAR(255),
    categories TEXT,
    autre_domaine VARCHAR(255),
    titre_cours VARCHAR(255),
    objectif TEXT,
    public_cible TEXT,
    detail_complementaire TEXT,
    formats TEXT,
    format_autre VARCHAR(255),
    duree_estimee VARCHAR(100),
    type_formation VARCHAR(100),
    motivation TEXT,
    valeurs TEXT,
    profil_public TEXT,
    statut ENUM('en_attente', 'verifie', 'premium', 'partenaire') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajout d'un administrateur par défaut (mot de passe : "admin123")
INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif)
VALUES (
    'Admin',
    'litomanto2@gmail.com',
    '$2y$10$mvmghyfCcp5ziWaLnzy.BuCq0bmE5B.gttWfz1X///O0NknyhT6gq', 
    'admin',
    1
);

-- Table des cours
CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formateur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formateur_id) REFERENCES formateurs(id) ON DELETE CASCADE
);

-- Table des modules
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);

-- Table des leçons
CREATE TABLE IF NOT EXISTS lecons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    format ENUM('pdf', 'audio', 'video') NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS journal_activite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE formateurs ADD code_entree VARCHAR(255) NULL;


ALTER TABLE formateurs ADD password VARCHAR(255) DEFAULT NULL;

ALTER TABLE cours ADD photo VARCHAR(255) NULL AFTER prix;

-- Ajout de la table contact
CREATE TABLE IF NOT EXISTS contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table forum
CREATE TABLE IF NOT EXISTS forum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table post
CREATE TABLE IF NOT EXISTS post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id INT NOT NULL,
    forum_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_post TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (forum_id) REFERENCES forum(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;" et il y a cette probleme "Erreur : Type de fichier non autorisé ou taille excessive pour la photo." dans "http://localhost/YitroLearning/Espace/formateur/submit_cours.php" quand je fais rentrer une leçon de format video  