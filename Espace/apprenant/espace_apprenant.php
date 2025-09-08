
<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../authentification/login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Récupérer tous les cours depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM cours");
$stmt->execute();
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Espace Apprenant</title>
    <link rel="stylesheet" href="../../asset/css/styles.css">
    <link rel="icon" href="asset/images/Yitro_consulting.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Header Styles */
        header {
            background: linear-gradient(109deg, #132F3F, #01ae8f);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 45px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.1);
        }

        .logo-text {
            text-decoration: none;
            font-weight: 700;
            font-size: 20px;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 25px;
            margin: 0;
            padding: 0;
        }

        .nav-list a {
            text-decoration: none;
            color: #e0e0e0;
            font-weight: 500;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .nav-list a:hover {
            color: #9b8227;
            transform: translateY(-2px);
        }

        .auth-links .nav-list {
            gap: 15px;
        }

        .auth-links .btn-primary {
            background-color: #9b8227;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .auth-links .btn-primary:hover {
            background-color: #e68c32;
            transform: scale(1.05);
        }

        /* Main Layout */
        .main-content {
            display: flex;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 80px;
            height: fit-content;
        }

        .sidebar h3 {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar .filter-group {
            margin-bottom: 20px;
        }

        .sidebar label {
            font-size: 0.95em;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }

        .sidebar select,
        .sidebar input[type="text"],
        .sidebar input[type="range"] {
            width: 100%;
            padding: 10px;
            font-size: 0.95em;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            transition: border-color 0.3s ease;
        }

        .sidebar select:focus,
        .sidebar input[type="text"]:focus,
        .sidebar input[type="range"]:focus {
            border-color: #9b8227;
            outline: none;
        }

        .sidebar .price-range {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar .price-range output {
            font-size: 0.9em;
            color: #2c3e50;
            text-align: center;
        }

        .sidebar button {
            width: 100%;
            background: #9b8227;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar button:hover {
            background: #e68c32;
            transform: scale(1.03);
        }

        .sidebar-toggle {
            display: none;
            background: #9b8227;
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            margin: 20px;
        }

        .content {
            flex: 1;
        }

        /* Course Section */
        .courses-section {
            padding: 60px 20px;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            font-size: 2.2em;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .course-card {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .course-img img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 3px solid #9b8227;
        }

        .course-content {
            padding: 20px;
        }

        .course-content h3 {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .course-content p {
            font-size: 0.95em;
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .course-content .price {
            font-size: 1.1em;
            font-weight: 700;
            color: #2ecc71;
            margin-bottom: 15px;
        }

        .btn-learn {
            display: block;
            text-align: center;
            background: #9b8227;
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-learn:hover {
            background: #e68c32;
            transform: scale(1.03);
        }

        .no-courses {
            text-align: center;
            font-size: 1.1em;
            color: #7f8c8d;
            margin: 40px 0;
        }

        /* Forum Section */
        .forum-section {
            padding: 60px 20px;
            background: #ffffff;
        }

        .forum-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .forum-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .forum-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .forum-card h3 {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .forum-card p {
            font-size: 0.95em;
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .btn-forum {
            display: block;
            text-align: center;
            background: #2ecc71;
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-forum:hover {
            background: #27ae60;
            transform: scale(1.03);
        }

        .forum-form {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        .forum-form h3 {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .forum-form input,
        .forum-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95em;
            font-family: 'Montserrat', sans-serif;
        }

        .forum-form input:focus,
        .forum-form textarea:focus {
            border-color: #9b8227;
            outline: none;
        }

        .forum-form button {
            background: #9b8227;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .forum-form button:hover {
            background: #e68c32;
            transform: scale(1.03);
        }

        .no-forum {
            text-align: center;
            font-size: 1.1em;
            color: #7f8c8d;
            margin: 40px 0;
        }

        /* Footer Styles */
        .footer {
            background: #132F3F;
            color: #e0e0e0;
            padding: 40px 20px;
        }

        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .footer-column h4 {
            font-size: 1.2em;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #cbcbcb;
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: #9b8227;
        }

        .footer-bottom {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #2a3f50;
            margin-top: 20px;
            font-size: 0.9em;
            color: #cbcbcb;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .main-nav {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .nav-list {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }

            .auth-links .nav-list {
                width: 100%;
                justify-content: center;
            }

            .courses-grid,
            .forum-grid {
                grid-template-columns: 1fr;
            }

            .course-img img {
                height: 180px;
            }

            .section-title {
                font-size: 1.8em;
            }

            .forum-form {
                padding: 15px;
            }

            .main-content {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
                margin-bottom: 20px;
            }

            .sidebar-toggle {
                display: block;
            }

            .sidebar.hidden {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .logo img {
                height: 35px;
            }

            .logo-text {
                font-size: 18px;
            }

            .course-content h3,
            .forum-card h3,
            .forum-form h3 {
                font-size: 1.2em;
            }

            .course-content p,
            .forum-card p {
                font-size: 0.9em;
            }

            .course-img img {
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="logo">
                <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                <a href="#" class="logo-text">Yitro Learning</a>
            </div>
            <ul class="nav-list">
                <li><a href="#">Catalogues</a></li>
                <li><a href="progression_apprenant.php">Ma progression</a></li>
                <li><a href="mes_cours.php">Mes Cours</a></li>

            </ul>
            <div class="auth-links">
                <ul class="nav-list">
                    <li>
                        <a href="#" class="btn-primary">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($user['nom']); ?>
                        </a>
                    </li>
                    <li><a href="../../authentification/logout.php" class="btn-primary">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="sidebar-toggle"><i class="fas fa-filter"></i> Filtres</div>
    <div class="main-content">
        <aside class="sidebar">
            <h3><i class="fas fa-filter"></i> Filtres</h3>
            <div class="filter-group">
                <h4><i class="fas fa-book"></i> Filtrer les cours</h4>
                <label for="course-category">Catégorie</label>
                <select id="course-category">
                    <option value="">Toutes les catégories</option>
                    <option value="development">Développement</option>
                    <option value="marketing">Marketing</option>
                    <option value="design">Design</option>
                    <option value="business">Business</option>
                </select>
                <label for="course-level">Niveau</label>
                <select id="course-level">
                    <option value="">Tous les niveaux</option>
                    <option value="beginner">Débutant</option>
                    <option value="intermediate">Intermédiaire</option>
                    <option value="advanced">Avancé</option>
                </select>
                <label>Prix</label>
                <div class="price-range">
                    <input type="range" id="price-min" min="0" max="500" value="0">
                    <input type="range" id="price-max" min="0" max="500" value="500">
                    <output>Prix: <span id="price-min-value">0</span> € - <span id="price-max-value">500</span> €</output>
                </div>
            </div>
            <div class="filter-group">
                <h4><i class="fas fa-comments"></i> Filtrer les forums</h4>
                <label for="forum-course">Cours associé</label>
                <select id="forum-course">
                    <option value="">Tous les cours</option>
                    <?php foreach ($cours as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="forum-keyword">Recherche par mot-clé</label>
                <input type="text" id="forum-keyword" placeholder="Mot-clé...">
            </div>
            <button id="apply-filters">Appliquer les filtres</button>
        </aside>
        <main class="content">
            <section class="courses-section">
                <h2 class="section-title">Nos Cours Disponibles</h2>
                <div class="courses-grid">
                    <?php if (empty($cours)): ?>
                        <p class="no-courses">Aucun cours disponible pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($cours as $c): ?>
                            <div class="course-card" data-category="<?php echo htmlspecialchars($c['category'] ?? 'development'); ?>" data-level="<?php echo htmlspecialchars($c['level'] ?? 'beginner'); ?>" data-price="<?php echo $c['prix']; ?>">
                                <div class="course-img">
                                    <?php if ($c['photo']): ?>
                                        <img src="../../Uploads/cours/<?php echo htmlspecialchars($c['photo']); ?>" alt="<?php echo htmlspecialchars($c['titre']); ?>">
                                    <?php else: ?>
                                        <img src="../../asset/images/default_course.jpg" alt="Image par défaut">
                                    <?php endif; ?>
                                </div>
                                <div class="course-content">
                                    <h3><?php echo htmlspecialchars($c['titre']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($c['description'], 0, 100)) . (strlen($c['description']) > 100 ? '...' : ''); ?></p>
                                    <div class="price"><?php echo number_format($c['prix'], 2); ?> €</div>
                                    <a href="cours_details.php?id=<?php echo $c['id']; ?>" class="btn-learn">Voir les détails</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-column">
                <h4>Suivez-nous</h4>
                <ul>
                    <li><a href="#">Suivez-nous sur les Réseaux Sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités.</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Yitro Learning</h4>
                <ul>
                    <li><a href="#">Lot 304-D_240 Andafiatsimo Ambohitrinibe</a></li>
                    <li><a href="#">110 Antsirabe</a></li>
                    <li><a href="#">Lun – Ven | 08h – 12h | 14h – 18h</a></li>
                    <li><a href="#">Sam – Dim | Fermé</a></li>
                    <li><a href="#">contact@yitro-consulting.com</a></li>
                    <li><a href="#">+261 34 53 313 87</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>À propos</h4>
                <ul>
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">Formations</a></li>
                    <li><a href="#">Certifications</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Légal</h4>
                <ul>
                    <li><a href="#">Mentions légales</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Tous droits réservés – SK Yitro Consulting © 2024</p>
        </div>
    </footer>

    <script>
        // JavaScript pour gérer l'animation et la visibilité de la barre de navigation
        const topNav = document.querySelector('.main-nav');
        let lastScrollTop = 0;
        let isAnimating = false;
        let scrollTimeout = null;

        function handleScroll() {
            if (isAnimating) return;

            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > 100) {
                if (!topNav.classList.contains('hidden')) {
                    isAnimating = true;
                    topNav.classList.add('hidden');
                    setTimeout(() => {
                        topNav.classList.add('invisible');
                        isAnimating = false;
                    }, 500);
                }
            } else {
                if (topNav.classList.contains('hidden') || topNav.classList.contains('invisible')) {
                    isAnimating = true;
                    topNav.classList.remove('invisible');
                    topNav.offsetHeight;
                    topNav.classList.remove('hidden');
                    setTimeout(() => {
                        isAnimating = false;
                    }, 500);
                }
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        }

        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(handleScroll, 100);
        });

        // JavaScript pour la sidebar et le filtrage
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const applyFiltersButton = document.querySelector('#apply-filters');
        const courseCategory = document.querySelector('#course-category');
        const courseLevel = document.querySelector('#course-level');
        const priceMin = document.querySelector('#price-min');
        const priceMax = document.querySelector('#price-max');
        const priceMinValue = document.querySelector('#price-min-value');
        const priceMaxValue = document.querySelector('#price-max-value');
        const forumCourse = document.querySelector('#forum-course');
        const forumKeyword = document.querySelector('#forum-keyword');
        const courseCards = document.querySelectorAll('.course-card');
        const forumCards = document.querySelectorAll('.forum-card');

        // Toggle sidebar on mobile
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });

        // Update price range values
        priceMin.addEventListener('input', () => {
            priceMinValue.textContent = priceMin.value;
        });

        priceMax.addEventListener('input', () => {
            priceMaxValue.textContent = priceMax.value;
        });

        // Apply filters
        applyFiltersButton.addEventListener('click', () => {
            const selectedCategory = courseCategory.value;
            const selectedLevel = courseLevel.value;
            const minPrice = parseFloat(priceMin.value);
            const maxPrice = parseFloat(priceMax.value);
            const selectedForumCourse = forumCourse.value;
            const keyword = forumKeyword.value.toLowerCase();

            // Filter courses
            courseCards.forEach(card => {
                const category = card.dataset.category || '';
                const level = card.dataset.level || '';
                const price = parseFloat(card.dataset.price) || 0;

                const matchesCategory = !selectedCategory || category === selectedCategory;
                const matchesLevel = !selectedLevel || level === selectedLevel;
                const matchesPrice = price >= minPrice && price <= maxPrice;

                if (matchesCategory && matchesLevel && matchesPrice) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            // Filter forums
            forumCards.forEach(card => {
                const courseId = card.dataset.courseId || '';
                const title = card.dataset.title.toLowerCase();
                const description = card.dataset.description.toLowerCase();

                const matchesCourse = !selectedForumCourse || courseId === selectedForumCourse;
                const matchesKeyword = !keyword || title.includes(keyword) || description.includes(keyword);

                if (matchesCourse && matchesKeyword) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
