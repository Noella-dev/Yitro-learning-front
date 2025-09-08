<?php
session_start();

// Supprime toutes les variables de session
$_SESSION = [];

// DÃ©truit la session
session_destroy();

// Redirige vers la page de connexion
header("Location: connexion.php");
exit;
?>
