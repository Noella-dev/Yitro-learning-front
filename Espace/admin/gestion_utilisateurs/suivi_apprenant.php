<?php
require_once '../../config/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ? AND role = 'apprenant'");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: gestion_utilisateur.php");
    exit;
}

// Simuler la progression (à remplacer par une vraie table de progression)
$progression = [
    ['cours' => 'Introduction à PHP', 'progression' => '75%', 'certificat' => 'Non obtenu'],
    ['cours' => 'JavaScript Avancé', 'progression' => '20%', 'certificat' => 'Non obtenu']
];

// Simuler l'activité
$activite = [
    ['date' => '2025-05-01', 'action' => 'Inscription au cours PHP'],
    ['date' => '2025-05-03', 'action' => 'Complétion du module 1']
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning | Suivi Apprenant</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Apprenant</span>
                <h2>Suivi de <?= htmlspecialchars($user['nom']) ?></h2>
            </div>
            <a href="gestion_utilisateur.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        <div class="card--container">
            <div class="card--wrapper">
                <h3>Progression des Cours</h3>
                <div class="table--wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cours</th>
                                <th>Progression</th>
                                <th>Certificat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($progression as $prog): ?>
                                <tr class="table--row">
                                    <td><?= htmlspecialchars($prog['cours']) ?></td>
                                    <td><?= htmlspecialchars($prog['progression']) ?></td>
                                    <td><?= htmlspecialchars($prog['certificat']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card--wrapper">
                <h3>Activité Récente</h3>
                <div class="table--wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activite as $act): ?>
                                <tr class="table--row">
                                    <td><?= htmlspecialchars($act['date']) ?></td>
                                    <td><?= htmlspecialchars($act['action']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        gsap.from(".card--wrapper", { 
            opacity: 0, 
            y: 50, 
            duration: 1, 
            stagger: 0.2, 
            ease: "power3.out" 
        });
        gsap.from(".table--row", { 
            opacity: 0, 
            x: -20, 
            duration: 0.8, 
            stagger: 0.05, 
            ease: "power2.out" 
        });
    </script>
</body>
</html>