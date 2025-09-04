<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning</title>
    <link rel="stylesheet" href="../asset/css/styles.css">
   
    <link rel="icon" href="asset/images/Yitro_consulting.png" type="image/png">
    <!-- Font Awesome pour les icônes -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../asset/css/FAQ.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

header {
    background: linear-gradient(109deg, #132F3F, #01ae8f);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.main-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 25px;
    max-width: 1200px;
    margin: 0 auto;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo img {
    height: 40px;
    border-radius: 50%;
}

.logo-text {
    text-decoration: none;
    font-weight: 700;
    font-size: 18px;
    color: #ffffff;
}

.nav-list {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 0;
    padding: 0;
}

.nav-list li {
    position: relative;
}

.nav-list a {
    text-decoration: none;
    color: #cbcbcb;
    font-weight: 500;
    font-size: 15px;
    transition: color 0.2s ease;
}

.nav-list a:hover {
    color: #9b8227;
}

.dropdown {
    position: relative;
}

.dropdown > a {
    display: flex;
    align-items: center;
    gap: 5px;
}

.dropdown > a i {
    font-size: 12px;
    color: #cbcbcb;
    transition: color 0.2s ease;
}

.dropdown:hover > a i {
    color: #9b8227;
}

.dropdown > a i.fa-chevron-down {
    transition: transform 0.2s ease;
}

.dropdown:hover > a i.fa-chevron-down {
    transform: rotate(180deg);
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #132F3F;
    border-radius: 6px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    list-style: none;
    padding: 8px 0;
    margin: 0;
    min-width: 160px;
    z-index: 1001;
}

.dropdown:hover > .dropdown-menu {
    display: block;
}

.dropdown-menu li {
    padding: 6px 15px;
    position: relative;
}

.dropdown-menu a {
    color: #cbcbcb;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.dropdown-menu a:hover {
    color: #9b8227;
}

.dropdown-menu .dropdown > a i.fa-chevron-right {
    font-size: 10px;
}

.dropdown-menu .dropdown:hover > .dropdown-menu {
    display: block;
    top: 0;
    left: 100%;
}

.auth-links .nav-list {
    gap: 12px;
}

.auth-links .btn-primary {
    background-color: #9b8227;
    color: white;
    padding: 6px 14px;
    border-radius: 18px;
    font-weight: bold;
    transition: background-color 0.2s ease;
}

.auth-links .btn-primary:hover {
    background-color: #e68c32;
}

/* Responsive */
@media (max-width: 768px) {
    .main-nav {
        flex-wrap: wrap;
        padding: 10px 15px;
    }

    .nav-list {
        gap: 15px;
    }

    .auth-links .nav-list {
        gap: 10px;
    }

    .dropdown-menu .dropdown:hover > .dropdown-menu {
        left: 0;
        top: 100%;
    }
}
</style>
<body>
<header>
  <nav class="main-nav">
    <div class="logo">
      <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
      <a href="../index.php" class="logo-text">Yitro Learning</a>
    </div>
    <ul class="nav-list">
      <li class="dropdown">
        <a href="#">À propos <i class="fas fa-chevron-down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="decouvrir-yitro.php">Decouvrir Yitro</a></li>
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
              <legend><a href="#">Compétences numériques fondamentales</a></legend>
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
            <a href="#">Finance & Investissement : <i class="fas fa-chevron-right"></i></a>
            <ul class="dropdown-menu">
              <li><a href="#">Finance personnellet</a></li>
              <li><a href="#">Bourse et trading</a></li>
              <li><a href="#">Analyse financière et modélisation</a></li>
              <li><a href="#">Cryptomonnaies & blockchain</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#">Santé & Bien-être <i class="fas fa-chevron-right"></i></a>
            <ul class="dropdown-menu">
              <li><a href="#">Formations Fitness</a></li>
              <li><a href="#">Yoga et Méditationx</a></li>
              <li><a href="#">Nutrition & santé</a></li>
              <li><a href="#">Bien-être mental et vie saine</a></li>
              <li><a href="#">Éducation bienveillante (parents)</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#">Développement Personnel (Soft Skills, productivité)<i class="fas fa-chevron-right"></i></a>
            <ul class="dropdown-menu">
              <li><a href="#">Productivité et gestion du tempst</a></li>
              <li><a href="#">Leadership et communication</a></li>
              <li><a href="#">Gestion du stress et confiance en soi</a></li>
              <li><a href="#">Compétences créatives de réflexion</a></li>
              <li><a href="#">Relationnel et développement de carrière</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#">Arts, Design & Artisanat<i class="fas fa-chevron-right"></i></a>
            <ul class="dropdown-menu">
              <li><a href="#">Arts visuels</a></li>
              <li><a href="#">Design & graphisme</a></li>
              <li><a href="#">Photographie et vidéo</a></li>
              <li><a href="#">Musique </a></li>
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
  </nav>
</header>

    <!-- Section FAQ -->
    <main class="faq-section">
        <div class="container">
            <h1>Foire Aux Questions (FAQ)</h1>
            

            <!-- faq-formateur -->
            <div class="faq-formateur">
                <h2>FAQ pour les Formateurs</h2>
                <div class="All-question-reponse">
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Comment devenir formateur sur Yitro Learning ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Pour devenir formateur, rendez-vous sur la page "Devenir formateur", soumettez votre candidature avec vos qualifications et une proposition de cours. Notre équipe examinera votre dossier.</p>
                        </div>
                    </div>
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Quels types de cours puis-je proposer ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Vous pouvez proposer des cours dans n'importe quel domaine où vous avez une expertise, comme la technologie, les affaires, les langues, ou les compétences créatives. Assurez-vous que le contenu est structuré et engageant.</p>
                        </div>
                    </div>
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Comment interagir avec les apprenants ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Vous pouvez interagir avec les apprenants via des forums de discussion, des sessions de questions-réponses en direct, ou des commentaires sur les cours.</p>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
 
    <!-- ==================== FOOTER ==================== -->
    <footer class="footer">
        <div class="container">
            <!-- Liens du footer organisés en colonnes -->
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Suivez-nous</h4>
                    <ul>
                        <li><a href="#">Suivez-nous sur les Réseaux Sociaux. Restez connecté avec SK Yitro Consulting pour les dernières mises à jour et actualités. Rejoignez-nous sur nos réseaux sociaux.</a></li>  
                    </ul>
                </div>
                

                <!-- Colonne 2: Légal -->
                <div class="footer-column">
                    <h4> Yitro Learning</h4>
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

                <!-- Colonne 3: Contact -->
                <div class="footer-column">
                    <h4>Legal</h4>
                    <ul>
                    <li><a href="#">Mention légal</a></li> 
                       
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section copyright -->
        <div class="footer-bottom">
            <p>Tous droits réservés – SK Yitro Consulting © 2024</p>
        </div>
    </footer>

    <script src="../asset/js/FAQ.js"></script>
    <script>
  // JavaScript pour gérer l'animation et la visibilité de la barre de navigation
  const topNav = document.getElementById('top-nav');
  let lastScrollTop = 0;
  let isAnimating = false;
  let scrollTimeout = null;

  // Fonction de debouncing pour limiter les appels pendant le défilement
  function handleScroll() {
    if (isAnimating) return; // Évite les interférences pendant l'animation

    let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScroll > 100) {
      // Défilement vers le bas ou loin du haut
      if (!topNav.classList.contains('hidden')) {
        isAnimating = true;
        topNav.classList.add('hidden');
        // Attendre la fin de l'animation (500ms) avant d'appliquer display: none
        setTimeout(() => {
          topNav.classList.add('invisible');
          isAnimating = false;
        }, 500);
      }
    } else {
      // Près du haut de la page
      if (topNav.classList.contains('hidden') || topNav.classList.contains('invisible')) {
        isAnimating = true;
        topNav.classList.remove('invisible'); // Rétablit display: flex
        // Forcer un reflow pour que l'animation de réapparition fonctionne
        topNav.offsetHeight; // Trigger reflow
        topNav.classList.remove('hidden');
        setTimeout(() => {
          isAnimating = false;
        }, 500);
      }
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  }

  // Écouteur d'événement avec debouncing
  window.addEventListener('scroll', () => {
    if (scrollTimeout) {
      clearTimeout(scrollTimeout);
    }
    scrollTimeout = setTimeout(handleScroll, 100); // Délai de 100ms pour le debouncing
  });
</script>
    
</body>
</html>