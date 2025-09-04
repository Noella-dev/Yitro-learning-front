<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - Yitro Learning</title>
    <link rel="icon" href="../asset/images/Yitro_consulting.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../asset/css/catalogue.css">
    <style>
        /* Styles pour la nouvelle interface du catalogue */
        .catalogue-hero {
            position: relative;
            background: linear-gradient(109deg, #01ae8f, #132f3f);
            padding: 100px 20px 60px;
            text-align: center;
            overflow: hidden;
        }

        .catalogue-hero-content {
            max-width: 900px;
            margin: 0 auto;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .catalogue-hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease-out;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .catalogue-hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-out 0.2s;
            animation-fill-mode: both;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .catalogue-hero-content .search-bar {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
            animation: fadeInUp 1s ease-out 0.4s;
            animation-fill-mode: both;
        }

        .catalogue-hero-content .search-bar input {
            flex: 1;
            padding: 12px;
            font-size: 1rem;
            border: none;
            border-radius: 6px 0 0 6px;
            outline: none;
            background: #fff;
            color: #333;
        }

        .catalogue-hero-content .search-bar button {
            padding: 12px 20px;
            background-color: #ff6f61;
            color: #fff;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .catalogue-hero-content .search-bar button:hover {
            background-color: #e55a50;
        }

        #hero-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.2;
        }

        .catalogue-main {
            display: flex;
            gap: 20px;
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
            background: #f8fafc;
        }

        .catalogue-sidebar {
            width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }

        .catalogue-sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #132f3f;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .filter-group select {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
            background: #f8fafc;
            color: #333;
        }

        .catalogue-content {
            flex: 1;
        }

        .catalogue-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .catalogue-controls h2 {
            color: #132f3f;
        }

        .sort-options label {
            color: #333;
            margin-right: 10px;
        }

        .sort-options select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f8fafc;
            color: #333;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .course-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .course-card-content {
            padding: 20px;
        }

        .course-card-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #132f3f;
        }

        .course-card-content p {
            font-size: 0.9rem;
            color: #444;
            margin-bottom: 8px;
        }

        .course-card-content .price {
            font-weight: 600;
            color: #ff6f61;
        }

        .course-card-content .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #01987a;
            color: #fff;
            font-size: 0.8rem;
            border-radius: 4px;
            margin-top: 10px;
        }

        .course-card-content .btn-primary {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            text-align: center;
            background: #4A90E2;
            color: #f8fafc;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .course-card-content .btn-primary:hover {
            transform: translateY(-2px);
            background-color: #357ABD;
        }

        .categories-section {
            padding: 40px 20px;
            background: #f9f9f9;
        }

        .categories-section h2 {
            font-size: 2rem;
            color: #132f3f;
            text-align: center;
            margin-bottom: 20px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .category-card {
            background: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-card i {
            font-size: 2rem;
            color: #01ae8f;
            margin-bottom: 10px;
        }

        .category-card h3 {
            font-size: 1.2rem;
            color: #132f3f;
        }

        .cta-section {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(109deg, #01ae8f, #2a5366);
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .cta-section .btn-primary {
            margin: 0 10px;
            padding: 12px 24px;
            font-size: 1.1rem;
            background: #ffd700;
            color: #132f3f;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cta-section .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .catalogue-main {
                flex-direction: column;
            }

            .catalogue-sidebar {
                width: 100%;
                position: static;
            }
        }

        @media (max-width: 768px) {
            .catalogue-hero-content h1 {
                font-size: 2rem;
            }

            .catalogue-hero-content p {
                font-size: 1rem;
            }

            .catalogue-hero {
                padding: 80px 15px 40px;
            }

            .course-card img {
                height: 150px;
            }
        }
    </style>
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
                            <li><a href="decouvrir-yitro.php">Découvrir Yitro</a></li>
                            <li><a href="FAQ.php">FAQ</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="catalogue.php">Nos formations <i class="fas fa-chevron-down"></i></a>
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
                            <li><a href="formations/categories/tech-programmation.php">Technologie & Programmation</a></li>
                            <li><a href="#">Langues étrangères</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="auth-links">
                    <ul class="nav-list">
                        <li><a href="../authentification/connexion.php" class="btn-primary">Connexion</a></li>
                        <li><a href="../authentification/inscription.php" class="btn-primary">S'inscrire</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="catalogue-hero">
        <div class="catalogue-hero-content">
            <h1>Explorez nos formations</h1>
            <p>Découvrez une large gamme de cours conçus par des experts pour booster vos compétences. Trouvez la formation parfaite pour vous dès aujourd'hui !</p>
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Rechercher une formation..." aria-label="Rechercher une formation">
                <button type="button" onclick="filterCourses()"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <canvas id="hero-animation"></canvas>
    </section>

    <!-- Main Catalogue Section -->
    <div class="catalogue-main">
        <aside class="catalogue-sidebar">
            <h2>Filtres</h2>
            <div class="filter-group">
                <label for="theme">Thème</label>
                <select id="theme" onchange="filterCourses()">
                    <option value="">Tous</option>
                    <option value="digital">Marketing Digital</option>
                    <option value="business">Business</option>
                    <option value="tech">Technologie</option>
                    <option value="health">Santé & Bien-être</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="level">Niveau</label>
                <select id="level" onchange="filterCourses()">
                    <option value="">Tous</option>
                    <option value="beginner">Débutant</option>
                    <option value="intermediate">Intermédiaire</option>
                    <option value="advanced">Avancé</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="duration">Durée</label>
                <select id="duration" onchange="filterCourses()">
                    <option value="">Toutes</option>
                    <option value="short">Moins de 2h</option>
                    <option value="medium">2h - 5h</option>
                    <option value="long">Plus de 5h</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="price">Prix</label>
                <select id="price" onchange="filterCourses()">
                    <option value="">Tous</option>
                    <option value="free">Gratuit</option>
                    <option value="paid">Payant</option>
                </select>
            </div>
        </aside>

        <div class="catalogue-content">
            <div class="catalogue-controls">
                <h2>Nos cours</h2>
                <div class="sort-options">
                    <label for="sort">Trier par :</label>
                    <select id="sort" onchange="sortCourses()">
                        <option value="popularity">Popularité</option>
                        <option value="newest">Plus récent</option>
                        <option value="price">Prix</option>
                    </select>
                </div>
            </div>
            <div class="courses-grid" id="courses-grid">
                <div class="course-card" data-theme="digital" data-level="beginner" data-duration="medium" data-price="free" data-popularity="100" data-date="2025-01-01" data-price-value="0">
                    <img src="../asset/images/marketing-digital.jpg" alt="Marketing Digital 101">
                    <div class="course-card-content">
                        <h3>Marketing Digital 101</h3>
                        <p class="trainer">Par Rina</p>
                        <p class="level">Débutant</p>
                        <p class="duration">2h</p>
                        <p class="price">Gratuit</p>
                        <span class="badge">Certificat</span>
                        <a href="/cours/marketing-digital" class="btn-primary">Accéder</a>
                    </div>
                </div>
                <div class="course-card" data-theme="tech" data-level="intermediate" data-duration="long" data-price="paid" data-popularity="90" data-date="2024-12-15" data-price-value="99">
                    <img src="../asset/images/programming.jpg" alt="Introduction à Python">
                    <div class="course-card-content">
                        <h3>Introduction à Python</h3>
                        <p class="trainer">Par Alex</p>
                        <p class="level">Intermédiaire</p>
                        <p class="duration">6h</p>
                        <p class="price">99€</p>
                        <span class="badge">Certificat</span>
                        <a href="/cours/python" class="btn-primary">Accéder</a>
                    </div>
                </div>
                <div class="course-card" data-theme="business" data-level="advanced" data-duration="medium" data-price="paid" data-popularity="85" data-date="2025-02-01" data-price-value="149">
                    <img src="../asset/images/business.jpg" alt="Stratégie d'Entreprise">
                    <div class="course-card-content">
                        <h3>Stratégie d'Entreprise</h3>
                        <p class="trainer">Par Sophie</p>
                        <p class="level">Avancé</p>
                        <p class="duration">4h</p>
                        <p class="price">149€</p>
                        <span class="badge">Certificat</span>
                        <a href="/cours/strategie" class="btn-primary">Accéder</a>
                    </div>
                </div>
                <div class="course-card" data-theme="health" data-level="beginner" data-duration="short" data-price="free" data-popularity="95" data-date="2025-03-01" data-price-value="0">
                    <img src="../asset/images/yoga.jpg" alt="Yoga pour Débutants">
                    <div class="course-card-content">
                        <h3>Yoga pour Débutants</h3>
                        <p class="trainer">Par Emma</p>
                        <p class="level">Débutant</p>
                        <p class="duration">1h</p>
                        <p class="price">Gratuit</p>
                        <span class="badge">Certificat</span>
                        <a href="/cours/yoga" class="btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2>Explorez par catégorie</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <i class="fas fa-laptop-code"></i>
                    <h3>Technologie</h3>
                </div>
                <div class="category-card">
                    <i class="fas fa-bullhorn"></i>
                    <h3>Marketing</h3>
                </div>
                <div class="category-card">
                    <i class="fas fa-briefcase"></i>
                    <h3>Business</h3>
                </div>
                <div class="category-card">
                    <i class="fas fa-heart"></i>
                    <h3>Santé & Bien-être</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Prêt à apprendre ?</h2>
            <p>Rejoignez des milliers d'apprenants et commencez votre parcours dès maintenant.</p>
            <div class="cta-buttons">
                <a href="../authentification/inscription.php" class="btn-primary">S'inscrire</a>
                <a href="contact.php" class="btn-primary">Nous contacter</a>
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
                        <li><a href="#">Suivez-nous sur les réseaux sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités. Rejoignez-nous sur nos réseaux sociaux.</a></li>
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
                        <li><a href="catalogue.php">Formations</a></li>
                        <li><a href="#">Certifications</a></li>
                        <li><a href="contact.php">Contact</a></li>
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

    <script>
        // Animation de particules pour la section hero
        const canvas = document.getElementById('hero-animation');
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
                if (this.x < 0 || this.x > canvas.width) this.speedX = -this.speedX;
                if (this.y < 0 || this.y > canvas.height) this.speedY = -this.speedY;
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

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = canvas.parentElement.offsetHeight;
            init();
        });

        // Filtrage et tri des cours
        function filterCourses() {
            const search = document.getElementById('search-input').value.toLowerCase();
            const theme = document.getElementById('theme').value;
            const level = document.getElementById('level').value;
            const duration = document.getElementById('duration').value;
            const price = document.getElementById('price').value;

            const courses = document.querySelectorAll('.course-card');

            courses.forEach(course => {
                const title = course.querySelector('h3').textContent.toLowerCase();
                const courseTheme = course.dataset.theme;
                const courseLevel = course.dataset.level;
                const courseDuration = course.dataset.duration;
                const coursePrice = course.dataset.price;

                const matchesSearch = title.includes(search);
                const matchesTheme = !theme || courseTheme === theme;
                const matchesLevel = !level || courseLevel === level;
                const matchesDuration = !duration || courseDuration === duration;
                const matchesPrice = !price || coursePrice === price;

                if (matchesSearch && matchesTheme && matchesLevel && matchesDuration && matchesPrice) {
                    course.style.display = 'block';
                } else {
                    course.style.display = 'none';
                }
            });
        }

        function sortCourses() {
            const sort = document.getElementById('sort').value;
            const grid = document.getElementById('courses-grid');
            const courses = Array.from(grid.querySelectorAll('.course-card'));

            courses.sort((a, b) => {
                if (sort === 'popularity') {
                    return b.dataset.popularity - a.dataset.popularity;
                } else if (sort === 'newest') {
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
                } else if (sort === 'price') {
                    return a.dataset.priceValue - b.dataset.priceValue;
                }
            });

            grid.innerHTML = '';
            courses.forEach(course => grid.appendChild(course));
        }
    </script>
</body>
</html>