<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

if (!isset($_GET['id_propriete'])) {
    die("ID de la propriété manquant.");
}

$id_propriete = (int) $_GET['id_propriete'];

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);


$stmt = $pdo->prepare("
    SELECT p.*, u.nom AS agent_nom, u.prenom AS agent_prenom, a.id_utilisateur AS id_agent_utilisateur
    FROM propriete p
    JOIN agent a ON p.id_agent = a.id_agent
    JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
    WHERE p.id_propriete = ?
");
$stmt->execute([$id_propriete]);
$bien = $stmt->fetch();

if (!$bien) {
    die("Propriété introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche du bien</title>
</head>
<body>
    <h2><?= htmlspecialchars($bien['titre']) ?></h2>

    <p><strong>Type :</strong> <?= htmlspecialchars($bien['type_bien']) ?></p>
    <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($bien['adresse']) ?></p>
    <p><strong>Superficie :</strong> <?= htmlspecialchars($bien['superficie']) ?> m²</p>
    <p><strong>Prix :</strong> <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>
    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($bien['description'])) ?></p>

    <h3>Agent en charge :</h3>
    <p><?= htmlspecialchars($bien['agent_prenom']) . ' ' . htmlspecialchars($bien['agent_nom']) ?></p>

    <a href="prise_rdv.php?id_agent=<?= $bien['id_agent_utilisateur'] ?>">Prendre rendez-vous avec cet agent</a>

    <br><br>
    <a href="liste_biens.php">← Retour à la liste des biens</a>
</body>
</html>