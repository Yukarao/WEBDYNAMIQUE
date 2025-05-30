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
    SELECT u.id_utilisateur, u.nom, u.prenom, u.email, a.specialite
    FROM utilisateur u
    LEFT JOIN agent a ON u.id_utilisateur = a.id_utilisateur
    WHERE u.role = 'Agent'");
$agents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Agents</title>
</head>
<body>
    <h2>Nos agents immobiliers</h2>

    <?php if (count($agents) === 0): ?>
	<p> Aucun agent disponible pour le moment <p>
	
	<?php else: ?>
	<table border="1" cellpadding="8">
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
				<th>Spécialité</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($agents as $agent): ?>
                <tr>
                    <td><?= htmlspecialchars($agent['nom']) ?></td>
                    <td><?= htmlspecialchars($agent['prenom']) ?></td>
                    <td><?= htmlspecialchars($agent['email']) ?></td>
					<td><?= htmlspecialchars($agent['specialite']?? 'Non renseignée') ?></td>
                    <td>
                        <a href="prise_rdv.php?id_agent=<?= $agent['id_utilisateur'] ?>">Prendre RDV</a>
                    </td>
                </tr>
			<?php endforeach; ?>
	</table>
	<?php endif; ?>
	

    <p><a href="compte_client.php">Retour à mon compte</a></p>
</body>
</html>