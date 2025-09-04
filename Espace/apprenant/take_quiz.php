<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Récupérer le nom de l'utilisateur
$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../authentification/login.php");
    exit();
}

// Vérifier si l'ID du quiz est fourni
if (!isset($_GET['id'])) {
    header("Location: espace_apprenant.php");
    exit();
}

$quiz_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($quiz_id === false || $quiz_id <= 0) {
    header("Location: espace_apprenant.php?error=ID du quiz invalide");
    exit();
}

// Récupérer les détails du quiz et du cours
$stmt = $pdo->prepare("
    SELECT q.*, m.cours_id, c.titre AS cours_titre, m.titre AS module_titre, c.prix
    FROM quiz q
    JOIN modules m ON q.module_id = m.id
    JOIN cours c ON m.cours_id = c.id
    WHERE q.id = ?
");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header("Location: espace_apprenant.php?error=Quiz non trouvé");
    exit();
}

// Vérifier si l'utilisateur a accès au cours
$stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE utilisateur_id = ? AND cours_id = ? AND statut_paiement = 'paye'");
$stmt->execute([$_SESSION['user_id'], $quiz['cours_id']]);
$is_enrolled = $stmt->fetch(PDO::FETCH_ASSOC) !== false;
$is_free = $quiz['prix'] == 0;
$can_access = $is_free || $is_enrolled;

if (!$can_access) {
    header("Location: espace_apprenant.php?error=Accès non autorisé au quiz");
    exit();
}

// Récupérer les questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la soumission finale via AJAX
$result_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_submission'])) {
    try {
        $score = 0;
        $total_questions = count($questions);
        $answers = json_decode($_POST['answers'], true);

        if ($total_questions > 0 && is_array($answers)) {
            foreach ($questions as $index => $question) {
                if (isset($answers[$index]) && $answers[$index] === $question['reponse_correcte']) {
                    $score++;
                }
            }
            $score_percentage = ($score / $total_questions) * 100;

            // Enregistrer le résultat
            $stmt = $pdo->prepare("
                INSERT INTO resultats_quiz (utilisateur_id, quiz_id, score, date)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$_SESSION['user_id'], $quiz_id, $score_percentage]);

            $result_message = $score_percentage >= $quiz['score_minimum'] ?
                "Félicitations ! Vous avez obtenu $score_percentage% (Score minimum : {$quiz['score_minimum']}%)." :
                "Vous avez obtenu $score_percentage%. Le score minimum requis est {$quiz['score_minimum']}%. Veuillez réessayer.";
            echo json_encode(['success' => true, 'message' => $result_message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucune question ou réponses invalides.']);
        }
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du résultat : ' . htmlspecialchars($e->getMessage())]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer le quiz - Yitro Learning</title>
    <link rel="stylesheet" href="../../asset/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
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

        .quiz-section {
            padding: 40px 20px;
            background: #f3f3f3;
            min-height: 100vh;
        }

        .quiz-section .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .quiz-section h1 {
            font-size: 2em;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .quiz-section .course-info {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .question {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: none;
        }

        .question.active {
            display: block;
        }

        .question h3 {
            font-size: 1.2em;
            color: #34495e;
            margin-bottom: 15px;
        }

        .question .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .question .options label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95em;
            color: #2c3e50;
            cursor: pointer;
        }

        .question .options input[type="radio"] {
            accent-color: #01ae8f;
            width: 18px;
            height: 18px;
        }

        .btn-next, .btn-submit {
            background: #01ae8f;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-right: 10px;
        }

        .btn-next:hover, .btn-submit:hover {
            background: #019074;
            transform: scale(1.03);
        }

        .btn-next:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
        }

        .result-message {
            font-size: 1em;
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .result-message.success {
            background: #dff0d8;
            color: #2ecc71;
        }

        .result-message.error {
            background: #f2dede;
            color: #e74c3c;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-left: 10px;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: scale(1.03);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .quiz-section h1 {
                font-size: 1.8em;
                text-align: center;
            }

            .quiz-section .course-info {
                text-align: center;
            }

            .question h3 {
                font-size: 1.1em;
            }
        }

        @media (max-width: 480px) {
            .quiz-section {
                padding: 20px 10px;
            }

            .btn-next, .btn-submit, .btn-back {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="logo">
                <img src="https://yitro-consulting.com/wp-content/uploads/2024/02/Capture-decran-le-2024-02-19-a-16.39.58.png" alt="Yitro E-Learning">
                <a href="espace_apprenant.php" class="logo-text">Yitro Learning</a>
            </div>
            <ul class="nav-list">
                <li class="dropdown">
                    <a href="#">Catalogues</a>
                </li>
                <li class="dropdown">
                    <a href="mes_cours.php">Mes cours</a>
                </li>
            </ul>
            <div class="auth-links">
                <ul class="nav-list">
                    <li>
                        <a href="#" class="btn-primary" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($user['nom']); ?>
                        </a>
                    </li>
                    <li><a href="../../authentification/logout.php" class="btn-primary">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="quiz-section">
        <div class="container">
            <h1><?php echo htmlspecialchars($quiz['titre']); ?></h1>
            <div class="course-info">
                Cours : <?php echo htmlspecialchars($quiz['cours_titre']); ?> | Module : <?php echo htmlspecialchars($quiz['module_titre']); ?>
            </div>
            <div class="result-message" id="resultMessage" style="display: none;"></div>
            <form id="quizForm">
                <?php if (empty($questions)): ?>
                    <p>Aucune question disponible pour ce quiz.</p>
                <?php else: ?>
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="question" data-question-index="<?php echo $index; ?>">
                            <h3><?php echo ($index + 1) . '. ' . htmlspecialchars($q['texte']); ?></h3>
                            <div class="options">
                                <?php
                                // Mélanger les réponses
                                $options = [
                                    $q['reponse_correcte'],
                                    $q['reponse_incorrecte_1'],
                                    $q['reponse_incorrecte_2'],
                                    $q['reponse_incorrecte_3']
                                ];
                                shuffle($options);
                                foreach ($options as $option): ?>
                                    <label>
                                        <input type="radio" name="answer_<?php echo $index; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                        <?php echo htmlspecialchars($option); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-next" id="nextButton" disabled>Suivant</button>
                    <button type="button" class="btn-submit" id="submitButton" style="display: none;">Soumettre</button>
                    <a href="cours_details.php?id=<?php echo $quiz['cours_id']; ?>" class="btn-back">Retour au cours</a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <script>
        // Gestion des questions une par une
        const questions = document.querySelectorAll('.question');
        const nextButton = document.getElementById('nextButton');
        const submitButton = document.getElementById('submitButton');
        const resultMessage = document.getElementById('resultMessage');
        let currentQuestionIndex = 0;
        const answers = [];

        if (questions.length > 0) {
            // Afficher la première question
            questions[0].classList.add('active');

            questions.forEach((question, index) => {
                const radios = question.querySelectorAll('input[type="radio"]');
                radios.forEach(radio => {
                    radio.addEventListener('change', () => {
                        answers[index] = radio.value;
                        nextButton.disabled = false;
                    });
                });
            });

            nextButton.addEventListener('click', () => {
                if (currentQuestionIndex < questions.length - 1) {
                    questions[currentQuestionIndex].classList.remove('active');
                    currentQuestionIndex++;
                    questions[currentQuestionIndex].classList.add('active');
                    nextButton.disabled = !answers[currentQuestionIndex];
                    gsap.from(questions[currentQuestionIndex], {
                        opacity: 0,
                        x: 50,
                        duration: 0.5,
                        ease: "power2.out"
                    });

                    if (currentQuestionIndex === questions.length - 1) {
                        nextButton.style.display = 'none';
                        submitButton.style.display = 'inline-block';
                    }
                }
            });

            submitButton.addEventListener('click', () => {
                if (answers.length < questions.length) {
                    alert('Veuillez répondre à toutes les questions avant de soumettre.');
                    return;
                }

                // Envoyer les réponses via AJAX
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ajax_submission=1&answers=${encodeURIComponent(JSON.stringify(answers))}`
                })
                .then(response => response.json())
                .then(data => {
                    resultMessage.style.display = 'block';
                    resultMessage.className = 'result-message';
                    resultMessage.classList.add(data.success ? 'success' : 'error');
                    resultMessage.textContent = data.message;
                    submitButton.style.display = 'none';
                    nextButton.style.display = 'none';
                })
                .catch(error => {
                    resultMessage.style.display = 'block';
                    resultMessage.className = 'result-message error';
                    resultMessage.textContent = 'Erreur réseau : ' + error.message;
                });
            });
        }

        // Animation GSAP
        gsap.from(".quiz-section h1", { opacity: 0, y: 50, duration: 1, ease: "power3.out" });
        gsap.from(".question.active", {
            opacity: 0,
            x: 50,
            duration: 0.8,
            ease: "power2.out",
            scrollTrigger: {
                trigger: ".question.active",
                start: "top 80%",
            }
        });
        gsap.from(".btn-next, .btn-submit, .btn-back", {
            opacity: 0,
            scale: 0.8,
            duration: 0.5,
            stagger: 0.1,
            ease: "back.out(1.7)",
            delay: 0.5,
            onComplete: () => {
                gsap.to(".btn-next, .btn-submit, .btn-back", {
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