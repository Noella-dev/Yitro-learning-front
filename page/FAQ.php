<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Yitro Learning</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../assetss/images/Yitro consulting.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/FAQ.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    <!-- Section FAQ -->
    <main class="faq-section">
        <div class="container">
            <h1>Foire Aux Questions (FAQ)</h1>
            <!-- FAQ Apprenant -->
            <div class="faq-apprenant">
                <h2>FAQ pour les Apprenants</h2>
                <div class="All-question-reponse">
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Comment puis-je m'inscrire à une formation ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Pour vous inscrire à une formation, rendez-vous sur notre site web, choisissez la formation qui vous intéresse et suivez les instructions d'inscription.</p>
                        </div>
                    </div>
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Les cours sont-ils accessibles à vie ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Oui, une fois inscrit à un cours, vous y avez accès à vie, sauf indication contraire dans la description du cours. Vous pouvez revoir le contenu à tout moment.</p>
                        </div>
                    </div>
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Puis-je obtenir un certificat ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Oui, la plupart de nos cours offrent un certificat de réussite une fois que vous avez complété toutes les leçons et passé les évaluations avec succès.</p>
                        </div>
                    </div>
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Que faire si j'ai des problèmes techniques ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Si vous rencontrez des problèmes techniques, contactez notre support via l'adresse contact@yitro-consulting.com ou utilisez le formulaire de contact sur notre site.</p>
                        </div>
                    </div>
                    <div class="faq-quest-rep">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h3>Y a-t-il des prérequis pour les cours ?</h3>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-reponse">
                            <p>Les prérequis varient selon les cours. Consultez la description de chaque cours pour connaître les compétences ou connaissances requises avant de vous inscrire.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Formateur -->
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

    <script src="../assets/js/FAQ.js"></script>
</body>
</html>