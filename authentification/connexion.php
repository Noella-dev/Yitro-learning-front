<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Yitro Learning</title>
    <link rel="icon" href="../asset/images/Yitro_consulting.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/style.css">
</head>
<style>
/* Réinitialisation et styles globaux */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8fafc;
    color: #333;
    line-height: 1.6;
}

/* Section Hero */
.heros {
    position: relative;
    background: linear-gradient(109deg, #01ae8f, #132F3F);
    padding: 80px 20px;
    text-align: center;
    overflow: hidden;
}

.heros-content {
    max-width: 800px;
    margin: 0 auto;
    color: #fff;
    position: relative;
    z-index: 1;
}

.heros-content h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.3;
}

.heros-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-buttons {
    display: inline-block;
    padding: 14px 28px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    background-color: #ff6f61;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.cta-buttons:hover {
    background-color: #e55a50;
    transform: translateY(-2px);
    text-decoration: none; /* Supprimer soulignement au survol */
}

#heros-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    opacity: 0.2;
}

/* Section Connexion */
.connexion-section {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    justify-content: center;
}

.connexion-card {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 100%;
    max-width: 550px;
    display: flex;
    gap: 20px;
    align-items: stretch;
    transition: transform 0.3s ease;
}

.connexion-card:hover {
    transform: translateY(-5px);
}

.connexion-form,
.connexion-guide {
    flex: 1;
}

.connexion-form h2 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1a3c6d;
    margin-bottom: 20px;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}

.connexion-form .form-group {
    margin-bottom: 1.5rem;
}

.connexion-form .form-label {
    font-size: 1rem;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.connexion-form .form-control {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.connexion-form .form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.connexion-form .btn-primary {
    padding: 14px 24px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    width: 100%;
    margin-top: 10px;
    text-decoration: none; /* Supprimer soulignement */
}

.connexion-form .btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    text-decoration: none; /* Supprimer soulignement au survol */
}

.connexion-form .btn-primary:active {
    transform: translateY(0);
}

.connexion-guide h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a3c6d;
    margin-bottom: 15px;
}

.connexion-guide p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 15px;
}

.connexion-guide ul {
    list-style: none;
    padding: 0;
}

.connexion-guide ul li {
    font-size: 1rem;
    color: #555;
    margin-bottom: 10px;
    position: relative;
    padding-left: 20px;
}

.connexion-guide ul li::before {
    content: '\f058';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: #007bff;
    position: absolute;
    left: 0;
    top: 2px;
}

/* Animation pour les champs requis */
.form-control:required:invalid {
    border-color: #ef4444;
}

.form-control:required:valid {
    border-color: #10b981;
}

/* Animation d'apparition */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive design */
@media (max-width: 768px) {
    .heros {
        padding: 60px 15px;
    }

    .heros-content h1 {
        font-size: 2rem;
    }

    .heros-content p {
        font-size: 1rem;
    }

    .connexion-card {
        flex-direction: column;
        max-width: 100%;
        padding: 20px;
    }

    .connexion-form h2,
    .connexion-guide h3 {
        font-size: 1.3rem;
    }
}

.error { color: red; }
.success { color: green; }
</style>
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
                        <li><a href="#" class="btn-primary">Connexion</a></li>
                        <li><a href="inscription.php" class="btn-primary">S'inscrire</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Section Hero -->
    <section class="heros">
        <div class="heros-content">
            <h1>Connectez-vous à Yitro Learning</h1>
            <p>Accédez à votre espace formateur ou apprenant pour commencer ou continuer votre parcours d'apprentissage.</p>
            <a href="#connexion" class="cta-buttons">Se connecter maintenant</a>
        </div>
        <canvas id="heros-animation"></canvas>
    </section>

    <!-- Section Connexion -->
    <section class="connexion-section" id="connexion">
        <!-- Formulaire Apprenant -->
        <div class="connexion-card fade-in">
            <div class="connexion-form">
                <h2>Connexion Apprenant</h2>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
                    unset($_SESSION['success']);
                }
                ?>
                <form id="apprenantForm" action="connexion-apprenant.php" method="POST">
                    <div class="form-group">
                        <label for="emailApprenant" class="form-label">Adresse e-mail *</label>
                        <input type="email" class="form-control" id="emailApprenant" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="passwordApprenant" class="form-label">Mot de passe *</label>
                        <input type="password" class="form-control" id="passwordApprenant" name="password" minlength="8" required>
                    </div>
                    <button type="submit" class="btn-primary">Se connecter</button>
                </form>
            </div>
            <div class="connexion-guide">
                <h3>Guide pour les Apprenants</h3>
                <p>Connectez-vous pour accéder à vos cours, suivre votre progression et obtenir vos certificats.</p>
                <ul>
                    <li>Utilisez l'e-mail et le mot de passe de votre inscription.</li>
                    <li>Si vous avez oublié votre mot de passe, contactez-nous à <a href="mailto:support@yitro-consulting.com">support@yitro-consulting.com</a>.</li>
                    <li>Pas encore inscrit ? <a href="../page/inscription.php">Créez un compte</a>.</li>
                </ul>
            </div>
        </div>

        <!-- Formulaire Formateur -->
        <div class="connexion-card fade-in">
            <div class="connexion-form">
                <h2>Connexion Formateur</h2>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
                    unset($_SESSION['success']);
                }
                ?>
                <form id="formateurForm" action="connexion-formateur.php" method="POST">
                    <div class="form-group">
                        <label for="emailFormateur" class="form-label">Adresse e-mail *</label>
                        <input type="email" class="form-control" id="emailFormateur" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="passwordFormateur" class="form-label">Mot de passe *</label>
                        <input type="password" class="form-control" id="passwordFormateur" name="password" minlength="8" required>
                    </div>
                    <button type="submit" class="btn-primary">Se connecter</button>
                </form>
            </div>
            <div class="connexion-guide">
                <h3>Guide pour les Formateurs</h3>
                <p>Connectez-vous pour gérer vos cours, interagir avec vos apprenants et suivre leurs progrès.</p>
                <ul>
                    <li>Utilisez l'e-mail fourni lors de votre candidature.</li>
                    <li>Problèmes de connexion ? Contactez <a href="mailto:formateur@yitro-consulting.com">formateur@yitro-consulting.com</a>.</li>
                    <li>Envie de rejoindre notre équipe ? <a href="formulaire-formateur.php">Postulez ici</a>.</li>
                </ul>
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

        // Mise en surbrillance des champs actifs et validation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('active');
            });
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('active');
            });
            input.addEventListener('input', () => {
                if (input.validity.valid) {
                    input.classList.add('valid');
                    input.classList.remove('invalid');
                } else {
                    input.classList.add('invalid');
                    input.classList.remove('valid');
                }
            });
        });

        // Animation du bouton de soumission
        const submitButtons = document.querySelectorAll('.btn-primary');
        submitButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const form = btn.closest('form');
                if (!form.checkValidity()) {
                    e.preventDefault();
                    btn.classList.add('shake');
                    setTimeout(() => btn.classList.remove('shake'), 500);
                }
            });
        });
    </script>
</body>
</html>