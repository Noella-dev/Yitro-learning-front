<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../authentification/connexion-formateur.php");
    exit();
}