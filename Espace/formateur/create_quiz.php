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
$stmt = $pdo->prepare("SELECT id, titre FROM cours WHERE formateur_id = ?");
$stmt->execute([$formateur_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $module_id = $_POST['module_id'];
        $titre = $_POST['titre_quiz'];
        $description = $_POST['description_quiz'];
        $score_minimum = $_POST['score_minimum'];

        // Vérifier si le module appartient au formateur
        $stmt = $pdo->prepare("
            SELECT m.id FROM modules m
            JOIN cours c ON m.cours_id = c.id
            WHERE m.id = ? AND c.formateur_id = ?
        ");
        $stmt->execute([$module_id, $formateur_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Module invalide ou non autorisé.");
        }

        // Insérer le quiz
        $stmt = $pdo->prepare("INSERT INTO quiz (module_id, titre, description, score_minimum) VALUES (?, ?, ?, ?)");
        $stmt->execute([$module_id, $titre, $description, $score_minimum]);
        $quiz_id = $pdo->lastInsertId();

        // Insérer les questions
        if (isset($_POST['questions'])) {
            $stmt = $pdo->prepare("
                INSERT INTO questions (quiz_id, texte, reponse_correcte, reponse_incorrecte_1, reponse_incorrecte_2, reponse_incorrecte_3)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($_POST['questions'] as $question) {
                $stmt->execute([
                    $quiz_id,
                    $question['texte'],
                    $question['reponse_correcte'],
                    $question['reponse_incorrecte_1'],
                    $question['reponse_incorrecte_2'],
                    $question['reponse_incorrecte_3']
                ]);
            }
        }

        header("Location: liste_quiz.php?success=Quiz créé avec succès");
        exit;
    } catch (Exception $e) {
        $error = "Erreur lors de la création du quiz : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Créer un quiz</title>
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
        h2 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
            font-family: 'Poppins', sans-serif;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 12px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        .form-control:focus {
            border-color: #01ae8f;
            box-shadow: 0 0 8px rgba(1, 174, 143, 0.2);
            outline: none;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s, background-color 0.3s;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }
        .btn-primary {
            background-color: #01ae8f;
            border: none;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #019074;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        .question-container {
            border: 1px solid #e9ecef;
            padding: 20px;
            margin-top: 20px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .question-container h5 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 10px;
        }
        .loading {
            display: none;
            font-size: 0.9em;
            color: #4a5568;
            margin-top: 5px;
            font-style: italic;
        }
        .no-modules {
            display: none;
            font-size: 0.9em;
            color: #dc3545;
            margin-top: 5px;
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
            <li>
                <a href="progression_apprenants.php"><i class="fas fa-chart-line"></i><span>Progression des apprenants</span></a>
            </li>
            <li class="active">
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
                <h2>Créer un quiz</h2>
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
        <h2>Créer un nouveau quiz</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="create_quiz.php" method="POST" id="quizForm">
            <div class="form-group">
                <label for="cours_id">Sélectionner un cours</label>
                <select name="cours_id" id="cours_id" class="form-control" required onchange="loadModules(this.value)">
                    <option value="">Choisir un cours</option>
                    <?php foreach ($cours as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($cours)): ?>
                    <div class="error">Aucun cours disponible. Veuillez créer un cours d'abord.</div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="module_id">Sélectionner un module</label>
                <select name="module_id" id="module_id" class="form-control" required>
                    <option value="">Choisir un module</option>
                </select>
                <div class="loading" id="module-loading">Chargement des modules...</div>
                <div class="no-modules" id="no-modules">Aucun module disponible pour ce cours. Veuillez créer un module d'abord.</div>
            </div>
            <div class="form-group">
                <label for="titre_quiz">Titre du quiz</label>
                <input type="text" name="titre_quiz" id="titre_quiz" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description_quiz">Description du quiz</label>
                <textarea name="description_quiz" id="description_quiz" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="score_minimum">Score minimum pour valider (%)</label>
                <input type="number" name="score_minimum" id="score_minimum" class="form-control" min="0" max="100" value="70" required>
            </div>
            <div id="questions-container"></div>
            <button type="button" class="btn btn-secondary mt-3" onclick="ajouterQuestion()">+ Ajouter une question</button>
            <button type="submit" class="btn btn-primary mt-3">Enregistrer le quiz</button>
        </form>
    </div>

    <script>
        let questionIndex = 0;

        function ajouterQuestion() {
            const questionsContainer = document.getElementById('questions-container');
            const questionHTML = `
                <div class="question-container" id="question-${questionIndex}">
                    <h5>Question ${questionIndex + 1}</h5>
                    <div class="form-group">
                        <label>Texte de la question</label>
                        <input type="text" name="questions[${questionIndex}][texte]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Réponse correcte</label>
                        <input type="text" name="questions[${questionIndex}][reponse_correcte]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Réponse incorrecte 1</label>
                        <input type="text" name="questions[${questionIndex}][reponse_incorrecte_1]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Réponse incorrecte 2</label>
                        <input type="text" name="questions[${questionIndex}][reponse_incorrecte_2]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Réponse incorrecte 3</label>
                        <input type="text" name="questions[${questionIndex}][reponse_incorrecte_3]" class="form-control" required>
                    </div>
                </div>
            `;
            questionsContainer.insertAdjacentHTML('beforeend', questionHTML);

            gsap.from(`#question-${questionIndex}`, {
                opacity: 0,
                y: 20,
                duration: 0.5,
                ease: "power2.out"
            });

            questionIndex++;
        }

        async function loadModules(coursId) {
            const moduleSelect = document.getElementById('module_id');
            const loading = document.getElementById('module-loading');
            const noModules = document.getElementById('no-modules');
            moduleSelect.innerHTML = '<option value="">Choisir un module</option>';
            loading.style.display = 'block';
            noModules.style.display = 'none';

            if (coursId) {
                try {
                    const response = await fetch(`get_modules.php?cours_id=${coursId}`);
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}`);
                    }
                    const result = await response.json();
                    loading.style.display = 'none';

                    if (result.error) {
                        noModules.textContent = result.error;
                        noModules.style.display = 'block';
                    } else if (result.length === 0) {
                        noModules.textContent = 'Aucun module disponible pour ce cours. Veuillez créer un module d\'abord.';
                        noModules.style.display = 'block';
                    } else {
                        result.forEach(module => {
                            const option = document.createElement('option');
                            option.value = module.id;
                            option.textContent = module.titre;
                            moduleSelect.appendChild(option);
                        });
                        gsap.from('#module_id', {
                            opacity: 0,
                            y: 10,
                            duration: 0.5,
                            ease: "power2.out"
                        });
                    }
                } catch (error) {
                    loading.style.display = 'none';
                    noModules.textContent = `Erreur lors du chargement des modules : ${error.message}`;
                    noModules.style.display = 'block';
                    console.error('Erreur AJAX:', error);
                }
            } else {
                loading.style.display = 'none';
            }
        }

        // Validation du formulaire
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            const titre = document.getElementById('titre_quiz').value.trim();
            const moduleId = document.getElementById('module_id').value;
            if (!titre || !moduleId) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires : Titre et Module.');
                gsap.from(".error", { opacity: 0, y: 10, duration: 0.5, ease: "power2.out" });
            }
        });

        gsap.from(".main--content", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
        gsap.from(".form-group", { opacity: 0, y: 20, duration: 0.8, stagger: 0.1, ease: "power2.out", delay: 0.2 });
        gsap.from(".btn", { opacity: 0, scale: 0.8, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)", delay: 0.5 });
    </script>
</body>
</html>