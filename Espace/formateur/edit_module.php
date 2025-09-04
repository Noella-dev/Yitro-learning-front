<?php
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
      <li>
        <a href="progression_apprenants.php"><i class="fas fa-chart-line"></i><span>Progression des apprenants</span></a>
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
</html>