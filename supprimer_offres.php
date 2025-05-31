<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_client = $_SESSION['id_utilisateur'];
$id_propriete = $_GET['id_propriete'] ?? null;

if ($id_propriete) {
    $stmt = $pdo->prepare("DELETE FROM encheres WHERE id_utilisateur = ? AND id_propriete = ?");
    $stmt->execute([$id_client, $id_propriete]);
}

header("Location: panier.php");
exit;