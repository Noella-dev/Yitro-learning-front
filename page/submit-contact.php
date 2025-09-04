<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['formateur_id'])) {
    header("Location: ../../authentification/login.php");
    exit;
}

$formateur_id = $_SESSION['formateur_id'];
$upload_dir = '../../Uploads/cours/';
$lecon_upload_dir = '../../Uploads/lecons/';
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
$allowed_lecon_types = ['application/pdf', 'audio/mpeg', 'video/mp4', 'video/x-mp4', 'video/mpeg'];
$max_size = 10 * 1024 * 1024; // 10MB pour tester

try {
    // Créer les dossiers d'upload s'ils n'existent pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!is_dir($lecon_upload_dir)) {
        mkdir($lecon_upload_dir, 0755, true);
    }

    // Débogage temporaire
    // echo "<pre>";
    // print_r($_FILES);
    // echo "</pre>";

    // Vérifier les champs principaux
    if (!isset($_POST['titre_cours']) || !isset($_POST['description_cours']) || !isset($_POST['prix_cours'])) {
        throw new Exception("Tous les champs obligatoires doivent être remplis : Titre, Description, Prix.");
    }

    $titre = $_POST['titre_cours'];
    $description = $_POST['description_cours'];
    $prix = $_POST['prix_cours'];
    $photo = null;

    // Gérer l'upload de la photo
    if (isset($_FILES['photo_cours']) && $_FILES['photo_cours']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo_cours'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = $file['type'];

        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $photo = 'course_' . time() . '.' . $extension;
            $dest = $upload_dir . $photo;

            if (!move_uploaded_file($file_tmp, $dest)) {
                throw new Exception("Échec de l'upload de la photo vers $dest.");
            }
        } else {
            throw new Exception("Type de fichier non autorisé ($file_type) ou taille excessive ($file_size octets) pour la photo.");
        }
    }

    // Insérer le cours
    $stmt = $pdo->prepare("INSERT INTO cours (formateur_id, titre, description, prix, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$formateur_id, $titre, $description, $prix, $photo]);
    $cours_id = $pdo->lastInsertId();

    // Gérer les modules et leçons
    if (isset($_POST['modules']) && is_array($_POST['modules'])) {
        foreach ($_POST['modules'] as $module_index => $module) {
            if (!isset($module['titre']) || !isset($module['description'])) {
                continue;
            }

            $module_titre = $module['titre'];
            $module_description = $module['description'];

            // Insérer le module
            $stmt = $pdo->prepare("INSERT INTO modules (cours_id, titre, description) VALUES (?, ?, ?)");
            $stmt->execute([$cours_id, $module_titre, $module_description]);
            $module_id = $pdo->lastInsertId();

            // Gérer les leçons
            if (isset($module['lecons']) && is_array($module['lecons'])) {
                foreach ($module['lecons'] as $lecon_index => $lecon) {
                    if (!isset($lecon['titre']) || !isset($lecon['format']) || !isset($_FILES['modules']['name'][$module_index]['lecons'][$lecon_index]['fichier'])) {
                        continue;
                    }

                    $lecon_titre = $lecon['titre'];
                    $lecon_format = $lecon['format'];
                    $lecon_file = $_FILES['modules']['name'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_tmp = $_FILES['modules']['tmp_name'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_size = $_FILES['modules']['size'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_type = $_FILES['modules']['type'][$module_index]['lecons'][$lecon_index]['fichier'];
                    $lecon_error = $_FILES['modules']['error'][$module_index]['lecons'][$lecon_index]['fichier'];

                    if ($lecon_file && $lecon_error === UPLOAD_ERR_OK && $lecon_size <= $max_size && in_array($lecon_type, $allowed_lecon_types)) {
                        $lecon_extension = pathinfo($lecon_file, PATHINFO_EXTENSION);
                        $lecon_filename = 'lecon_' . $cours_id . '_' . $module_id . '_' . time() . '.' . $lecon_extension;
                        $lecon_dest = $lecon_upload_dir . $lecon_filename;

                        if (move_uploaded_file($lecon_tmp, $lecon_dest)) {
                            $stmt = $pdo->prepare("INSERT INTO lecons (module_id, titre, format, fichier) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$module_id, $lecon_titre, $lecon_format, $lecon_filename]);
                        } else {
                            throw new Exception("Échec de l'upload du fichier de la leçon vers $lecon_dest.");
                        }
                    } else {
                        throw new Exception("Type de fichier non autorisé ($lecon_type), erreur ($lecon_error), ou taille excessive ($lecon_size octets) pour la leçon.");
                    }
                }
            }
        }
    }

    header("Location: liste_cours.php");
    exit;
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>