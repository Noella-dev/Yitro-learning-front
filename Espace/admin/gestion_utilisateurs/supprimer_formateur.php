<?php
session_start();
require_once '../../config/db.php';

$id = $_GET['id'];
$admin_id = $_SESSION['user_id'];

// Supprimer le formateur
$stmt = $pdo->prepare("DELETE FROM formateurs WHERE id = ?");
$stmt->execute([$id]);

// Journaliser l'action
$stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
$stmt->execute([$admin_id, 'Suppression formateur', "Formateur ID: $id"]);

header("Location: gestion_utilisateur.php");
exit;
?>