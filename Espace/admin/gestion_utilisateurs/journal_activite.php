<?php
session_start();
require_once '../../config/db.php';

// Recherche et tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';

// Construire la clause WHERE pour inclure le nom de l'administrateur et la date
$where = $search ? "WHERE j.action LIKE ? OR j.details LIKE ? OR u.nom LIKE ? OR j.created_at LIKE ?" : "";
$sql = "SELECT j.*, u.nom FROM journal_activite j JOIN utilisateurs u ON j.admin_id = u.id $where ORDER BY " . ($sort == 'nom' ? 'u.nom' : 'j.' . $sort) . " $order";
$stmt = $pdo->prepare($sql);
if ($search) {
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt->execute();
}
$activites = $stmt->fetchAll();

// Suppression d'une entrée du journal (sans journalisation)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_activite') {
    $id = $_POST['id'];
    
    // Supprimer l'entrée
    $stmt = $pdo->prepare("DELETE FROM journal_activite WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: journal_activite.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning | Journal d'Activité</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Administration</span>
                <h2>Journal d'Activité</h2>
            </div>
            <a href="gestion_utilisateur.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        
        <!-- Filtres et recherche -->
        <div class="filter--container">
            <form method="GET" class="search--form">
                <input type="text" name="search" placeholder="Rechercher par action, détails, administrateur ou date (ex: 2025-05-27)" value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="sort--export">
                <select onchange="window.location.href='?sort='+this.value+'&order=<?= $order ?>'">
                    <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Trier par Date</option>
                    <option value="action" <?= $sort == 'action' ? 'selected' : '' ?>>Trier par Action</option>
                    <option value="nom" <?= $sort == 'nom' ? 'selected' : '' ?>>Trier par Administrateur</option>
                </select>
                <a href="?order=<?= $order == 'ASC' ? 'desc' : 'asc' ?>&sort=<?= $sort ?>" class="btn-action btn-toggle">
                    <i class="fas fa-sort-<?= $order == 'ASC' ? 'up' : 'down' ?>"></i> <?= $order == 'ASC' ? 'Ascendant' : 'Descendant' ?>
                </a>
            </div>
        </div>

        <div class="card--container">
            <div class="card--wrapper">
                <h3>Activités Récentes</h3>
                <div class="table--wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Administrateur</th>
                                <th>Action</th>
                                <th>Détails</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activites as $act): ?>
                                <tr class="table--row">
                                    <td><?= htmlspecialchars($act['created_at']) ?></td>
                                    <td><?= htmlspecialchars($act['nom']) ?></td>
                                    <td><?= htmlspecialchars($act['action']) ?></td>
                                    <td><?= htmlspecialchars($act['details']) ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete_activite">
                                            <input type="hidden" name="id" value="<?= $act['id'] ?>">
                                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Supprimer cette entrée du journal ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Animations GSAP pour les conteneurs
        gsap.from(".card--wrapper", { 
            opacity: 0, 
            y: 50, 
            duration: 1, 
            ease: "power3.out" 
        });
        // Animations pour les lignes de tableau
        gsap.from(".table--row", { 
            opacity: 0, 
            x: -20, 
            duration: 0.8, 
            stagger: 0.05, 
            ease: "power2.out" 
        });
        // Animation pour le filtre
        gsap.from(".filter--container", {
            opacity: 0,
            y: 20,
            duration: 0.8,
            ease: "power2.out"
        });
    </script>
</body>
</html>