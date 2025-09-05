<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisissez votre parcours - Yitro Learning</title>
    <link rel="icon" href="../../asset/images/Yitro_consulting.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/inscription.css">
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="main-nav">
            <div class="container">
                <div class="logo">
                    <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                    <a href="../index.php" class="logo-text">Yitro Learning</a>
                </div>
                <ul class="nav-list">
                    <li class="dropdown">
                        <a href="#">À propos <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="../page/decouvrir-yitro.php">Découvrir Yitro</a></li>
                            <li><a href="../page/FAQ.php">FAQ</a></li>
                            <li><a href="../page/contact.php">Contact</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="../page/catalogue.php">Nos formations <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown">
                                <a href="#">Compétences Bureautiques & Outil <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Excel et analyse de données</a></li>
                                    <li><a href="#">Bureautique générale</a></li>
                                    <li><a href="#">Outils de collaboration et gestion</a></li>
                                    <li><a href="#">Compétences numériques fondamentales</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Marketing Digital <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">SEO / Référencement</a></li>
                                    <li><a href="#">Marketing des réseaux sociaux</a></li>
                                    <li><a href="#">Publicité en ligne</a></li>
                                    <li><a href="#">Content marketing & copywriting</a></li>
                                    <li><a href="#">Marketing d’affiliation ou influence</a></li>
                                    <li><a href="#">Création de contenu et personal branding</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Finance & Investissement <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Finance personnelle</a></li>
                                    <li><a href="#">Bourse et trading</a></li>
                                    <li><a href="#">Analyse financière et modélisation</a></li>
                                    <li><a href="#">Cryptomonnaies & blockchain</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Santé & Bien-être <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Formations Fitness</a></li>
                                    <li><a href="#">Yoga et Méditation</a></li>
                                    <li><a href="#">Nutrition & santé</a></li>
                                    <li><a href="#">Bien-être mental et vie saine</a></li>
                                    <li><a href="#">Éducation bienveillante (parents)</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Développement Personnel (Soft Skills, productivité) <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Productivité et gestion du temps</a></li>
                                    <li><a href="#">Leadership et communication</a></li>
                                    <li><a href="#">Gestion du stress et confiance en soi</a></li>
                                    <li><a href="#">Compétences créatives de réflexion</a></li>
                                    <li><a href="#">Relationnel et développement de carrière</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#">Arts, Design & Artisanat <i class="fas fa-chevron-right"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Arts visuels</a></li>
                                    <li><a href="#">Design & graphisme</a></li>
                                    <li><a href="#">Photographie et vidéo</a></li>
                                    <li><a href="#">Musique</a></li>
                                    <li><a href="#">Artisanat & DIY (Do It Yourself)</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Business & Entrepreneuriat</a></li>
                            <li><a href="../page/formations/categories/tech-programmation.php">Technologie & Programmation</a></li>
                            <li><a href="#">Langues étrangères</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="auth-links">
                    <ul class="nav-list">
                        <li><a href="connexion.php" class="btn-primary">Connexion</a></li>
                        <li><a href="#" class="btn-primary">S'inscrire</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Section Hero -->
    <section class="heros">
        <div class="heros-content">
            <h1>Rejoignez Yitro Learning</h1>
            <p>Que vous souhaitiez enseigner ou apprendre, Yitro Learning vous offre une plateforme pour développer vos compétences et partager votre passion.</p>
        </div>
        <canvas id="heros-animation"></canvas>
    </section>

    <!-- Section Choix -->
    <section class="choice-section">
        <h2>Choisissez votre parcours</h2>
        <div class="choice-grid">
            <!-- Carte Formateur -->
            <div class="choice-card fade-in">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Devenir Formateur</h3>
                <p>Partagez votre expertise avec des milliers d'apprenants et inspirez la prochaine génération de professionnels.</p>
                <a href="./inscription-formateur.php" class="btn-choice">S'inscrire comme formateur</a>
            </div>
            <!-- Carte Apprenant -->
            <div class="choice-card fade-in">
                <i class="fas fa-user-graduate"></i>
                <h3>Devenir Apprenant</h3>
                <p>Apprenez à votre rythme avec nos cours interactifs en technologie, programmation et bien plus encore.</p>
                <a href="./inscription-apprenant.php" class="btn-choice">S'inscrire comme apprenant</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Suivez-nous</h4>
                    <ul>
                        <li><a href="#">Suivez-nous sur les réseaux sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités.</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Yitro Learning</h4>
                    <ul>
                        <li><a href="#">Lot 304-D_240 Andafiatsimo Ambohitrinibe</a></li>
                        <li><a href="#">110 Antsirabe</a></li>
                        <li><a href="#">Lun – Ven | 08h à 14h à 18h</a></li>
                        <li><a href="#">Sam – Dim | Fermé</a></li>
                        <li><a href="#">contact@yitro-consulting.com</a></li>
                        <li><a href="#">+261 34 53 313 87</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>À propos</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="../page/catalogue.php">Formations</a></li>
                        <li><a href="#">Certifications</a></li>
                        <li><a href="../page/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="#">Mentions légales</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Tous droits réservés – SK Yitro Consulting © 2025</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Animation pour le canvas de la section hero
        const canvas = document.getElementById('heros-animation');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = canvas.parentElement.offsetHeight;

        let particlesArray = [];

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 1 - 0.5;
                this.speedY = Math.random() * 1 - 0.5;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width) {
                    this.speedX = -this.speedX;
                }
                if (this.y < 0 || this.y > canvas.height) {
                    this.speedY = -this.speedY;
                }
            }
            draw() {
                ctx.fillStyle = 'rgba(255,255,255,0.8)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function init() {
            particlesArray = [];
            for (let i = 0; i < 100; i++) {
                particlesArray.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particlesArray.length; i++) {
                particlesArray[i].update();
                particlesArray[i].draw();
            }
            requestAnimationFrame(animate);
        }

        init();
        animate();

        window.addEventListener('resize', function() {
            canvas.width = window.innerWidth;
            canvas.height = canvas.parentElement.offsetHeight;
            init();
        });

        // Animation d'apparition au défilement
        const fadeInElements = document.querySelectorAll('.fade-in');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        fadeInElements.forEach(element => {
            observer.observe(element);
        });
    </script>
</body>
</html>