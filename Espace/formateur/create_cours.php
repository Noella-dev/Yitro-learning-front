<?php
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
</html>