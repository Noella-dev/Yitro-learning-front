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

// Vérifier si l'ID du quiz est fourni
if (!isset($_GET['id'])) {
    header("Location: liste_quiz.php?error=ID du quiz manquant");
    exit;
}

$quiz_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($quiz_id === false || $quiz_id <= 0) {
    header("Location: liste_quiz.php?error=ID du quiz invalide");
    exit;
}

// Récupérer les informations du quiz
$stmt = $pdo->prepare("
    SELECT q.*, m.cours_id, c.titre AS cours_titre, m.titre AS module_titre
    FROM quiz q
    JOIN modules m ON q.module_id = m.id
    JOIN cours c ON m.cours_id = c.id
    WHERE q.id = ? AND c.formateur_id = ?
");
$stmt->execute([$quiz_id, $formateur_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header("Location: liste_quiz.php?error=Quiz non trouvé ou non autorisé");
    exit;
}

// Récupérer les questions du quiz
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les cours du formateur
$stmt = $pdo->prepare("SELECT id, titre FROM cours WHERE formateur_id = ?");
$stmt->execute([$formateur_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les modules du cours associé au quiz
$stmt = $pdo->prepare("SELECT id, titre FROM modules WHERE cours_id = ?");
$stmt->execute([$quiz['cours_id']]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        // Mettre à jour le quiz
        $stmt = $pdo->prepare("UPDATE quiz SET module_id = ?, titre = ?, description = ?, score_minimum = ? WHERE id = ?");
        $stmt->execute([$module_id, $titre, $description, $score_minimum, $quiz_id]);

        // Supprimer les anciennes questions
        $stmt = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);

        // Insérer les nouvelles questions
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

        header("Location: liste_quiz.php?success=Quiz mis à jour avec succès");
        exit;
    } catch (Exception $e) {
        $error = "Erreur lors de la mise à jour du quiz : " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Modifier un quiz</title>
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
            margin: 0 5px;
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
        .btn-danger {
            background-color: #dc3545;
            border: none;
            color: #fff;
        }
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .question-container {
            border: 1px solid #e9ecef;
            padding: 20px;
            margin-top: 20px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            position: relative;
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
        .remove-question {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #dc3545;
            font-size: 1.2em;
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
                <h2>Modifier un quiz</h2>
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
        <h2>Modifier le quiz : <?php echo htmlspecialchars($quiz['titre']); ?></h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="edit_quiz.php?id=<?php echo $quiz_id; ?>" method="POST" id="quizForm">
            <div class="form-group">
                <label for="cours_id">Sélectionner un cours</label>
                <select name="cours_id" id="cours_id" class="form-control" required onchange="loadModules(this.value)">
                    <option value="">Choisir un cours</option>
                    <?php foreach ($cours as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $quiz['cours_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['titre']); ?>
                        </option>
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
                    <?php foreach ($modules as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo $m['id'] == $quiz['module_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($m['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="loading" id="module-loading">Chargement des modules...</div>
                <div class="no-modules" id="no-modules">Aucun module disponible pour ce cours. Veuillez créer un module d'abord.</div>
            </div>
            <div class="form-group">
                <label for="titre_quiz">Titre du quiz</label>
                <input type="text" name="titre_quiz" id="titre_quiz" class="form-control" value="<?php echo htmlspecialchars($quiz['titre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description_quiz">Description du quiz</label>
                <textarea name="description_quiz" id="description_quiz" class="form-control" rows="3"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="score_minimum">Score minimum pour valider (%)</label>
                <input type="number" name="score_minimum" id="score_minimum" class="form-control" min="0" max="100" value="<?php echo htmlspecialchars($quiz['score_minimum']); ?>" required>
            </div>
            <div id="questions-container">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-container" id="question-<?php echo $index; ?>">
                        <h5>Question <?php echo $index + 1; ?></h5>
                        <i class="fas fa-trash remove-question" onclick="removeQuestion(<?php echo $index; ?>)"></i>
                        <div class="form-group">
                            <label>Texte de la question</label>
                            <input type="text" name="questions[<?php echo $index; ?>][texte]" class="form-control" value="<?php echo htmlspecialchars($q['texte']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Réponse correcte</label>
                            <input type="text" name="questions[<?php echo $index; ?>][reponse_correcte]" class="form-control" value="<?php echo htmlspecialchars($q['reponse_correcte']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Réponse incorrecte 1</label>
                            <input type="text" name="questions[<?php echo $index; ?>][reponse_incorrecte_1]" class="form-control" value="<?php echo htmlspecialchars($q['reponse_incorrecte_1']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Réponse incorrecte 2</label>
                            <input type="text" name="questions[<?php echo $index; ?>][reponse_incorrecte_2]" class="form-control" value="<?php echo htmlspecialchars($q['reponse_incorrecte_2']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Réponse incorrecte 3</label>
                            <input type="text" name="questions[<?php echo $index; ?>][reponse_incorrecte_3]" class="form-control" value="<?php echo htmlspecialchars($q['reponse_incorrecte_3']); ?>" required>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-secondary mt-3" onclick="ajouterQuestion()">+ Ajouter une question</button>
            <button type="submit" class="btn btn-primary mt-3">Enregistrer les modifications</button>
            <a href="liste_quiz.php" class="btn btn-secondary mt-3">Annuler</a>
        </form>
    </div>

    <script>
        let questionIndex = <?php echo count($questions); ?>;

        function ajouterQuestion() {
            const questionsContainer = document.getElementById('questions-container');
            const questionHTML = `
                <div class="question-container" id="question-${questionIndex}">
                    <h5>Question ${questionIndex + 1}</h5>
                    <i class="fas fa-trash remove-question" onclick="removeQuestion(${questionIndex})"></i>
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

        function removeQuestion(index) {
            const questionElement = document.getElementById(`question-${index}`);
            gsap.to(questionElement, {
                opacity: 0,
                y: -20,
                duration: 0.3,
                ease: "power2.in",
                onComplete: () => questionElement.remove()
            });
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
        gsap.from(".btn", {
            opacity: 0,
            scale: 0.8,
            duration: 0.5,
            stagger: 0.1,
            ease: "back.out(1.7)",
            delay: 0.5,
            onComplete: () => {
                gsap.to(".btn-primary, .btn-secondary, .btn-danger", {
                    scale: 1.1,
                    duration: 0.2,
                    repeat: -1,
                    yoyo: true,
                    ease: "power1.inOut",
                    paused: true,
                    onStart: function() { this.targets().forEach(btn => btn.addEventListener('mouseenter', () => this.play())) },
                    onComplete: function() { this.targets().forEach(btn => btn.addEventListener('mouseleave', () => this.pause())) }
                });
            }
        });
    </script>
</body>
</html>