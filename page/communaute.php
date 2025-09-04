<?php
session_start();

   
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communauté - Yitro Learning</title>
    <link rel="stylesheet" href="../asset/css/styles.css">
    <link rel="stylesheet" href="../asset/css/communaute.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../asset/images/Yitro_consulting.png">
</head>
<body>
   <!-- ==================== NAVIGATION ==================== -->
   <nav class="navbar">
        <div class="container">
            <!-- Logo du site -->
            <div class="logo">
                <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                <a href="#" class="logo">Yitro Learning</a>
            </div>


            <!-- Bouton menu mobile (visible seulement sur petits écrans) -->
            <div class="menu-toggle" id="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- Liste de navigation principale -->
            <ul class="nav-list">
                <li><a href="#">Communautés</a></li>
    
                <?php
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'formateur') {
            echo '<a href="espace-formateur.php">  ⬅ Mon espace</a>';
        } elseif ($_SESSION['role'] == 'apprenant') {
            echo '<a href="espace-apprenant.php">⬅Mon espace</a>';
        }elseif ($_SESSION['role'] == 'admin') {
            echo '<a href="backoffice.php">⬅ Mon espace</a>';
        }
    } else {
        echo '<a href="index.php">⬅ Retour à l\'accueil</a>';
    }
    ?>
                <li><a href="../authentification/logout.php" class="btn-primary">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <!-- ==================== SECTION 1 : FORUM D’ENTRAIDE ==================== -->
    <section class="forum-section">
        <div class="container">
            <h2>Forum d’entraide</h2>
            <p>Une interface simple, moderne et intuitive, accessible à tous les utilisateurs enregistrés.</p>
            <div class="forum-search">
                <input type="text" placeholder="Recherche par mot-clé (ex. “certificat”, “leadership”…)" aria-label="Rechercher dans le forum">
                <button type="submit" aria-label="Lancer la recherche"><i class="fas fa-search"></i></button>
            </div>
            <div class="forum-categories">
                <h3>Catégories</h3>
                <ul>
                    <li><a href="#digital">Digital</a></li>
                    <li><a href="#inclusion">Inclusion</a></li>
                    <li><a href="#dev-perso">Développement personnel</a></li>
                    <li><a href="#soft-skills">Soft skills</a></li>
                    <li><a href="#technique">Technique</a></li>
                </ul>
            </div>
            <div class="forum-topics">
                <h3>Topics récents</h3>
                <div class="topic-card pinned">
                    <h4> FAQ : Comment obtenir un certificat ?</h4>
                    <p>Retrouvez toutes les réponses à vos questions fréquentes.</p>
                    <span class="badge">Épinglé</span>
                </div>
                <div class="topic-card">
                    <h4>Comment optimiser son profil LinkedIn ?</h4>
                    <p>Partagez vos astuces pour un profil professionnel percutant.</p>
                    <span class="badge validated">Réponse validée </span>
                </div>
                <div class="topic-card">
                    <h4>Problème d’accès à un cours</h4>
                    <p>Des solutions pour débloquer votre compte.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SECTION 2 : GROUPES THÉMATIQUES ==================== -->
    <section class="groups-section">
        <div class="container">
            <h2>Groupes thématiques</h2>
            <p>Espaces collaboratifs selon les centres d’intérêt ou les parcours.</p>
            <div class="groups-grid">
                <div class="group-card">
                    <h3>Leadership éthique</h3>
                    <p>Chat actif<br>Guides de management<br>Webinaire mensuel<br>120 membres</p>
                    <a href="#leadership" class="btn-primary" aria-label="Rejoindre le groupe Leadership éthique">Rejoindre</a>
                </div>
                <div class="group-card">
                    <h3>Entrepreneuriat solidaire</h3>
                    <p> Discussions projets<br>Études de cas<br>Ateliers pratiques<br> 85 membres</p>
                    <a href="#entrepreneuriat" class="btn-primary" aria-label="Rejoindre le groupe Entrepreneuriat solidaire">Rejoindre</a>
                </div>
                <div class="group-card">
                    <h3>Tech pour tous</h3>
                    <p>Forum tech<br>Tutoriels<br> Hackathons<br>200 membres</p>
                    <a href="#tech" class="btn-primary" aria-label="Rejoindre le groupe Tech pour tous">Rejoindre</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SECTION 3 : MENTORAT ==================== -->
    <section class="mentorship-section">
        <div class="container">
            <h2> Mentorat & échanges interpersonnels</h2>
            <p>Encourager la transmission des savoirs et la solidarité intergénérationnelle.</p>
            <ul class="mentorship-list">
                <li><span class="emoji"></span> Mise en relation entre apprenants expérimentés et nouveaux inscrits</li>
                <li><span class="emoji"></span> Option de mentorat volontaire (un “guide” pour accompagner les autres)</li>
                <li><span class="emoji"></span> Système de pairage intelligent : selon les parcours, intérêts, zone géographique</li>
            </ul>
        
        </div>
    </section>

    <!-- ==================== SECTION 4 : ÉVÉNEMENTS COMMUNAUTAIRES ==================== -->
    <section class="events-section">
        <div class="container">
            <h2> Événements communautaires</h2>
            <p>Favoriser le lien humain dans la formation à distance.</p>
            <div class="events-grid">
                <div class="event-card">
                    <h3> Webinaire : Inclusion numérique</h3>
                    <p><br>Avec Maéva, experte inclusion</p>
                    <a href="#webinaire" class="btn-primary" aria-label="S’inscrire au webinaire">S’inscrire</a>
                </div>
                <div class="event-card">
                    <h3> Q&A : Leadership</h3>
                    <p><br>Avec Jean-Luc, coach</p>
                    <a href="#qa" class="btn-primary" aria-label="Participer au Q&A">Participer</a>
                </div>
                <div class="event-card">
                    <h3>Défi : Nouvelle compétence</h3>
                    <p><br>Apprenez et partagez !</p>
                    <a href="#defi" class="btn-primary" aria-label="Rejoindre le défi">Rejoindre</a>
                </div>
            </div>
            <div class="community-wall">
                <h3> Mur communautaire</h3>
                <div class="wall-posts">
                    <div class="post-card">
                        <img src="https://via.placeholder.com/100" alt="Photo parcours">
                        <p>“Fier d’avoir terminé mon cours digital !” — Sarah</p>
                    </div>
                    <div class="post-card">
                        <img src="https://via.placeholder.com/100" alt="Photo événement">
                        <p>“Super webinaire, merci Yitro !” — Amadou</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SECTION 5 : SÉCURITÉ & MODÉRATION ==================== -->
    <section class="security-section">
        <div class="container">
            <h2> garantir un environnement bienveillant, respectueux et conforme aux valeurs Yitro.</p>
            <ul class="security-list">
                <li><span class="emoji"></span> Charte de bonne conduite affichée dans tous les espaces d’échange</li>
                <li><span class="emoji"></span> Modérateurs désignés (Yitro + bénévoles ambassadeurs)</li>
                <li><span class="emoji"></span> Bouton “signaler” pour signaler un abus ou une publication inappropriée</li>
                <li><span class="emoji"></span> Données sécurisées et RGPD compliant</li>
            </ul>
           
        </div>
    </section>

    <!-- ==================== SECTION 6 : AMBASSADEURS ==================== -->
    <section class="ambassadors-section">
        <div class="container">
            <h2> Ambassadeurs & engagement communautaire</h2>
            <p>Encourager les utilisateurs engagés à devenir leaders de communauté.</p>
            <div class="ambassadors-program">
                <h3> Programme "Ambassadeur Yitro"</h3>
                <p>Missions : animation, mentorat, relai local ou thématique<br>Avantages : badge officiel, certificat, accès à des contenus exclusifs</p>
                <p> Proposez un groupe thématique personnalisé</p>
                <a href="#devenir-ambassadeur" class="btn-primary" aria-label="Devenir ambassadeur Yitro">Devenir ambassadeur</a>
            </div>
        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
    <footer class="footer">
        <div class="container">
            <!-- Colonne logo et description -->

            <!-- Liens du footer organisés en colonnes -->
            <div class="footer-links">
                <!-- Colonne 1: Navigation -->
                <div class="footer-column">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">Formations</a></li>
                        <li><a href="#">Certifications</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>

                <!-- Colonne 2: Légal -->
                <div class="footer-column">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">CGU</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>

                <!-- Colonne 3: Contact -->
                <div class="footer-column">
                    <h4>Contact</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> contact@yitro-consulting.com</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-map-marker-alt"></i> Paris, France</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section copyright -->
        <div class="footer-bottom">
            <p>&copy; 2024 Yitro Consulting. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Fichier JavaScript -->
    <script src="../asset/js/script.js"></script>
</body>

</html>