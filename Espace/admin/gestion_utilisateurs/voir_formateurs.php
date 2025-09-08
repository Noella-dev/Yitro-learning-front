<?php
require_once '../../config/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM formateurs WHERE id = ?");
$stmt->execute([$id]);
$formateur = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning | Détails Formateur</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Formateur</span>
                <h2>Détails du Formateur</h2>
            </div>
            <a href="gestion_utilisateur.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        <div class="card--container">
            <div class="card--wrapper">
                <h3>Informations du Formateur</h3>
                <div class="info--grid">
                    <div class="info--item"><strong>Nom et Prénom :</strong> <?= htmlspecialchars($formateur['nom_prenom']) ?></div>
                    <div class="info--item"><strong>Email :</strong> <?= htmlspecialchars($formateur['email']) ?></div>
                    <div class="info--item"><strong>Téléphone :</strong> <?= htmlspecialchars($formateur['telephone']) ?></div>
                    <div class="info--item"><strong>Ville, Pays :</strong> <?= htmlspecialchars($formateur['ville_pays']) ?></div>
                    <div class="info--item"><strong>LinkedIn :</strong> <?= htmlspecialchars($formateur['linkedin']) ?></div>
                    <div class="info--item"><strong>Métier :</strong> <?= htmlspecialchars($formateur['intitule_metier']) ?></div>
                    <div class="info--item"><strong>Temps d'expérience :</strong> <?= htmlspecialchars($formateur['experience_formation']) ?></div>
                    <div class="info--item"><strong>Détail d'expérience :</strong> <?= htmlspecialchars($formateur['detail_experience']) ?></div>
                    <div class="info--item"><strong>CV :</strong> <?= htmlspecialchars($formateur['cv']) ?></div>
                    <div class="info--item"><strong>Catégories Étude :</strong> <?= htmlspecialchars($formateur['categories']) ?></div>
                    <div class="info--item"><strong>Autre Domaine :</strong> <?= htmlspecialchars($formateur['autre_domaine']) ?></div>
                    <div class="info--item"><strong>Titre du cours :</strong> <?= htmlspecialchars($formateur['titre_cours']) ?></div>
                    <div class="info--item"><strong>Objectif :</strong> <?= htmlspecialchars($formateur['objectif']) ?></div>
                    <div class="info--item"><strong>Public Cibles :</strong> <?= htmlspecialchars($formateur['public_cible']) ?></div>
                    <div class="info--item"><strong>Détail complémentaire :</strong> <?= htmlspecialchars($formateur['detail_complementaire']) ?></div>
                    <div class="info--item"><strong>Formats :</strong> <?= htmlspecialchars($formateur['formats']) ?></div>
                    <div class="info--item"><strong>Autre Formats :</strong> <?= htmlspecialchars($formateur['format_autre']) ?></div>
                    <div class="info--item"><strong>Durée estimée :</strong> <?= htmlspecialchars($formateur['duree_estimee']) ?></div>
                    <div class="info--item"><strong>Type de formation :</strong> <?= htmlspecialchars($formateur['type_formation']) ?></div>
                    <div class="info--item"><strong>Motivation :</strong> <?= htmlspecialchars($formateur['motivation']) ?></div>
                    <div class="info--item"><strong>Valeurs :</strong> <?= htmlspecialchars($formateur['valeurs']) ?></div>
                    <div class="info--item"><strong>Profil public :</strong> <?= htmlspecialchars($formateur['profil_public']) ?></div>
                    <div class="info--item"><strong>Statut :</strong> <?= htmlspecialchars($formateur['statut']) ?></div>
                    <div class="info--item"><strong>Créé le :</strong> <?= htmlspecialchars($formateur['created_at']) ?></div>
                    <div class="info--item"><strong>Code d'entrée :</strong> <?= htmlspecialchars($formateur['code_entree']) ?></div>
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