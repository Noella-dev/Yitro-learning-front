<?php
session_start();
require_once '../../config/db.php';

// Inclure PHPMailer manuellement
require_once '../../../vendor/PHPMailer/src/Exception.php';
require_once '../../../vendor/PHPMailer/src/PHPMailer.php';
require_once '../../../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Recherche et tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';

// Normaliser la colonne de tri pour les formateurs
$sort_column = $sort;
if ($sort == 'nom') {
    $sort_column_utilisateurs = 'nom';
    $sort_column_formateurs = 'nom_prenom';
} else {
    $sort_column_utilisateurs = $sort;
    $sort_column_formateurs = $sort;
}

// Requête pour les apprenants
$where = $search ? "WHERE role = 'apprenant' AND (nom LIKE ? OR email LIKE ?)" : "WHERE role = 'apprenant'";
$sql = "SELECT * FROM utilisateurs $where ORDER BY $sort_column_utilisateurs $order";
$stmtUser = $pdo->prepare($sql);
if ($search) {
    $searchTerm = "%$search%";
    $stmtUser->execute([$searchTerm, $searchTerm]);
} else {
    $stmtUser->execute();
}
$users = $stmtUser->fetchAll();

// Requête pour les formateurs
$where = $search ? "WHERE nom_prenom LIKE ? OR email LIKE ?" : "";
$sql = "SELECT * FROM formateurs $where ORDER BY $sort_column_formateurs $order";
$stmtFormtr = $pdo->prepare($sql);
if ($search) {
    $searchTerm = "%$search%";
    $stmtFormtr->execute([$searchTerm, $searchTerm]);
} else {
    $stmtFormtr->execute();
}
$formtrs = $stmtFormtr->fetchAll();

// Requête pour les administrateurs
$where = $search ? "WHERE role IN ('admin', 'moderator') AND (nom LIKE ? OR email LIKE ?)" : "WHERE role IN ('admin', 'moderator')";
$sql = "SELECT * FROM utilisateurs $where ORDER BY $sort_column_utilisateurs $order";
$stmtAdmin = $pdo->prepare($sql);
if ($search) {
    $searchTerm = "%$search%";
    $stmtAdmin->execute([$searchTerm, $searchTerm]);
} else {
    $stmtAdmin->execute();
}
$admins = $stmtAdmin->fetchAll();

// Export en CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=utilisateurs.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Type', 'ID', 'Nom', 'Email', 'Statut/Actif']);
    
    foreach ($users as $user) {
        fputcsv($output, ['Apprenant', $user['id'], $user['nom'], $user['email'], $user['actif'] ? 'Actif' : 'Inactif']);
    }
    foreach ($formtrs as $formtr) {
        fputcsv($output, ['Formateur', $formtr['id'], $formtr['nom_prenom'], $formtr['email'], $formtr['statut']]);
    }
    foreach ($admins as $admin) {
        fputcsv($output, ['Administrateur', $admin['id'], $admin['nom'], $admin['email'], $admin['actif'] ? 'Actif' : 'Inactif']);
    }
    fclose($output);
    exit;
}

// Activation/désactivation, changement de statut, suppression et envoi de code
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $admin_id = $_SESSION['user_id'];
    if ($_POST['action'] == 'toggle_active') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = !actif WHERE id = ?");
        $stmt->execute([$id]);
        // Journaliser l'action
        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$admin_id, 'Toggle actif utilisateur', "Utilisateur ID: $id"]);
    } elseif ($_POST['action'] == 'update_statut') {
        $id = $_POST['id'];
        $statut = $_POST['statut'];
        $stmt = $pdo->prepare("UPDATE formateurs SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
        // Journaliser l'action
        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$admin_id, 'Mise à jour statut formateur', "Formateur ID: $id, Statut: $statut"]);
    } elseif ($_POST['action'] == 'send_code') {
        $formateur_id = $_POST['id'];
        
        // Générer un code unique
        $code = bin2hex(random_bytes(8)); // Code aléatoire de 16 caractères

        // Récupérer l'email du formateur
        $stmt = $pdo->prepare("SELECT email FROM formateurs WHERE id = ?");
        $stmt->execute([$formateur_id]);
        $formateur = $stmt->fetch();

        if ($formateur) {
            // Mettre à jour le code dans la base de données
            $stmt = $pdo->prepare("UPDATE formateurs SET code_entree = ? WHERE id = ?");
            if ($stmt->execute([$code, $formateur_id])) {
                error_log("Code d'entrée mis à jour pour $formateur_id: $code");
            } else {
                $error_message = "Erreur lors de la mise à jour du code.";
                error_log("Échec mise à jour code pour $formateur_id: " . print_r($stmt->errorInfo(), true));
            }

            // Envoyer l'email avec PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configuration du serveur SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'emmanuelitorandria@gmail.com'; // Remplacez par votre adresse Gmail
                $mail->Password = 'iyzc tfvd hgnc ofjg'; // Remplacez par le mot de passe d'application
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Désactiver le débogage
                $mail->SMTPDebug = 0; // 0 = désactivé
                $mail->Debugoutput = 'html'; // Sortie par défaut (non utilisée si SMTPDebug = 0)

                // Destinataire
                $mail->setFrom('emmanuelitorandria@gmail.com', 'Yitro Learning');
                $mail->addAddress($formateur['email']);

                // Contenu de l'email
                $mail->isHTML(true);
                $mail->Subject = 'Votre code d\'inscription à Yitro Learning';
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd;'>
                        <img src='https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png' alt='Yitro Learning' style='max-width: 150px;'>
                        <h2>Votre code d'inscription</h2>
                        <p>Bonjour,</p>
                        <p>Voici votre code d'inscription pour devenir formateur sur Yitro Learning :</p>
                        <p style='font-size: 24px; font-weight: bold; color: #28a745;'>$code</p>
                        <p>Utilisez ce code pour finaliser votre inscription sur <a href='https://yitro-learning.com/Page/inscription-formateur.php' style='color: #007bff;'>notre plateforme</a>.</p>
                        <p>Merci de rejoindre notre communauté !</p>
                        <p style='color: #888;'>L'équipe Yitro Learning</p>
                    </div>
                ";
                $mail->AltBody = "Bonjour,\n\nVoici votre code d'inscription pour devenir formateur sur Yitro Learning : $code\n\nUtilisez ce code pour finaliser votre inscription sur https://yitro-learning.com/Page/inscription-formateur.php.\n\nMerci de rejoindre notre communauté !\nL'équipe Yitro Learning";

                $mail->send();
                $success_message = "Code envoyé avec succès à {$formateur['email']}.";
                
                // Journaliser l'action
                $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
                $stmt->execute([$admin_id, 'Envoi code formateur', "Formateur ID: $formateur_id, Email: {$formateur['email']}, Code: $code"]);
                error_log("Email envoyé à {$formateur['email']} avec code: $code");
            } catch (Exception $e) {
                $error_message = "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
                error_log("Erreur envoi email à {$formateur['email']}: {$mail->ErrorInfo}");
            }
        } else {
            $error_message = "Formateur non trouvé.";
            error_log("Formateur ID $formateur_id non trouvé");
        }
        header("Location: gestion_utilisateur.php");
        exit;
    }
    header("Location: gestion_utilisateur.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yitro Learning - Admin</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    .error-message {
        color: red;
        text-align: center;
        margin-bottom: 15px;
    }
    .success-message {
        color: green;
        text-align: center;
        margin-bottom: 15px;
    }
    .btn-send-code {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    .btn-send-code:hover {
        background-color: #218838;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo"></div>
    <ul class="menu">
      <li>
        <a href="../backoffice.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
      </li>
      <li class="active">
        <a href="#"><i class="fas fa-user-cog"></i><span>Gestion utilisateur</span></a>
      </li>
      <li>
        <a href="../gestion_forum.php"><i class="fas fa-comments"></i><span>Forum</span></a>
      </li>
            <li>
                <a href="../progression_apprenant.php"><i class="fas fa-chart-line"></i><span>Progression Apprenant</span></a>
            </li>
            <li>
                <a href="../espace-certificat.php"><i class="fas fa-certificate"></i><span>Certificat Apprenant</span></a>
            </li>
      <li class="logout">
        <a href="../../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
      </li>
    </ul>
  </div>
  <div class="main--content">
    <div class="header--wrapper">
      <div class="header--title">
        <span>Primary</span>
        <h2>Gestion des Utilisateurs</h2>
      </div>
      <div class="user--info">
        <div class="search--box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Rechercher...">
        </div>
        <img src="../asset/images/lito.jpg" alt="User Profile">
      </div>
    </div>

    <!-- Messages d'erreur ou de succès -->
    <?php if (isset($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message) ?></p>    
    <?php elseif (isset($success_message)): ?>
        <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <!-- Filtres et export -->
    <div class="filter--container">
        <form method="GET" class="search--form">
            <input type="text" name="search" placeholder="Rechercher par nom ou email" value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        <div class="sort--export">
            <select onchange="window.location.href='?sort='+this.value+'&order=<?= $order ?>'">
                <option value="id" <?= $sort == 'id' ? 'selected' : '' ?>>Trier par ID</option>
                <option value="nom" <?= $sort == 'nom' ? 'selected' : '' ?>>Trier par Nom/Prénom</option>
                <option value="email" <?= $sort == 'email' ? 'selected' : '' ?>>Trier par Email</option>
            </select>
            <a href="?export=csv" class="btn-action btn-export"><i class="fas fa-download"></i> Exporter CSV</a>
        </div>
    </div>

    <!-- Gestion Apprenants -->
    <div class="gest--container">
        <h2 class="gest--title">Gestion des Apprenants</h2>
        <div class="table--wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="gest-list-apprenants">
                    <?php foreach ($users as $user): ?>
                        <tr class="table--row">
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['actif'] ? 'Actif' : 'Inactif' ?></td>
                            <td>
                                <a href="voir_utilisateurs.php?id=<?= $user['id'] ?>" class="btn-action btn-view">Voir</a>
                                <a href="suivi_apprenant.php?id=<?= $user['id'] ?>" class="btn-action btn-track">Suivi</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-action btn-toggle"><?= $user['actif'] ? 'Désactiver' : 'Activer' ?></button>
                                </form>
                                <a href="supprimer_utilisateur.php?id=<?= $user['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer cet apprenant ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gestion Formateurs -->
    <div class="gest--container">
        <h2 class="gest--title">Gestion des Formateurs</h2>
        <div class="table--wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="gest-list-formateurs">
                    <?php foreach ($formtrs as $formtr): ?>
                        <tr class="table--row">
                            <td><?= $formtr['id'] ?></td>
                            <td><?= htmlspecialchars($formtr['nom_prenom']) ?></td>
                            <td><?= htmlspecialchars($formtr['email']) ?></td>
                            <td><?= htmlspecialchars($formtr['statut']) ?></td>
                            <td>
                                <a href="voir_formateurs.php?id=<?= $formtr['id'] ?>" class="btn-action btn-view">Voir</a>
                                <a href="controle_qualite.php?id=<?= $formtr['id'] ?>" class="btn-action btn-quality">Contrôle Qualité</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="update_statut">
                                    <input type="hidden" name="id" value="<?= $formtr['id'] ?>">
                                    <select name="statut" onchange="this.form.submit()">
                                        <option value="en_attente" <?= $formtr['statut'] == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                        <option value="verifie" <?= $formtr['statut'] == 'verifie' ? 'selected' : '' ?>>Vérifié</option>
                                        <option value="premium" <?= $formtr['statut'] == 'premium' ? 'selected' : '' ?>>Premium</option>
                                        <option value="partenaire" <?= $formtr['statut'] == 'partenaire' ? 'selected' : '' ?>>Partenaire</option>
                                    </select>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="send_code">
                                    <input type="hidden" name="id" value="<?= $formtr['id'] ?>">
                                    <button type="submit" class="btn-action btn-send-code">Envoyer le code</button>
                                </form>
                                <a href="supprimer_formateur.php?id=<?= $formtr['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer ce formateur ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gestion Administrateurs -->
    <div class="gest--container">
        <h2 class="gest--title">Gestion des Administrateurs</h2>
        <div class="table--wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="gest-list-admins">
                    <?php foreach ($admins as $admin): ?>
                        <tr class="table--row">
                            <td><?= $admin['id'] ?></td>
                            <td><?= htmlspecialchars($admin['nom']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= htmlspecialchars($admin['role']) ?></td>
                            <td><?= $admin['actif'] ? 'Actif' : 'Inactif' ?></td>
                            <td>
                                <a href="voir_utilisateurs.php?id=<?= $admin['id'] ?>" class="btn-action btn-view">Voir</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                    <button type="submit" class="btn-action btn-toggle"><?= $admin['actif'] ? 'Désactiver' : 'Activer' ?></button>
                                </form>
                                <a href="supprimer_utilisateur.php?id=<?= $admin['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer cet administrateur ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lien vers le journal d'activité -->
    <div class="gest--container">
        <h2 class="gest--title">Journal d'Activité</h2>
        <a href="journal_activite.php" class="btn-action btn-view">Voir le Journal d'Activité</a>
    </div>

    <script>
        // Animations GSAP pour les conteneurs
        gsap.from(".gest--container", {
            opacity: 0,
            y: 50,
            duration: 1,
            stagger: 0.2,
            ease: "power3.out"
        });

        // Animations pour les lignes de tableau
        gsap.from(".table--row", {
            opacity: 0,
            x: -20,
            duration: 0.8,
            stagger: 0.05,
            ease: "power2.out",
            delay: 0.5
        });

        // Animation pour le filtre
        gsap.from(".filter--container", {
            opacity: 0,
            y: 20,
            duration: 0.8,
            ease: "power2.out"
        });

        // Confirmation avant envoi du code
        document.querySelectorAll('.btn-send-code').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Voulez-vous envoyer un code d\'inscription à ce formateur ?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>