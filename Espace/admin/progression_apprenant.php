<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../authentification/connexion-admin.php");
    exit();
}

// Récupérer les apprenants actifs
$stmt = $pdo->prepare("
    SELECT u.id, u.nom, u.email
    FROM utilisateurs u
    WHERE u.role = 'apprenant' AND u.actif = 1
    ORDER BY u.nom ASC
");
$stmt->execute();
$apprenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque apprenant, récupérer ses cours, progression des cours et des quiz
foreach ($apprenants as &$apprenant) {
    $stmt = $pdo->prepare("
        SELECT c.id AS cours_id, c.titre AS cours_titre, i.date_inscription
        FROM inscriptions i
        JOIN cours c ON i.cours_id = c.id
        WHERE i.utilisateur_id = ? AND i.statut_paiement = 'paye'
    ");
    $stmt->execute([$apprenant['id']]);
    $apprenant['cours'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($apprenant['cours'] as &$cours) {
        // Progression des cours (modules complétés)
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total_modules FROM modules WHERE cours_id = ?");
        $stmt->execute([$cours['cours_id']]);
        $total_modules = $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS modules_completes
            FROM completions
            WHERE utilisateur_id = ? AND cours_id = ?
        ");
        $stmt->execute([$apprenant['id'], $cours['cours_id']]);
        $modules_completes = $stmt->fetchColumn();

        $cours['progression_cours'] = $total_modules > 0 ? round(($modules_completes / $total_modules) * 100) : 0;

        // Progression des quiz (quiz réussis)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total_quiz
            FROM quiz q
            JOIN modules m ON q.module_id = m.id
            WHERE m.cours_id = ?
        ");
        $stmt->execute([$cours['cours_id']]);
        $total_quiz = $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS quiz_reussis
            FROM resultats_quiz rq
            JOIN quiz q ON rq.quiz_id = q.id
            JOIN modules m ON q.module_id = m.id
            WHERE rq.utilisateur_id = ? AND m.cours_id = ? AND rq.score >= q.score_minimum
        ");
        $stmt->execute([$apprenant['id'], $cours['cours_id']]);
        $quiz_reussis = $stmt->fetchColumn();

        $cours['progression_quiz'] = $total_quiz > 0 ? round(($quiz_reussis / $total_quiz) * 100) : 0;
    }
}
unset($apprenant, $cours);

// Enregistrer l'activité dans journal_activite
$stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], 'Visualisation progression apprenants', 'Consultation de la page de progression', date('Y-m-d H:i:s')]);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Progression des Apprenants</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        .main--content {
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
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

        .table--wrapper {
            overflow-x: auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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

        .progress-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-bar-cours .progress {
            height: 10px;
            background: linear-gradient(45deg, #01ae8f, #008f75);
            border-radius: 5px;
            transition: width 0.3s ease;
        }

        .progress-bar-quiz .progress {
            height: 10px;
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            border-radius: 5px;
            transition: width 0.3s ease;
        }

        .no-data {
            text-align: center;
            font-size: 0.95rem;
            color: #4a5568;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .main--content {
                padding: 25px;
            }

            .table th, .table td {
                padding: 8px;
                font-size: 0.9rem;
            }

            .progress-bar span {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .main--content {
                padding: 15px;
            }

            .table th, .table td {
                padding: 6px;
                font-size: 0.85rem;
            }

            .progress-bar span {
                font-size: 0.8rem;
            }

            .header--title h2 {
                font-size: 1.2rem;
            }

            .header--title span {
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
            <li>
                <a href="gestion_forum.php"><i class="fas fa-comments"></i><span>Forum</span></a>
            </li>
            <li class="active">
                <a href="progression_apprenants.php"><i class="fas fa-chart-line"></i><span>Progression apprenants</span></a>
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
                <h2>Progression des Apprenants</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <img src="../asset/images/lito.jpg" alt="User Profile">
            </div>
        </div>

        <div class="table--wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Cours</th>
                        <th>Progression Cours</th>
                        <th>Progression Quiz</th>
                        <th>Date d'inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($apprenants)): ?>
                        <tr>
                            <td colspan="6" class="no-data">Aucun apprenant trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($apprenants as $apprenant): ?>
                            <?php if (empty($apprenant['cours'])): ?>
                                <tr class="table--row">
                                    <td><?php echo htmlspecialchars($apprenant['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($apprenant['email']); ?></td>
                                    <td>Aucun cours</td>
                                    <td>
                                        <div class="progress-bar progress-bar-cours">
                                            <div class="progress" style="width: 0%;"></div>
                                            <span>0%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress-bar progress-bar-quiz">
                                            <div class="progress" style="width: 0%;"></div>
                                            <span>0%</span>
                                        </div>
                                    </td>
                                    <td>-</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($apprenant['cours'] as $index => $cours): ?>
                                    <tr class="table--row">
                                        <?php if ($index === 0): ?>
                                            <td rowspan="<?php echo count($apprenant['cours']); ?>">
                                                <?php echo htmlspecialchars($apprenant['nom']); ?>
                                            </td>
                                            <td rowspan="<?php echo count($apprenant['cours']); ?>">
                                                <?php echo htmlspecialchars($apprenant['email']); ?>
                                            </td>
                                        <?php endif; ?>
                                        <td><?php echo htmlspecialchars($cours['cours_titre']); ?></td>
                                        <td>
                                            <div class="progress-bar progress-bar-cours">
                                                <div class="progress" style="width: <?php echo $cours['progression_cours']; ?>%;"></div>
                                                <span><?php echo $cours['progression_cours']; ?>%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress-bar progress-bar-quiz">
                                                <div class="progress" style="width: <?php echo $cours['progression_quiz']; ?>%;"></div>
                                                <span><?php echo $cours['progression_quiz']; ?>%</span>
                                            </div>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($cours['date_inscription'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Animations GSAP
        gsap.from(".header--wrapper", { 
            opacity: 0, 
            y: -20, 
            duration: 0.8, 
            ease: "power3.out" 
        });
        gsap.from(".table--wrapper", { 
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