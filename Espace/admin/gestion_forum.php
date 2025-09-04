
<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../authentification/connexion-admin.php");
    exit();
}

// Traitement de la suppression d'un forum
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $forum_id = (int)$_POST['forum_id'];
    $stmt = $pdo->prepare("DELETE FROM forum WHERE id = ?");
    $stmt->execute([$forum_id]);

    // Enregistrer l'activité dans journal_activite
    $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], 'Suppression de forum', "Forum ID: $forum_id"]);
    header("Location: gestion_forum.php");
    exit();
}

// Traitement de la modification d'un forum
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $forum_id = (int)$_POST['forum_id'];
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);

    $stmt = $pdo->prepare("UPDATE forum SET titre = ?, description = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $forum_id]);

    // Enregistrer l'activité dans journal_activite
    $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], 'Modification de forum', "Forum ID: $forum_id"]);
    header("Location: gestion_forum.php");
    exit();
}

// Récupérer tous les forums avec les informations du cours
$stmt = $pdo->prepare("
    SELECT f.*, c.titre AS cours_titre
    FROM forum f
    JOIN cours c ON f.cours_id = c.id
    ORDER BY f.date_creation DESC
");
$stmt->execute();
$forums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Gestion des Forums</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        .main--content {
            padding: 20px;
        }
        .header--wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .header--title h2 {
            color: #01ae8f;
            font-weight: 600;
            margin: 0;
        }
        .header--title span {
            color: #777;
            font-size: 0.9rem;
        }
        .forum--container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .forum--container h3 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }
        .table--wrapper {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .table th {
            background: #f9f9f9;
            color: #333;
            font-weight: 600;
        }
        .table td {
            color: #555;
        }
        .btn-action {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-right: 5px;
            transition: background 0.3s;
        }
        .btn-edit {
            background: #9b8227;
            color: #fff;
        }
        .btn-edit:hover {
            background: #e68c32;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .btn-view {
            background: #01ae8f;
            color: #fff;
        }
        .btn-view:hover {
            background: #028f76;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: min(90%, 500px);
            position: relative;
        }
        .modal-content h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .modal-content label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
        }
        .modal-content textarea {
            resize: vertical;
            min-height: 100px;
        }
        .modal-content button {
            background: #01ae8f;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .modal-content button:hover {
            background: #028f76;
        }
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .main--content {
                padding: 10px;
            }
            .table th, .table td {
                padding: 8px;
                font-size: 0.9rem;
            }
            .btn-action {
                padding: 6px 8px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li>
                <a href="backoffice.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            </li>
            <li>
                <a href="gestion_utilisateurs/gestion_utilisateur.php"><i class="fas fa-user-cog"></i><span>Gestion utilisateur</span></a>
            </li>
            <li class="active">
                <a href="gestion_forum.php"><i class="fas fa-comments"></i><span>Forum</span></a>
            </li>
            <li>
                <a href="progression_apprenant.php"><i class="fas fa-chart-line"></i><span>Progression Apprenant</span></a>
            </li>
            <li>
                <a href="espace-certificat.php"><i class="fas fa-certificate"></i><span>Certificat Apprenant</span></a>
            </li>
            <li class="logout">
                <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </li>
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Administration</span>
                <h2>Gestion des Forums</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <img src="../asset/images/lito.jpg" alt="User Profile">
            </div>
        </div>

        <div class="forum--container">
            <h3>Liste des Forums</h3>
            <div class="table--wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Cours</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($forums)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">Aucun forum disponible.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($forums as $forum): ?>
                                <tr class="table--row">
                                    <td><?php echo htmlspecialchars($forum['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($forum['cours_titre']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($forum['date_creation'])); ?></td>
                                    <td>
                                        <a href="#" class="btn-action btn-edit" onclick="openEditModal(<?php echo $forum['id']; ?>, '<?php echo htmlspecialchars(addslashes($forum['titre'])); ?>', '<?php echo htmlspecialchars(addslashes($forum['description'])); ?>')">Modifier</a>
                                        <form action="gestion_forum.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="forum_id" value="<?php echo $forum['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer ce forum ?')">Supprimer</button>
                                        </form>
                                        <a href="voir_messages.php?forum_id=<?php echo $forum['id']; ?>" class="btn-action btn-view"><i class="fas fa-eye"></i> Voir les messages</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier un forum -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">×</span>
            <h3>Modifier le Forum</h3>
            <form action="gestion_forum.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="forum_id" id="edit_forum_id">
                <label for="edit_titre">Titre</label>
                <input type="text" name="titre" id="edit_titre" required>
                <label for="edit_description">Description</label>
                <textarea name="description" id="edit_description"></textarea>
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </div>

    <script>
        // Fonctions pour gérer le modal
        function openEditModal(id, titre, description) {
            document.getElementById('edit_forum_id').value = id;
            document.getElementById('edit_titre').value = titre;
            document.getElementById('edit_description').value = description;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        // Animations GSAP
        gsap.from(".header--wrapper", { 
            opacity: 0, 
            y: -20, 
            duration: 0.8, 
            ease: "power3.out" 
        });
        gsap.from(".forum--container", { 
            opacity: 0, 
            y: 30, 
            duration: 0.8, 
            ease: "power3.out",
            delay: 0.2 
        });
        gsap.from(".table--row", { 
            opacity: 0, 
            x: -20, 
            duration: 0.8, 
            stagger: 0.05, 
            ease: "power2.out",
            delay: 0.4 
        });
    </script>
</body>
</html>
