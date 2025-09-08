<?php
require_once '../../config/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning | Détails Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Utilisateur</span>
                <h2>Détails de l'Utilisateur</h2>
            </div>
            <a href="gestion_utilisateur.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        <div class="card--container">
            <div class="card--wrapper">
                <h3>Informations de l'Utilisateur</h3>
                <div class="info--grid">
                    <div class="info--item"><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></div>
                    <div class="info--item"><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></div>
                    <div class="info--item"><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone']) ?></div>
                    <div class="info--item"><strong>Photo :</strong> <?= htmlspecialchars($user['photo']) ?></div>
                    <div class="info--item"><strong>Langue :</strong> <?= htmlspecialchars($user['langue']) ?></div>
                    <div class="info--item"><strong>Autre Langue :</strong> <?= htmlspecialchars($user['autre_langue']) ?></div>
                    <div class="info--item"><strong>Objectifs :</strong> <?= htmlspecialchars($user['objectifs']) ?></div>
                    <div class="info--item"><strong>Type de cours :</strong> <?= htmlspecialchars($user['type_cours']) ?></div>
                    <div class="info--item"><strong>Niveau de formation :</strong> <?= htmlspecialchars($user['niveau_formation']) ?></div>
                    <div class="info--item"><strong>Niveau d'étude :</strong> <?= htmlspecialchars($user['niveau_etude']) ?></div>
                    <div class="info--item"><strong>Accès Internet :</strong> <?= htmlspecialchars($user['acces_internet']) ?></div>
                    <div class="info--item"><strong>Appareil :</strong> <?= htmlspecialchars($user['appareil']) ?></div>
                    <div class="info--item"><strong>Accessibilité :</strong> <?= htmlspecialchars($user['accessibilite']) ?></div>
                    <div class="info--item"><strong>RGPD :</strong> <?= htmlspecialchars($user['rgpd']) ?></div>
                    <div class="info--item"><strong>Charte :</strong> <?= htmlspecialchars($user['charte']) ?></div>
                    <div class="info--item"><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></div>
                    <div class="info--item"><strong>Actif :</strong> <?= htmlspecialchars($user['actif']) ?></div>
                    <div class="info--item"><strong>Créé le :</strong> <?= htmlspecialchars($user['created_at']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Animation GSAP pour le conteneur de la carte
        gsap.from(".card--wrapper", { 
            opacity: 0, 
            y: 50, 
            duration: 1, 
            ease: "power3.out" 
        });
        // Animation pour les éléments d'information
        gsap.from(".info--item", { 
            opacity: 0, 
            y: 20, 
            duration: 0.8, 
            stagger: 0.05, 
            ease: "power2.out" 
        });
    </script>
</body>
</html>