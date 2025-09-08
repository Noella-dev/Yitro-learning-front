<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Apprentissage en Ligne</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="assets/images/Yitro consulting.png" type="image/png">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Header -->
    <?php include "resutilisable/header.php" ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <video autoplay muted loop playsinline class="hero-video">
                <source src="assets/images/607453_People_Middle_aged_3840x2160.mp4" type="video/mp4">
                Votre navigateur ne prend pas en charge les vidéos.
            </video>
            <div class="hero-content">
                <h1>Apprends. Progresse. Impacte.</h1>
                <p>La plateforme e-learning inclusive pour les talents formateurs.</p>
                <div class="hero-buttons">
                    <a href="page/catalogue.php" class="btn-primary">Explorer les cours</a>
                    <a href="page/devenir-formateur.php" class="btn-primary">Devenir formateur</a>
                    <a href="page/decouvrir-yitro.php" class="btn-primary">Découvrir Yitro</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Trainings Section -->
    <section class="featured-trainings">
        <div class="container">
            <h2 class="section-title">Formations en cours exclusifs</h2>
            <div class="featured-grid">
                <div class="training-card">
                    <div class="training-img">
                        <img src="assets/images/social-media-marketing-concept-marketing-with-applications.jpg" alt="Marketing Digital">
                    </div>
                    <div class="training-content">
                        <h3>Marketing Digital</h3>
                        <p>Apprenez à booster votre présence en ligne avec des stratégies digitales efficaces.</p>
                        <a href="#" class="btn-learn">En savoir plus de</a>
                    </div>
                </div>
                <div class="training-card">
                    <div class="training-img">
                        <img src="assets/images/front-view-woman-working-with-post-its.jpg" src="Développement personnel" alt="Développement Personnel">
                    </div>
                    <div class="training-content">
                        <h3>Développement personnel</h3>
                        <p>Apprenez à vous épanouir pleinement dans le développement personnel avec nos formations en ligne.</p>
                        <a href="#" class="btn-learn">En savoir plus sur</a>
                    </div>
                </div>
                <div class="training-card">
                    <div class="training-img">
                        <img src="assets/images/black-businessman-using-computer-laptop.jpg" alt="Finance & Investissement">
                    </div>
                    <div class="training-content">
                        <h3>Finance & Investissements</h3>
                        <p>Maîtriser les bases financières et découvrez comment faire fructifier votre argent avec des stratégies d’investissement simples et efficaces.</p>
                        <a href="#" class="btn-learn">En savoir plus sur</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-content1">
                    <h2>Pourquoi choisir Yitro Learning ?</h2>
                    <p>Yitro learning est plus qu’une plateforme d’e-learning. C’est une communauté d’apprentissage engagé pour la transformation sociale, la montée en compétences, et l'inclusion numérique. Nos formations sont conçues pour apprendre vite, retenir mieux, et agir plus rapidement.</p>
                </div>
            </div>
            <div class="categories">
                <div class="category-grid">
                    <div class="category-card">
                        <i class="fas fa-stopwatch"></i>
                        <p>Microlearning & vidéos courtes vidéos</p>
                    </div>
                    <div class="category-card">
                        <i class="fas fa-question-circle"></i>
                        <p>Quiz & activités pour progresser avec plaisir</p>
                    </div>
                    <div class="category-card">
                        <i class="fas fa-award"></i>
                        <p>Certifications reconnu reconnues</p>
                    </div>
                    <div class="category-card">
                        <i class="fas fa-comment"></i>
                        <p>Forums et entraide communautaires</p>
                    </div>
                    <div class="category-card">
                        <i class="fas fa-graduation-cap"></i>
                        <p>Formations gratuites ou premiums, accessibles à tous</p>
                    </div>
                    <div class="category-card">
                        <i class="fas fa-mobile-alt"></i>
                        <p>Accès mobile, hors ligne & multilingue</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section class="trainers-section">
        <div class="container">
            <h2>Partagez vos connaissances, inspirez des vies</h2>
            <p>Rejoignez notre réseau de formateurs, coachs, ONG et experts formateurs. Sur Yitro Learning, vous pouvez créer votre propre programme, diffuser vos formations, toucher un large public… et être rémunéré votre travail équitablement.</p>
            <a href="page/formulaire-formateur.php" class="btn-primary" href="#" aria-label="Devenir formateur sur Yitro Learning">Devenir formateur</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include "resutilisable/footer.php" ?>
</body>
</html>