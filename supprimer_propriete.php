<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_propriete'])) {
    $id_propriete = $_POST['id_propriete'];
	
	$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
	
    // Suppression propriete
    $stmt = $pdo->prepare("DELETE FROM propriete WHERE id_propriete = ?");
    $stmt->execute([$id_propriete]);
	
    // Redirection 
    header("Location: admin.php");
    exit;
} else {
    echo "Requête invalide.";
}
?>
