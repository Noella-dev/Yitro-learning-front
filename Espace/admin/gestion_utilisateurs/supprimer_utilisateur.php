<?php
session_start();
require_once '../../config/db.php';

$id = $_GET['id'];
$admin_id = $_SESSION['user_id'];

// Supprimer l'utilisateur
$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);

// Journaliser l'action
$stmt = $pdo->prepare("INSERT INTO journal_activite (admin_id, action, details) VALUES (?, ?, ?)");
$stmt->execute([$admin_id, 'Suppression utilisateur', "Utilisateur ID: $id"]);

header("Location: gestion_utilisateur.php");
exit;
?>