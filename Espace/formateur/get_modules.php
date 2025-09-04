<?php
session_start();
require_once '../config/db.php';

// Vérifier si le formateur est connecté
if (!isset($_SESSION['formateur_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Utilisateur non authentifié']);
    exit;
}

$formateur_id = $_SESSION['formateur_id'];

if (!isset($_GET['cours_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du cours manquant']);
    exit;
}

$cours_id = filter_var($_GET['cours_id'], FILTER_VALIDATE_INT);
if ($cours_id === false || $cours_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du cours invalide']);
    exit;
}

try {
    // Vérifier que le cours appartient au formateur
    $stmt = $pdo->prepare("SELECT id FROM cours WHERE id = ? AND formateur_id = ?");
    $stmt->execute([$cours_id, $formateur_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Cours non autorisé ou introuvable']);
        exit;
    }

    // Récupérer les modules
    $stmt = $pdo->prepare("SELECT id, titre FROM modules WHERE cours_id = ?");
    $stmt->execute([$cours_id]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($modules);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
    error_log("Erreur dans get_modules.php : " . $e->getMessage());
}
?>