
<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion-admin.php");
    exit();
}

// Initialiser les messages de confirmation/erreur
$success_message = '';
$error_message = '';

// Traitement des actions (marquer notification comme lue, désactiver utilisateur)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_notification_read'])) {
        $_SESSION['notifications_read'] = true;
        $success_message = "Notifications marquées comme lues.";
        // Journaliser l'action
        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], 'Marquer notifications comme lues', 'Notifications des dernières 24 heures']);
    } elseif (isset($_POST['deactivate_user_id'])) {
        $user_id = (int)$_POST['deactivate_user_id'];
        try {
            // Vérifier si la colonne 'active' existe
            $stmt = $pdo->prepare("SHOW COLUMNS FROM utilisateurs LIKE 'active'");
            $stmt->execute();
            $has_active_column = $stmt->rowCount() > 0;

            if ($has_active_column) {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET active = 0 WHERE id = ?");
                $stmt->execute([$user_id]);
                $success_message = "Compte désactivé avec succès.";
                // Journaliser l'action
                $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], 'Désactivation de compte', "Utilisateur ID: $user_id"]);
            } else {
                $error_message = "La colonne 'active' n'existe pas dans la table 'utilisateurs'. Ajoutez-la pour utiliser cette fonctionnalité.";
            }
        } catch (Exception $e) {
            $error_message = "Erreur lors de la désactivation du compte : " . $e->getMessage();
        }
    }
}

// Récupérer les notifications (nouveaux apprenants, formateurs, cours des dernières 24 heures)
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM utilisateurs WHERE role = 'apprenant' AND created_at >= NOW() - INTERVAL 1 DAY");
$stmt->execute();
$new_apprenants = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM formateurs WHERE created_at >= NOW() - INTERVAL 1 DAY");
$stmt->execute();
$new_formateurs = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours WHERE created_at >= NOW() - INTERVAL 1 DAY");
$stmt->execute();
$new_cours = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$has_notifications = ($new_apprenants > 0 || $new_formateurs > 0 || $new_cours > 0) && !isset($_SESSION['notifications_read']);

// Récupérer les utilisateurs inactifs (pas de message depuis 30 jours, fallback sur created_at)
$inactive_users = [];
try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.nom, u.email, u.role, MAX(COALESCE(p.date_post, u.created_at)) as last_activity
        FROM utilisateurs u
        LEFT JOIN post p ON u.id = p.auteur_id
        GROUP BY u.id
        HAVING last_activity < NOW() - INTERVAL 30 DAY
        ORDER BY last_activity ASC
        LIMIT 5
    ");
    $stmt->execute();
    $inactive_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des utilisateurs inactifs : " . $e->getMessage();
}

// Récupérer les formateurs pour vérifier les rôles
$stmt = $pdo->query("SELECT id FROM formateurs");
$formateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les statistiques
$stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs WHERE role = 'apprenant'");
$apprenants_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM formateurs");
$formateurs_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM cours");
$cours_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM journal_activite WHERE DATE(created_at) = CURDATE()");
$activites_aujourdhui = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Récupérer les 5 dernières activités
$stmt = $pdo->prepare("SELECT j.*, u.nom FROM journal_activite j JOIN utilisateurs u ON j.admin_id = u.id ORDER BY j.created_at DESC LIMIT 5");
$stmt->execute();
$dernieres_activites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Données pour les graphiques (inscriptions par mois)
$inscriptions = [];
for ($i = 5; $i >= 0; $i--) {
    $mois = date('Y-m', strtotime("-$i months"));
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM utilisateurs WHERE role = 'apprenant' AND DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$mois]);
    $inscriptions[$mois] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
$labels_inscriptions = array_keys($inscriptions);
$data_inscriptions = array_values($inscriptions);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Tableau de Bord Admin</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
        .stats--container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat--card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            transition: transform 0.3s;
        }
        .stat--card:hover {
            transform: translateY(-5px);
        }
        .stat--icon {
            font-size: 2rem;
            color: #01ae8f;
            margin-right: 15px;
        }
        .stat--info h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }
        .stat--info p {
            margin: 0;
            color: #777;
            font-size: 0.9rem;
        }
        .chart--container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .chart--container h3 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }
        .activites--container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .activites--container h3 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }
        .notifications--container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .notifications--container h3 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notification {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 10px;
            animation: pulse 2s infinite;
        }
        .notification i {
            color: #01ae8f;
            margin-right: 10px;
        }
        .notification p {
            margin: 0;
            color: #555;
            font-size: 0.95rem;
        }
        .btn-mark-read {
            background: #9b8227;
            color: #fff;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        .btn-mark-read:hover {
            background: #e68c32;
        }
        .inactive-users--container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .inactive-users--container h3 {
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
            transition: background 0.3s;
        }
        .btn-view {
            background: #01ae8f;
            color: #fff;
        }
        .btn-view:hover {
            background: #028f76;
        }
        .btn-deactivate {
            background: #dc3545;
            color: #fff;
        }
        .btn-deactivate:hover {
            background: #c82333;
        }
        .btn-remind {
            background: #6c757d;
            color: #fff;
        }
        .btn-remind:hover {
            background: #5a6268;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @media (max-width: 768px) {
            .main--content {
                padding: 10px;
            }
            .stats--container {
                grid-template-columns: 1fr;
            }
            .notifications--container,
            .inactive-users--container {
                padding: 15px;
            }
        }
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
                <a href="gestion_utilisateurs/gestion_utilisateur.php"><i class="fas fa-user-cog"></i><span>Gestion utilisateur</span></a>
            </li>
            <li>
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
                <h2>Tableau de Bord</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <img src="../asset/images/lito.jpg" alt="User Profile">
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Notifications -->
        <?php if ($has_notifications): ?>
            <div class="notifications--container">
                <h3>Notifications Récentes
                    <form action="" method="POST" style="display:inline;">
                        <button type="submit" name="mark_notification_read" class="btn-mark-read">Marquer comme lues</button>
                    </form>
                </h3>
                <?php if ($new_apprenants > 0): ?>
                    <div class="notification">
                        <i class="fas fa-users"></i>
                        <p><?= $new_apprenants ?> nouvel<?= $new_apprenants > 1 ? 's' : '' ?> apprenant<?= $new_apprenants > 1 ? 's' : '' ?> ajouté<?= $new_apprenants > 1 ? 's' : '' ?> dans les dernières 24 heures.</p>
                    </div>
                <?php endif; ?>
                <?php if ($new_formateurs > 0): ?>
                    <div class="notification">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p><?= $new_formateurs ?> nouvel<?= $new_formateurs > 1 ? 's' : '' ?> formateur<?= $new_formateurs > 1 ? 's' : '' ?> ajouté<?= $new_formateurs > 1 ? 's' : '' ?> dans les dernières 24 heures.</p>
                    </div>
                <?php endif; ?>
                <?php if ($new_cours > 0): ?>
                    <div class="notification">
                        <i class="fas fa-book-open"></i>
                        <p><?= $new_cours ?> nouveau<?= $new_cours > 1 ? 'x' : '' ?> cours ajouté<?= $new_cours > 1 ? 's' : '' ?> dans les dernières 24 heures.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats--container">
            <div class="stat--card">
                <div class="stat--icon"><i class="fas fa-users"></i></div>
                <div class="stat--info">
                    <h3><?= $apprenants_count ?></h3>
                    <p>Apprenants</p>
                </div>
            </div>
            <div class="stat--card">
                <div class="stat--icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat--info">
                    <h3><?= $formateurs_count ?></h3>
                    <p>Formateurs</p>
                </div>
            </div>
            <div class="stat--card">
                <div class="stat--icon"><i class="fas fa-book-open"></i></div>
                <div class="stat--info">
                    <h3><?= $cours_count ?></h3>
                    <p>Cours</p>
                </div>
            </div>
            <div class="stat--card">
                <div class="stat--icon"><i class="fas fa-tasks"></i></div>
                <div class="stat--info">
                    <h3><?= $activites_aujourdhui ?></h3>
                    <p>Activités aujourd'hui</p>
                </div>
            </div>
        </div>

        <!-- Utilisateurs inactifs -->
        <div class="inactive-users--container">
            <h3>Utilisateurs Inactifs (30 derniers jours)</h3>
            <div class="table--wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Dernière Activité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inactive_users)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Aucun utilisateur inactif.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inactive_users as $user): ?>
                                <tr class="table--row">
                                    <td><?= htmlspecialchars($user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($user['role'] === 'apprenant' && in_array($user['id'], array_column($formateurs, 'id')) ? 'formateur' : $user['role'])) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($user['last_activity']))) ?></td>
                                    <td>
                                        <a href="#" class="btn-action btn-remind">Envoyer un rappel</a>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="deactivate_user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn-action btn-deactivate" onclick="return confirm('Voulez-vous vraiment désactiver ce compte ?')">Désactiver</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Graphique des inscriptions -->
        <div class="chart--container">
            <h3>Inscriptions par Mois</h3>
            <canvas id="inscriptionsChart"></canvas>
        </div>

        <!-- Dernières activités -->
        <div class="activites--container">
            <h3>Dernières Activités</h3>
            <div class="table--wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Administrateur</th>
                            <th>Action</th>
                            <th>Détails</th>
                            <th>Voir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dernieres_activites)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Aucune activité récente.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dernieres_activites as $activite): ?>
                                <tr class="table--row">
                                    <td><?= htmlspecialchars($activite['created_at']) ?></td>
                                    <td><?= htmlspecialchars($activite['nom']) ?></td>
                                    <td><?= htmlspecialchars($activite['action']) ?></td>
                                    <td><?= htmlspecialchars($activite['details']) ?></td>
                                    <td>
                                        <a href="gestion_utilisateurs/journal_activite.php" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            // Initialisation du graphique Chart.js
            const ctx = document.getElementById('inscriptionsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($labels_inscriptions) ?>,
                    datasets: [{
                        label: 'Nouvelles Inscriptions',
                        data: <?= json_encode($data_inscriptions) ?>,
                        borderColor: '#01ae8f',
                        backgroundColor: 'rgba(1, 174, 143, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Animations GSAP
            gsap.from(".header--wrapper", { 
                opacity: 0, 
                y: -20, 
                duration: 0.8, 
                ease: "power3.out" 
            });
            gsap.from(".notifications--container", { 
                opacity: 0, 
                y: 30, 
                duration: 0.8, 
                ease: "power3.out",
                delay: 0.1 
            });
            gsap.from(".notification", { 
                opacity: 0, 
                x: -20, 
                duration: 0.8, 
                stagger: 0.1, 
                ease: "power2.out",
                delay: 0.2 
            });
            gsap.from(".stat--card", { 
                opacity: 0, 
                y: 30, 
                duration: 0.8, 
                stagger: 0.1, 
                ease: "power3.out",
                delay: 0.3 
            });
            gsap.from(".inactive-users--container", { 
                opacity: 0, 
                y: 30, 
                duration: 0.8, 
                ease: "power3.out",
                delay: 0.4 
            });
            gsap.from(".chart--container", { 
                opacity: 0, 
                y: 30, 
                duration: 0.8, 
                ease: "power3.out",
                delay: 0.5 
            });
            gsap.from(".activites--container", { 
                opacity: 0, 
                y: 30, 
                duration: 0.8, 
                ease: "power3.out",
                delay: 0.6 
            });
            gsap.from(".table--row", { 
                opacity: 0, 
                x: -20, 
                duration: 0.8, 
                stagger: 0.05, 
                ease: "power2.out",
                delay: 0.7 
            });
            gsap.from(".alert", { 
                opacity: 0, 
                y: 20, 
                duration: 0.8, 
                ease: "power3.out",
                delay: 0.0 
            });
        </script>
    </div>
</body>
</html>
<?php
// Fin du code PHP
?>
