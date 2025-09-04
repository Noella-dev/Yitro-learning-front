
<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

// Vérifier si les données POST sont présentes
if (!isset($_POST['module_id']) || !isset($_POST['cours_id']) || !isset($_POST['is_checked'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

$utilisateur_id = $_SESSION['user_id'];
$module_id = (int)$_POST['module_id'];
$cours_id = (int)$_POST['cours_id'];
$is_checked = filter_var($_POST['is_checked'], FILTER_VALIDATE_BOOLEAN);

// Vérifier si le module appartient au cours
$stmt = $pdo->prepare("SELECT id FROM modules WHERE id = ? AND cours_id = ?");
$stmt->execute([$module_id, $cours_id]);
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$module) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Module ou cours invalide']);
    exit();
}

try {
    if ($is_checked) {
        // Ajouter la complétion
        $stmt = $pdo->prepare("INSERT INTO completions (utilisateur_id, module_id, cours_id) VALUES (?, ?, ?)");
        $stmt->execute([$utilisateur_id, $module_id, $cours_id]);
        echo json_encode(['success' => true, 'message' => 'Module marqué comme terminé !']);
    } else {
        // Supprimer la complétion
        $stmt = $pdo->prepare("DELETE FROM completions WHERE utilisateur_id = ? AND module_id = ?");
        $stmt->execute([$utilisateur_id, $module_id]);
        echo json_encode(['success' => true, 'message' => 'Complétion annulée']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>
