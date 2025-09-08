<?php
require_once 'Espace/config/db.php';

if (!isset($_GET['id'])) {
    die("Cours non spécifié.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ?");
$stmt->execute([$id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    die("Cours introuvable.");
}

// Récupérer les modules
$stmtModules = $pdo->prepare("SELECT * FROM modules WHERE cours_id = ?");
$stmtModules->execute([$id]);
$modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($cours['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1 class="mb-3"><?= htmlspecialchars($cours['titre']) ?></h1>
    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($cours['description'])) ?></p>
    <p><strong>Prix :</strong> <?= htmlspecialchars($cours['prix']) ?> MGA</p>

    <hr>

    <h3>Modules</h3>
    <?php foreach ($modules as $module): ?>
        <div class="card mb-3">
            <div class="card-header">
                <strong><?= htmlspecialchars($module['titre']) ?></strong>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($module['description'])) ?></p>

                <?php
                $stmtLecons = $pdo->prepare("SELECT * FROM lecons WHERE module_id = ?");
                $stmtLecons->execute([$module['id']]);
                $lecons = $stmtLecons->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <h5>Leçons</h5>
                <ul>
                    <?php foreach ($lecons as $lecon): ?>
                        <li>
                            <strong><?= htmlspecialchars($lecon['titre']) ?></strong> -
                            Format : <?= htmlspecialchars($lecon['format']) ?> -
                            <a href="<?= htmlspecialchars($lecon['fichier']) ?>" target="_blank">Voir</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
