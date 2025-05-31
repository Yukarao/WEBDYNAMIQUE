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

// Suppression d’un achat
if (isset($_GET['supprimer'])) {
    $id_propriete = (int) $_GET['supprimer'];

    // Supprimer de la table achat
    $pdo->prepare("DELETE FROM achat WHERE id_client = ? AND id_propriete = ?")
        ->execute([$id_client, $id_propriete]);

    // Marquer la propriété comme non vendue
    $pdo->prepare("UPDATE propriete SET vendue = 0 WHERE id_propriete = ?")
        ->execute([$id_propriete]);

    // Supprimer les paiements associés
    $pdo->prepare("DELETE FROM paiement WHERE id_client = ? AND id_propriete = ?")
        ->execute([$id_client, $id_propriete]);
}

// Récupérer les achats
$stmt = $pdo->prepare("
    SELECT p.* 
    FROM achat a 
    JOIN propriete p ON a.id_propriete = p.id_propriete 
    WHERE a.id_client = ?
");
$stmt->execute([$id_client]);
$achats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes achats</title>
</head>
<body>
    <h2>Mes biens achetés</h2>

    <?php if (count($achats) === 0): ?>
        <p>Vous n'avez pas encore effectué d'achat.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($achats as $bien): ?>
                <li>
                    <strong><?= htmlspecialchars($bien['titre']) ?></strong><br>
                    Adresse : <?= htmlspecialchars($bien['adresse']) ?>, <?= htmlspecialchars($bien['ville']) ?><br>
                    Prix : <?= htmlspecialchars($bien['prix']) ?> €<br>
                 </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="compte_client.php">Retour à mon compte</a></p>
</body>
</html>
