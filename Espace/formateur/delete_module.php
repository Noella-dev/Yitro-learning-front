<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID module manquant.");
}

$id = $_GET['id'];

// Supprimer les enregistrements de la table completions liés à ce module
$stmtCompletions = $pdo->prepare("DELETE FROM completions WHERE module_id = ?");
$stmtCompletions->execute([$id]);

// Supprimer les leçons liées au module
$stmtLecons = $pdo->prepare("DELETE FROM lecons WHERE module_id = ?");
$stmtLecons->execute([$id]);

// Supprimer le module
$stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cours.php");
exit;
?>