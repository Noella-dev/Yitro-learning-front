<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID cours manquant.");
}

$id = $_GET['id'];

// Supprimer les enregistrements de la table inscriptions liés à ce cours
$stmtInscriptions = $pdo->prepare("DELETE FROM inscriptions WHERE cours_id = ?");
$stmtInscriptions->execute([$id]);

// Supprimer les enregistrements de la table completions liés aux modules de ce cours
$stmtCompletions = $pdo->prepare("
    DELETE completions FROM completions 
    INNER JOIN modules ON completions.module_id = modules.id 
    WHERE modules.cours_id = ?
");
$stmtCompletions->execute([$id]);

// Supprimer les leçons liées aux modules de ce cours
$stmtLecons = $pdo->prepare("
    DELETE lecons FROM lecons 
    INNER JOIN modules ON lecons.module_id = modules.id 
    WHERE modules.cours_id = ?
");
$stmtLecons->execute([$id]);

// Supprimer les modules
$stmtModules = $pdo->prepare("DELETE FROM modules WHERE cours_id = ?");
$stmtModules->execute([$id]);

// Supprimer le cours
$stmt = $pdo->prepare("DELETE FROM cours WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cours.php");
exit;
?>