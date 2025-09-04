<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID leçon manquant.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM lecons WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cours.php");
exit;
