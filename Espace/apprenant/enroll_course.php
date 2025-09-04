<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

$utilisateur_id = $_POST['utilisateur_id'] ?? null;
$cours_id = $_POST['cours_id'] ?? null;

if (!$utilisateur_id || !$cours_id) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

// Vérifier si l'utilisateur est déjà inscrit
$stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE utilisateur_id = ? AND cours_id = ?");
$stmt->execute([$utilisateur_id, $cours_id]);
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à ce cours']);
    exit();
}

// Insérer l'inscription
$stmt = $pdo->prepare("INSERT INTO inscriptions (utilisateur_id, cours_id, statut_paiement) VALUES (?, ?, 'paye')");
try {
    $stmt->execute([$utilisateur_id, $cours_id]);
    echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]);
}
?>