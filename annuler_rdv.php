<link rel="stylesheet" href="style.css">
<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: auth.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_rdv'])) {
    $pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $stmt = $pdo->prepare("DELETE FROM rendezvous WHERE id_rdv = ?");
    $stmt->execute([$_POST['id_rdv']]);

    header("Location: liste_rdv.php");
    exit;
} else {
    echo "Requête invalide.";
}