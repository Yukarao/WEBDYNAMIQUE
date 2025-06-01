<link rel="stylesheet" href="style.css">
<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$stmt = $pdo->query("
    SELECT p.*, a.nom AS agent_nom, a.prenom AS agent_prenom
    FROM propriete p
    LEFT JOIN utilisateur a ON p.id_agent = a.id_utilisateur
    ORDER BY p.ville, p.titre");

$proprietes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des propriétés</title>
</head>
<body>

<h2>Propriétés disponibles</h2>

<?php if (count($proprietes) > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Ville</th>
            <th>Adresse</th>
            <th>Type</th>
            <th>Superficie</th>
            <th>Prix (€)</th>
            <th>Agent référent</th>
			<th>Actions</th>
        </tr>

<?php foreach ($proprietes as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['id_propriete']) ?></td>
        <td><?= htmlspecialchars($p['titre']) ?></td>
        <td><?= htmlspecialchars($p['ville']) ?></td>
        <td><?= htmlspecialchars($p['adresse']) ?></td>
        <td><?= htmlspecialchars($p['type_bien']) ?> </td>
        <td><?= htmlspecialchars($p['superficie']) ?> m² </td>
        <td><?= number_format($p['prix'], 2, ',', ' ') ?>€</td>
		<td>
            <?= isset($p['agent_prenom'], $p['agent_nom']) ? htmlspecialchars($p['agent_prenom']) . ' ' . htmlspecialchars($p['agent_nom']) : 'Non renseigné' ?></td>
        <td>
        <?php if (!empty($p['id_agent'])): ?>
            <a href="prise_rdv.php?id_agent=<?= $p['id_agent'] ?>">Prendre RDV</a>
        <?php else: ?>
            <em>Non disponible</em>
        <?php endif; ?>
        </td>
		</tr>
<?php endforeach; ?>
    </table>
<?php else: ?>
<p>Aucune propriété enregistrée.</p>
<?php endif; ?>


<a href="compte_client.php">Retour au tableau de bord</a>

</body>
</html>