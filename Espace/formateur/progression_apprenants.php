<?php
session_start();
require_once '../config/db.php';

// Vérifier si le formateur est connecté
if (!isset($_SESSION['formateur_id'])) {
    header("Location: ../../authentification/login.php");
    exit;
}

$formateur_id = $_SESSION['formateur_id'];

// Récupérer le nom du formateur
$trainer_name = "Formateur";
if (isset($pdo)) {
    try {
        $query = "SELECT nom_prenom FROM formateurs WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $formateur_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $trainer_name = htmlspecialchars($row['nom_prenom']);
        }
    } catch (PDOException $e) {
        error_log("Erreur de requête : " . $e->getMessage());
    }
}

// Récupérer les cours du formateur
$stmt = $pdo->prepare("SELECT * FROM cours WHERE formateur_id = ?");
$stmt->execute([$formateur_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les données de progression et résultats des quiz
$progression = [];
foreach ($cours as $c) {
    // Récupérer les modules du cours
    $stmt = $pdo->prepare("SELECT id FROM modules WHERE cours_id = ?");
    $stmt->execute([$c['id']]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_modules = count($modules);

    // Récupérer les apprenants inscrits
    $stmt = $pdo->prepare("
        SELECT u.id AS utilisateur_id, u.nom AS utilisateur_nom
        FROM inscriptions i
        JOIN utilisateurs u ON i.utilisateur_id = u.id
        WHERE i.cours_id = ? AND i.statut_paiement = 'paye'
    ");
    $stmt->execute([$c['id']]);
    $apprenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $progression[$c['id']] = [
        'titre' => $c['titre'],
        'total_modules' => $total_modules,
        'apprenants' => []
    ];

    foreach ($apprenants as $apprenant) {
        // Récupérer les modules terminés
        $stmt = $pdo->prepare("
            SELECT m.titre
            FROM completions c
            JOIN modules m ON c.module_id = m.id
            WHERE c.utilisateur_id = ? AND c.cours_id = ?
        ");
        $stmt->execute([$apprenant['utilisateur_id'], $c['id']]);
        $modules_termines = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Récupérer les résultats des quiz
        $stmt = $pdo->prepare("
            SELECT q.titre AS quiz_titre, r.score, r.date, q.score_minimum
            FROM resultats_quiz r
            JOIN quiz q ON r.quiz_id = q.id
            JOIN modules m ON q.module_id = m.id
            WHERE r.utilisateur_id = ? AND m.cours_id = ?
        ");
        $stmt->execute([$apprenant['utilisateur_id'], $c['id']]);
        $resultats_quiz = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $progression[$c['id']]['apprenants'][$apprenant['utilisateur_id']] = [
            'nom' => $apprenant['utilisateur_nom'],
            'modules_termines' => $modules_termines,
            'progression' => $total_modules > 0 ? (count($modules_termines) / $total_modules) * 100 : 0,
            'resultats_quiz' => $resultats_quiz
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Progression des apprenants</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        .main--content {
            padding: 40px;
            background: #f5f7fa;
            min-height: 100vh;
        }
        h1 {
            color: #2d3748;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 40px;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border: none;
            border-radius: 16px;
            background: linear-gradient(145deg, #ffffff, #f9fbfc);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }
        .card-body {
            padding: 30px;
        }
        .card-title {
            color: #2d3748;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .progression-list {
            margin-top: 20px;
        }
        .progression-item {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: background-color 0.3s;
        }
        .progression-item:hover {
            background: #e2e8f0;
        }
        .progression-item h5 {
            color: #2d3748;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .progress-bar {
            height: 20px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .progress-bar-fill {
            height: 100%;
            background: #2ecc71;
            transition: width 0.5s ease;
        }
        .completed-modules {
            font-size: 0.95rem;
            color: #4a5568;
        }
        .completed-modules ul {
            list-style: none;
            padding: 0;
            margin-top: 10px;
        }
        .completed-modules li {
            padding: 5px 0;
            color: #2ecc71;
            font-weight: 500;
        }
        .no-progression {
            text-align: center;
            color: #4a5568;
            font-size: 1.2rem;
            font-weight: 500;
            padding: 30px;
            background: linear-gradient(145deg, #ffffff, #f9fbfc);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        .toggle-progression, .toggle-quiz {
            cursor: pointer;
            color: #3b82f6;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 10px;
            display: inline-block;
        }
        .toggle-progression:hover, .toggle-quiz:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        .progression-details, .quiz-details {
            display: none;
        }
        .progression-details.active, .quiz-details.active {
            display: block;
        }
        .quiz-results {
            margin-top: 15px;
        }
        .quiz-results h6 {
            color: #2d3748;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .quiz-result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-radius: 6px;
            background: #f1f5f9;
            margin-bottom: 8px;
            font-size: 0.85rem;
            border-left: 3px solid transparent;
        }
        .quiz-result-item.valid {
            border-left: 3px solid #2ecc71;
        }
        .quiz-result-item.invalid {
            border-left: 3px solid #dc3545;
        }
        .quiz-result-item span {
            flex: 1;
            margin-right: 10px;
        }
        .quiz-chart {
            max-width: 120px;
            max-height: 60px;
            margin-left: 10px;
        }
        .user--info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .trainer-name {
            font-size: 0.9em;
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li>
                <a href="espace_formateur.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            </li>
            <li>
                <a href="create_cours.php"><i class="fas fa-user-cog"></i><span>Créer un cours</span></a>
            </li>
            <li>
                <a href="liste_cours.php"><i class="fas fa-folder-open"></i><span>Mes cours</span></a>
            </li>
            <li class="active">
                <a href="progression_apprenants.php"><i class="fas fa-chart-line"></i><span>Progression des apprenants</span></a>
            </li>
            <li>
                <a href="liste_quiz.php"><i class="fas fa-question-circle"></i><span>Gestion des quiz</span></a>
            </li>
            <li class="logout">
                <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </li>
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Primary</span>
                <h2>Progression des apprenants</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <i class="fas fa-users"></i>
                <span class="trainer-name"><?php echo $trainer_name; ?></span>
            </div>
        </div>
        <h1>Progression des apprenants</h1>

        <?php if (empty($cours)): ?>
            <div class="no-progression">
                Aucun cours disponible. Créez un cours pour suivre la progression des apprenants !
            </div>
        <?php else: ?>
            <?php foreach ($cours as $c): ?>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo htmlspecialchars($c['titre']); ?></h4>
                        <?php if (empty($progression[$c['id']]['apprenants'])): ?>
                            <p>Aucun apprenant inscrit à ce cours.</p>
                        <?php else: ?>
                            <div class="progression-list">
                                <?php foreach ($progression[$c['id']]['apprenants'] as $utilisateur_id => $data): ?>
                                    <div class="progression-item">
                                        <h5><?php echo htmlspecialchars($data['nom']); ?></h5>
                                        <div class="progress-bar">
                                            <div class="progress-bar-fill" style="width: <?php echo $data['progression']; ?>%;"></div>
                                        </div>
                                        <p>Progression : <?php echo number_format($data['progression'], 1); ?>% (<?php echo count($data['modules_termines']); ?> / <?php echo $progression[$c['id']]['total_modules']; ?> modules terminés)</p>
                                        <div class="toggle-progression" onclick="toggleDetails(this)">Voir les détails</div>
                                        <div class="progression-details">
                                            <div class="completed-modules">
                                                <strong>Modules terminés :</strong>
                                                <?php if (empty($data['modules_termines'])): ?>
                                                    <p>Aucun module terminé.</p>
                                                <?php else: ?>
                                                    <ul>
                                                        <?php foreach ($data['modules_termines'] as $module): ?>
                                                            <li><?php echo htmlspecialchars($module); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="quiz-results">
                                            <h6>Résultats des quiz</h6>
                                            <?php if (empty($data['resultats_quiz'])): ?>
                                                <p>Aucun quiz complété.</p>
                                            <?php else: ?>
                                                <div class="toggle-quiz" onclick="toggleQuizDetails(this)">Voir les résultats des quiz</div>
                                                <div class="quiz-details">
                                                    <?php foreach ($data['resultats_quiz'] as $index => $resultat): ?>
                                                        <div class="quiz-result-item <?php echo $resultat['score'] >= $resultat['score_minimum'] ? 'valid' : 'invalid'; ?>">
                                                            <span><strong><?php echo htmlspecialchars($resultat['quiz_titre']); ?></strong></span>
                                                            <span>Score : <?php echo $resultat['score']; ?>% (Min : <?php echo $resultat['score_minimum']; ?>%)</span>
                                                            <span>Date : <?php echo date('d/m/Y', strtotime($resultat['date'])); ?></span>
                                                            <canvas id="quiz-chart-<?php echo $utilisateur_id . '-' . $c['id'] . '-' . $index; ?>" class="quiz-chart"></canvas>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetails(element) {
            const details = element.nextElementSibling;
            details.classList.toggle('active');
            element.textContent = details.classList.contains('active') ? 'Masquer les détails' : 'Voir les détails';
        }

        function toggleQuizDetails(element) {
            const details = element.nextElementSibling;
            details.classList.toggle('active');
            element.textContent = details.classList.contains('active') ? 'Masquer les résultats des quiz' : 'Voir les résultats des quiz';
        }

        // Initialiser les graphiques Chart.js pour les résultats des quiz
        <?php foreach ($cours as $c): ?>
            <?php foreach ($progression[$c['id']]['apprenants'] as $utilisateur_id => $data): ?>
                <?php foreach ($data['resultats_quiz'] as $index => $resultat): ?>
                    new Chart(document.getElementById('quiz-chart-<?php echo $utilisateur_id . '-' . $c['id'] . '-' . $index; ?>'), {
                        type: 'bar',
                        data: {
                            labels: ['Score', 'Min'],
                            datasets: [{
                                data: [<?php echo $resultat['score']; ?>, <?php echo $resultat['score_minimum']; ?>],
                                backgroundColor: [
                                    '<?php echo $resultat['score'] >= $resultat['score_minimum'] ? '#2ecc71' : '#dc3545'; ?>',
                                    '#e5e7eb'
                                ],
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    display: false // Masquer l'axe Y pour compacter
                                },
                                x: {
                                    display: false // Masquer l'axe X pour compacter
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false } // Désactiver les tooltips pour simplifier
                            }
                        }
                    });
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

        gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
        gsap.from(".card, .no-progression", { opacity: 0, y: 30, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
        gsap.from(".progression-item", { opacity: 0, y: 20, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.3 });
        gsap.from(".progress-bar-fill", { width: 0, duration: 1, ease: "power2.out", delay: 0.5 });
        gsap.from(".quiz-result-item", { opacity: 0, y: 10, duration: 0.5, stagger: 0.05, ease: "power2.out", delay: 0.6 });
    </script>
</body>
</html>