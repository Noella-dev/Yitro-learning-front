    <?php
    require_once '../../config/db.php';

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM formateurs WHERE id = ?");
    $stmt->execute([$id]);
    $formateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$formateur) {
        header("Location: gestion_utilisateur.php");
        exit;
    }

    // Récupérer les cours du formateur
    $stmt = $pdo->prepare("SELECT * FROM cours WHERE formateur_id = ?");
    $stmt->execute([$id]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pour chaque cours, récupérer les modules et leçons
    foreach ($cours as &$c) {
        $stmt = $pdo->prepare("SELECT * FROM modules WHERE cours_id = ?");
        $stmt->execute([$c['id']]);
        $c['modules'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($c['modules'] as &$m) {
            $stmt = $pdo->prepare("SELECT * FROM lecons WHERE module_id = ?");
            $stmt->execute([$m['id']]);
            $m['lecons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Yitro Learning | Contrôle Qualité</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
        <style>
            /* Styles pour la modale des modules */
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }
            .modal-content {
                background: #fff;
                border-radius: 12px;
                padding: 30px;
                max-width: 900px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
                position: relative;
            }
            .modal-content h3 {
                color: #333;
                font-weight: 600;
                margin-bottom: 20px;
            }
            .close-modal {
                position: absolute;
                top: 15px;
                right: 15px;
                font-size: 24px;
                color: #555;
                cursor: pointer;
                transition: color 0.3s;
            }
            .close-modal:hover {
                color: #01ae8f;
            }
            .modal .table--wrapper {
                margin-bottom: 20px;
            }
            .modal .table th, .modal .table td {
                padding: 12px;
                vertical-align: top;
            }
            .lecons-list {
                list-style-type: disc;
                margin: 0;
                padding-left: 20px;
            }
            .lecons-list li {
                margin-bottom: 8px;
                color: #333;
            }
            .lecons-list .btn-action {
                margin-left: 10px;
            }
            .no-data {
                color: #6b7280;
                font-style: italic;
            }

            /* Styles pour la modale des leçons */
            .lesson-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 2000;
                justify-content: center;
                align-items: center;
            }
            .lesson-modal-content {
                background: #fff;
                border-radius: 16px;
                padding: 30px;
                max-width: 800px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                position: relative;
                text-align: center;
            }
            .lesson-modal-content h3 {
                color: #333;
                font-weight: 600;
                margin-bottom: 20px;
                font-size: 1.5em;
            }
            .close-lesson-modal {
                position: absolute;
                top: 15px;
                right: 15px;
                font-size: 28px;
                color: #555;
                cursor: pointer;
                transition: color 0.3s;
            }
            .close-lesson-modal:hover {
                color: #01ae8f;
            }
            .lesson-content video,
            .lesson-content audio,
            .lesson-content iframe {
                width: 100%;
                max-height: 500px;
                border-radius: 8px;
                border: none;
                margin-top: 10px;
            }
            .lesson-content audio {
                max-height: 50px;
            }
            .lesson-content iframe {
                height: 600px;
            }
            .lesson-content {
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Formateur</span>
                    <h2>Contrôle Qualité de <?= htmlspecialchars($formateur['nom_prenom']) ?></h2>
                </div>
                <a href="gestion_utilisateur.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
            </div>
            <div class="card--container">
                <h3>Cours Proposés</h3>
                <div class="table--wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Prix</th>
                                <th>Photo</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cours as $c): ?>
                                <tr class="table--row">
                                    <td><?= htmlspecialchars($c['id']) ?></td>
                                    <td><?= htmlspecialchars($c['titre']) ?></td>
                                    <td><?= htmlspecialchars($c['description']) ?></td>
                                    <td><?= htmlspecialchars($c['prix']) ?> €</td>
                                    <td>
                                        <?php if ($c['photo']): ?>
                                            <img src="../../../Uploads/cours/<?= htmlspecialchars($c['photo']) ?>" alt="Photo du cours" style="max-width: 100px; border-radius: 50%;">
                                        <?php else: ?>
                                            Aucune photo
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-view" onclick="showModal('modal-<?= $c['id'] ?>')">
                                            <i class="fas fa-eye"></i> Voir en détail
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modales pour chaque cours -->
            <?php foreach ($cours as $c): ?>
                <div id="modal-<?= $c['id'] ?>" class="modal">
                    <div class="modal-content">
                        <span class="close-modal" onclick="hideModal('modal-<?= $c['id'] ?>')">×</span>
                        <h3>Détails du cours : <?= htmlspecialchars($c['titre']) ?></h3>
                        <div class="table--wrapper">
                            <h4>Modules et Leçons</h4>
                            <?php if (empty($c['modules'])): ?>
                                <p class="no-data">Aucun module disponible.</p>
                            <?php else: ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Titre</th>
                                            <th>Description</th>
                                            <th>Leçons</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($c['modules'] as $m): ?>
                                            <tr class="table--row">
                                                <td><?= htmlspecialchars($m['id']) ?></td>
                                                <td><?= htmlspecialchars($m['titre']) ?></td>
                                                <td><?= htmlspecialchars($m['description']) ?></td>
                                                <td>
                                                    <?php if (empty($m['lecons'])): ?>
                                                        <span class="no-data">Aucune leçon</span>
                                                    <?php else: ?>
                                                        <ul class="lecons-list">
                                                            <?php foreach ($m['lecons'] as $l): ?>
                                                                <li>
                                                                    <?= htmlspecialchars($l['titre']) ?> 
                                                                    (<?= htmlspecialchars(strtoupper($l['format'])) ?>)                                                            
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Modales pour chaque leçon -->
                <?php foreach ($c['modules'] as $m): ?>
                    <?php foreach ($m['lecons'] as $l): ?>
                        <div id="lesson-modal-<?= $c['id'] ?>-<?= $m['id'] ?>-<?= $l['id'] ?>" class="lesson-modal">
                            <div class="lesson-modal-content">
                                <span class="close-lesson-modal" onclick="hideLessonModal('lesson-modal-<?= $c['id'] ?>-<?= $m['id'] ?>-<?= $l['id'] ?>')">×</span>
                                <h3>Leçon : <?= htmlspecialchars($l['titre']) ?> (<?= htmlspecialchars(strtoupper($l['format'])) ?>)</h3>
                                <div class="lesson-content">
                                    <?php if (strtolower($l['format']) === 'video'): ?>
                                        <video controls>
                                            <source src="../../Uploads/lecons/<?= htmlspecialchars($l['fichier']) ?>" type="video/mp4">
                                            Votre navigateur ne prend pas en charge la lecture de vidéos.
                                        </video>
                                    <?php elseif (strtolower($l['format']) === 'audio'): ?>
                                        <audio controls>
                                            <source src="../../Uploads/lecons/<?= htmlspecialchars($l['fichier']) ?>" type="audio/mpeg">
                                            Votre navigateur ne prend pas en charge la lecture d'audio.
                                        </audio>
                                    <?php elseif (strtolower($l['format']) === 'pdf'): ?>
                                        <iframe src="../../Uploads/lecons/<?= htmlspecialchars($l['fichier']) ?>" title="Visualiseur PDF"></iframe>
                                    <?php else: ?>
                                        <p>Format non pris en charge.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <script>
            // Afficher la modale des modules
            function showModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.style.display = 'flex';
                gsap.fromTo(modal.querySelector('.modal-content'), 
                    { opacity: 0, scale: 0.8, y: 50 },
                    { opacity: 1, scale: 1, y: 0, duration: 0.5, ease: 'power3.out' }
                );
            }

            // Masquer la modale des modules
            function hideModal(modalId) {
                const modal = document.getElementById(modalId);
                gsap.to(modal.querySelector('.modal-content'), {
                    opacity: 0,
                    scale: 0.8,
                    y: 50,
                    duration: 0.3,
                    ease: 'power3.in',
                    onComplete: () => {
                        modal.style.display = 'none';
                    }
                });
            }

            // Afficher la modale des leçons
            function showLessonModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.style.display = 'flex';
                gsap.fromTo(modal.querySelector('.lesson-modal-content'), 
                    { opacity: 0, scale: 0.9, y: 30 },
                    { opacity: 1, scale: 1, y: 0, duration: 0.4, ease: 'power3.out' }
                );
            }

            // Masquer la modale des leçons
            function hideLessonModal(modalId) {
                const modal = document.getElementById(modalId);
                gsap.to(modal.querySelector('.lesson-modal-content'), {
                    opacity: 0,
                    scale: 0.9,
                    y: 30,
                    duration: 0.3,
                    ease: 'power3.in',
                    onComplete: () => {
                        modal.style.display = 'none';
                    }
                });
            }

            // Animations GSAP pour le tableau principal
            gsap.from(".table--wrapper", { 
                opacity: 0, 
                y: 50, 
                duration: 1, 
                ease: "power3.out" 
            });
            gsap.from(".table--row", { 
                opacity: 0, 
                x: -20, 
                duration: 0.8, 
                stagger: 0.05, 
                ease: "power2.out",
                delay: 0.5 
            });
        </script>
    </body>
    </html>