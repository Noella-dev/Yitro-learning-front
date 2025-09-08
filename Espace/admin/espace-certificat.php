<?php
session_start();
require_once '../config/db.php';
require_once '../../vendor/tcpdf/tcpdf.php'; // Inclure TCPDF

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../authentification/connexion-admin.php");
    exit();
}

// Récupérer les apprenants actifs
$stmt = $pdo->prepare("
    SELECT u.id, u.nom
    FROM utilisateurs u
    WHERE u.role = 'apprenant' AND u.actif = 1
    ORDER BY u.nom ASC
");
$stmt->execute();
$apprenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$download_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apprenant_id = filter_input(INPUT_POST, 'apprenant_id', FILTER_VALIDATE_INT);
    $cours_id = filter_input(INPUT_POST, 'cours_id', FILTER_VALIDATE_INT);
    $titre_certificat = trim($_POST['titre_certificat'] ?? 'Certificat de Réussite');
    $date_emission = trim($_POST['date_emission'] ?? date('Y-m-d'));

    // Validation des entrées
    if (!$apprenant_id || !$cours_id) {
        $message = "Erreur : Sélection d'apprenant ou de cours invalide.";
    } else {
        // Vérifier l'inscription et les détails
        $stmt = $pdo->prepare("
            SELECT u.nom, c.titre
            FROM utilisateurs u
            JOIN inscriptions i ON u.id = i.utilisateur_id
            JOIN cours c ON i.cours_id = c.id
            WHERE u.id = ? AND c.id = ? AND i.statut_paiement = 'paye'
        ");
        $stmt->execute([$apprenant_id, $cours_id]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$info) {
            $message = "Erreur : L'apprenant n'est pas inscrit à ce cours ou le paiement n'est pas validé.";
        } else {
            // Vérifier l'éligibilité (100% des modules complétés)
            $stmt = $pdo->prepare("SELECT COUNT(*) AS total_modules FROM modules WHERE cours_id = ?");
            $stmt->execute([$cours_id]);
            $total_modules = $stmt->fetchColumn();

            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS modules_completes
                FROM completions
                WHERE utilisateur_id = ? AND cours_id = ?
            ");
            $stmt->execute([$apprenant_id, $cours_id]);
            $modules_completes = $stmt->fetchColumn();

            if ($total_modules > 0 && $modules_completes == $total_modules) {
                // Échapper les caractères pour affichage
                $nom_apprenant = htmlspecialchars($info['nom'], ENT_QUOTES, 'UTF-8');
                $titre_cours = htmlspecialchars($info['titre'], ENT_QUOTES, 'UTF-8');
                $titre_certificat = htmlspecialchars($titre_certificat, ENT_QUOTES, 'UTF-8');
                $filename = "certificat_" . str_replace(' ', '_', $info['nom']) . "_" . str_replace(' ', '_', $info['titre']) . ".pdf";
                $output_dir = __DIR__ . '/certificats/';
                $logo_path = 'C:/xampp/htdocs/yitroLearning/asset/images/lito.jpg';
                $signature_path = 'C:/xampp/htdocs/yitroLearning/asset/images/signature.jpg'; // Chemin de la signature

                // Vérifier et créer le dossier
                if (!is_dir($output_dir)) {
                    if (!mkdir($output_dir, 0777, true)) {
                        $message = "Erreur : Impossible de créer le dossier certificats.";
                        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$_SESSION['user_id'], 'Erreur génération certificat', "Échec création dossier certificats", date('Y-m-d H:i:s')]);
                    }
                }

                // Tester l'écriture dans le dossier
                $test_file = $output_dir . 'test.txt';
                if (!file_put_contents($test_file, "Test d'écriture")) {
                    $message = "Erreur : Impossible d'écrire dans le dossier certificats. Détails : " . json_encode(error_get_last());
                    $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], 'Erreur génération certificat', "Échec écriture dossier pour {$info['nom']} - {$info['titre']}: " . json_encode(error_get_last()), date('Y-m-d H:i:s')]);
                } else {
                    unlink($test_file); // Supprimer le fichier test

                    // Vérifier le logo
                    $logo_error = '';
                    if (!file_exists($logo_path)) {
                        $logo_error = "Logo introuvable : $logo_path";
                    } elseif (!is_readable($logo_path)) {
                        $logo_error = "Logo non lisible (permissions) : $logo_path";
                    }

                    // Vérifier la signature
                    $signature_error = '';
                    if (!file_exists($signature_path)) {
                        $signature_error = "Signature introuvable : $signature_path";
                    } elseif (!is_readable($signature_path)) {
                        $signature_error = "Signature non lisible (permissions) : $signature_path";
                    }

                    // Initialiser TCPDF en paysage
                    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                    $pdf->SetCreator(PDF_CREATOR);
                    $pdf->SetAuthor('Yitro Learning');
                    $pdf->SetTitle($titre_certificat);
                    $pdf->SetMargins(15, 15, 15);
                    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                    $pdf->AddPage();

                    // Police Times pour un look élégant
                    $pdf->SetFont('times', '', 12);

                    // Bordure double
                    $pdf->SetLineStyle(array('width' => 1, 'color' => array(1, 174, 143)));
                    $pdf->Rect(15, 15, 267, 180);
                    $pdf->SetLineStyle(array('width' => 0.5, 'color' => array(1, 174, 143)));
                    $pdf->Rect(16, 16, 265, 178);

                    // Rectangle interne subtil
                    $pdf->SetLineStyle(array('width' => 0.2, 'color' => array(1, 174, 143)));
                    $pdf->Rect(25, 30, 247, 140);

                    // Filigrane (logo en transparence)
                    if ($logo_error === '') {
                        try {
                            $pdf->SetAlpha(0.07);
                            $pdf->Image($logo_path, 108.5, 65, 80, 0, '', '', 'T', false, 300, '', false, false, 0);
                            $pdf->SetAlpha(1);
                        } catch (Exception $e) {
                            // Ignorer l'erreur du filigrane
                        }
                    }

                    // Logo principal (centré en haut)
                    if ($logo_error === '') {
                        try {
                            $pdf->Image($logo_path, 118.5, 15, 60, 0, '', '', 'T', false, 300, '', false, false, 0);
                        } catch (Exception $e) {
                            $logo_error = "Erreur TCPDF pour le logo : " . $e->getMessage();
                        }
                    }
                    if ($logo_error !== '') {
                        $pdf->SetXY(118.5, 15);
                        $pdf->SetFont('times', 'B', 16);
                        $pdf->Cell(0, 10, 'Yitro Learning Logo', 0, 1, 'C');
                        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$_SESSION['user_id'], 'Erreur génération certificat', $logo_error, date('Y-m-d H:i:s')]);
                    }

                    // Lignes décoratives autour du titre
                    $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(1, 174, 143)));
                    $pdf->Line(40, 45, 257, 45);
                    $pdf->Line(40, 55, 257, 55);

                    // Contenu du certificat
                    $pdf->SetY(50);
                    $pdf->SetFont('times', 'B', 30);
                    $pdf->SetTextColor(1, 174, 143);
                    $pdf->Cell(0, 10, $titre_certificat, 0, 1, 'C');

                    $pdf->SetY(60);
                    $pdf->SetFont('times', '', 16);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(0, 10, 'Ce certificat est décerné à', 0, 1, 'C');

                    // Nom de l'apprenant (fixe, gère longs et courts)
                    $pdf->SetY(70);
                    $pdf->SetFont('times', 'B', 32);
                    $pdf->SetTextColor(1, 174, 143);
                    $pdf->MultiCell(240, 10, $nom_apprenant, 0, 'C', false, 1, 28.5, 70, true, 0, false, true, 10, 'M');

                    // Ligne décorative
                    $pdf->SetLineStyle(array('width' => 0.5, 'color' => array(1, 174, 143)));
                    $pdf->Line(50, 85, 247, 85);

                    $pdf->SetY(95);
                    $pdf->SetFont('times', '', 16);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(0, 10, 'pour avoir complété avec succès le cours', 0, 1, 'C');

                    $pdf->SetY(105);
                    $pdf->SetFont('times', 'B', 24);
                    $pdf->Cell(0, 10, $titre_cours, 0, 1, 'C');

                    $pdf->SetY(115);
                    $pdf->SetFont('times', '', 16);
                    $pdf->Cell(0, 10, 'Date d\'émission : ' . $date_emission, 0, 1, 'C');

                    // Signature (image)
                    if ($signature_error === '') {
                        try {
                            $pdf->Image($signature_path, 230, 160, 25, 0, '', '', 'T', false, 300, '', false, false, 0);
                            // Rectangle de débogage temporaire
                            $pdf->SetLineStyle(array('width' => 0.2, 'color' => array(255, 0, 0)));
                            $pdf->Rect(230, 160, 25, 10, 'D');
                        } catch (Exception $e) {
                            $signature_error = "Erreur TCPDF pour la signature : " . $e->getMessage();
                        }
                    }
                    if ($signature_error !== '') {
                        $pdf->SetXY(230, 160);
                        $pdf->SetFont('times', 'I', 14);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(0, 10, 'Signature : Yitro Learning', 0, 1, 'R');
                        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$_SESSION['user_id'], 'Erreur génération certificat', $signature_error, date('Y-m-d H:i:s')]);
                    }

                    $pdf->SetY(170);
                    $pdf->SetFont('times', 'B', 14);
                    $pdf->SetTextColor(1, 174, 143);
                    $pdf->Cell(0, 10, 'Yitro Learning', 0, 1, 'C');

                    // Sauvegarder le PDF
                    $pdf_file = $output_dir . $filename;
                    try {
                        $pdf->Output($pdf_file, 'F');
                        $download_link = "certificats/" . $filename;
                        $message = "Certificat généré pour {$info['nom']} - {$info['titre']}.";
                        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$_SESSION['user_id'], 'Génération certificat', "Certificat pour {$info['nom']} - {$info['titre']}", date('Y-m-d H:i:s')]);
                    } catch (Exception $e) {
                        $message = "Erreur : Impossible de sauvegarder le fichier PDF. Détails : " . $e->getMessage();
                        $stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details, created_at) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$_SESSION['user_id'], 'Erreur génération certificat', "Échec sauvegarde PDF pour {$info['nom']} - {$info['titre']}: " . $e->getMessage(), date('Y-m-d H:i:s')]);
                    }
                }
            } else {
                $message = "Erreur : L'apprenant n'a pas complété tous les modules du cours.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yitro Learning - Génération de Certificat</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        .main--content {
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .header--wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .header--title h2 {
            color: #01ae8f;
            font-weight: 600;
            margin: 0;
        }

        .header--title span {
            color: #777;
            font-size: 0.9rem;
        }

        .card--wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group select, .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            color: #555;
        }

        .form-group select:focus, .form-group input:focus {
            outline: none;
            border-color: #01ae8f;
            box-shadow: 0 0 5px rgba(1, 174, 143, 0.3);
        }

        .form-group button {
            background: linear-gradient(45deg, #01ae8f, #008f75);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .form-group button:hover {
            background: linear-gradient(45deg, #008f75, #01ae8f);
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .message.success {
            background: #b7f0d3;
            color: #27ae60;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
        }

        .download-link {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #01ae8f;
            text-decoration: none;
            font-weight: 500;
        }

        .download-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .main--content {
                padding: 25px;
            }

            .card--wrapper {
                padding: 15px;
            }

            .form-group select, .form-group input, .form-group button {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .main--content {
                padding: 15px;
            }

            .card--wrapper {
                padding: 10px;
            }

            .form-group select, .form-group input, .form-group button {
                font-size: 0.85rem;
            }

            .header--title h2 {
                font-size: 1.2rem;
            }

            .header--title span {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li>
                <a href="backoffice.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            </li>
            <li>
                <a href="gestion_utilisateurs/gestion_utilisateur.php"><i class="fas fa-user-cog"></i><span>Gestion utilisateur</span></a>
            </li>
            <li>
                <a href="gestion_forum.php"><i class="fas fa-comments"></i><span>Forum</span></a>
            </li>
            <li>
                <a href="progression_apprenant.php"><i class="fas fa-chart-line"></i><span>Progression apprenants</span></a>
            </li>
            <li class="active">
                <a href="espace-certificat.php"><i class="fas fa-certificate"></i><span>Générer Certificat</span></a>
            </li>
            <li class="logout">
                <a href="../../authentification/logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </li>
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Administration</span>
                <h2>Générer un Certificat</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <img src="../asset/images/lito.jpg" alt="User Profile">
            </div>
        </div>

        <div class="card--wrapper">
            <h3>Formulaire de Génération de Certificat</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="apprenant_id">Sélectionner un Apprenant</label>
                    <select name="apprenant_id" id="apprenant_id" required onchange="updateCours()">
                        <option value="">-- Choisir un apprenant --</option>
                        <?php foreach ($apprenants as $apprenant): ?>
                            <option value="<?php echo $apprenant['id']; ?>">
                                <?php echo htmlspecialchars($apprenant['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cours_id">Sélectionner un Cours</label>
                    <select name="cours_id" id="cours_id" required>
                        <option value="">-- Choisir un cours --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="titre_certificat">Titre du Certificat</label>
                    <input type="text" name="titre_certificat" id="titre_certificat" value="Certificat de Réussite" required>
                </div>
                <div class="form-group">
                    <label for="date_emission">Date d'Émission</label>
                    <input type="date" name="date_emission" id="date_emission" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit">Générer le Certificat</button>
                </div>
            </form>
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'Erreur') === false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                    <?php if ($download_link): ?>
                        <a href="<?php echo $download_link; ?>" class="download-link" download>Télécharger le Certificat</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Animations GSAP
        gsap.from(".header--wrapper", { 
            opacity: 0, 
            y: -20, 
            duration: 0.8, 
            ease: "power3.out" 
        });
        gsap.from(".card--wrapper", { 
            opacity: 0, 
            y: 30, 
            duration: 0.8, 
            ease: "power3.out",
            delay: 0.2 
        });

        // Mettre à jour la liste des cours en fonction de l'apprenant
        function updateCours() {
            const apprenantId = document.getElementById('apprenant_id').value;
            const coursSelect = document.getElementById('cours_id');
            coursSelect.innerHTML = '<option value="">-- Choisir un cours --</option>';

            if (apprenantId) {
                fetch(`get_cours_apprenant.php?apprenant_id=${apprenantId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(cours => {
                            const option = document.createElement('option');
                            option.value = cours.id;
                            option.textContent = cours.titre;
                            coursSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }
    </script>
</body>
</html>