<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_creneau'])) {
	$id_creneau = intval($_POST['id_creneau']);
	$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    
    $stmt = $pdo->prepare("DELETE FROM creneau WHERE id_creneau = ?");
    $stmt->execute([$id_creneau]);
}

header("Location: liste_creneaux.php");
exit;