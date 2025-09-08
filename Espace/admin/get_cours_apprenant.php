<?php
require_once '../config/db.php';

if (isset($_GET['apprenant_id'])) {
    $apprenant_id = intval($_GET['apprenant_id']);
    $stmt = $pdo->prepare("
        SELECT c.id, c.titre
        FROM inscriptions i
        JOIN cours c ON i.cours_id = c.id
        WHERE i.utilisateur_id = ? AND i.statut_paiement = 'paye'
    ");
    $stmt->execute([$apprenant_id]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($cours);
}
?>