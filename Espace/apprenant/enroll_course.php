<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Vérifier si la requête est de type POST et si l'utilisateur est connecté
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
    exit();
}

// Récupérer les données
$utilisateur_id = $_SESSION['user_id'];
$cours_id = $_POST['cours_id'] ?? null;

if (!$cours_id) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ID du cours manquant.']);
    exit();
}

try {
    // Vérifier si l'utilisateur est déjà inscrit
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inscriptions WHERE utilisateur_id = ? AND cours_id = ? AND statut_paiement = 'paye'");
    $stmt->execute([$utilisateur_id, $cours_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à ce cours.']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO inscriptions (utilisateur_id, cours_id, date_inscription, statut_paiement) VALUES (?, ?, NOW(), 'paye')");
    $stmt->execute([$utilisateur_id, $cours_id]);

    echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);

} catch (PDOException $e) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>