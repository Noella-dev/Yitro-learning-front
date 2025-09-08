<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté et est apprenant
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'apprenant') {
    header("Location: ../../authentification/connexion.php");
    exit();
}

// Récupérer les certificats de l'apprenant
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT c.fichier, c.titre_certificat, c.date_emission, cours.titre AS cours_titre
    FROM certificats c
    JOIN cours ON c.cours_id = cours.id
    WHERE c.utilisateur_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$certificats = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Mes Certificats</title>
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

        .card--wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .certificat-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .certificat-table th, .certificat-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .certificat-table th {
            background: #01ae8f;
            color: #fff;
            font-weight: 500;
        }

        .certificat-table td {
            color: #333;
        }

        .certificat-table a {
            color: #01ae8f;
            text-decoration: none;
            font-weight: 500;
        }

        .certificat-table a:hover {
            text-decoration: underline;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .main--content {
                padding: 25px;
            }

            .card--wrapper {
                padding: 15px;
            }

            .certificat-table th, .certificat-table td {
                font-size: 0.9rem;
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            .main--content {
                padding: 15px;
            }

            .card--wrapper {
                padding: 10px;
            }

            .certificat-table th, .certificat-table td {
                font-size: 0.85rem;
                padding: 6px;
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
                <a href="espace-apprenant.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            </li>
            <li>
                <a href="mes-cours.php"><i class="fas fa-book"></i><span>Mes cours</span></a>
            </li>
            <li>
                <a href="forum-apprenant.php"><i class="fas fa-comments"></i><span>Forum</span></a>
            </li>
            <li class="active">
                <a href="espace-apprenant-certificats.php"><i class="fas fa-certificate"></i><span>Mes certificats</span></a>
            </li>
            <li>
                <a href="parametres.php"><i class="fas fa-cog"></i><span>Paramètres</span></a>
            </li>
            <li class="logout">
                <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </li>
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Espace Apprenant</span>
                <h2>Mes Certificats</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <img src="../asset/images/lito.jpg" alt="User Profile">
            </div>
        </div>

        <div class="card--wrapper">
            <h3>Mes Certificats</h3>
            <?php if (empty($certificats)): ?>
                <div class="message">Aucun certificat disponible pour le moment.</div>
            <?php else: ?>
                <table class="certificat-table">
                    <thead>
                        <tr>
                            <th>Cours</th>
                            <th>Titre du Certificat</th>
                            <th>Date d'Émission</th>
                            <th>Télécharger</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificats as $certificat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($certificat['cours_titre']); ?></td>
                                <td><?php echo htmlspecialchars($certificat['titre_certificat']); ?></td>
                                <td><?php echo htmlspecialchars($certificat['date_emission']); ?></td>
                                <td><a href="../admin/<?php echo htmlspecialchars($certificat['fichier']); ?>" download>Télécharger</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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
        gsap.from(".card--wrapper", { 
            opacity: 0, 
            y: 30, 
            duration: 0.8, 
            ease: "power3.out",
            delay: 0.2 
        });
    </script>
</body>
</html>