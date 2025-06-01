<link rel="stylesheet" href="style.css">
<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_utilisateur = $_SESSION['id_utilisateur'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare("
    SELECT DISTINCT u.id_utilisateur, u.nom, u.prenom
    FROM utilisateur u
    WHERE u.id_utilisateur != :me AND (
        u.id_utilisateur IN (
            SELECT id_expediteur FROM message WHERE id_destinataire = :me
        )
        OR
        u.id_utilisateur IN (
            SELECT id_destinataire FROM message WHERE id_expediteur = :me
        )
    )
");
	$stmt->execute(['me' => $id_utilisateur]);
	$contacts = $stmt->fetchAll();

	$contact_ids = array_column($contacts, 'id_utilisateur');
	$placeholders = implode(',', array_fill(0, count($contact_ids), '?'));

	$sql_agents = "
    SELECT u.id_utilisateur, u.nom, u.prenom
    FROM utilisateur u
    JOIN agent a ON u.id_utilisateur = a.id_utilisateur
    WHERE u.id_utilisateur != ?";
	
	if ($placeholders) {
    $sql_agents .= " AND u.id_utilisateur NOT IN ($placeholders)";
	}

$params = array_merge([$id_utilisateur], $contact_ids);
$stmt_agents = $pdo->prepare($sql_agents);
$stmt_agents->execute($params);
$other_agents = $stmt_agents->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
</head>
<body>
    <h2>Messagerie</h2>
    <p>Bienvenue <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?> !</p>

    <h3>Vos contacts :</h3>
    <?php if (count($contacts) === 0): ?>
        <p>Aucun contact trouvé.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($contacts as $contact): ?>
                <li>
                    <?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?>
                    - <a href="discussion.php?contact_id=<?= $contact['id_utilisateur'] ?>">Contacter</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
	
	<h3>Contacter un autre agent :</h3>
    <?php if (count($other_agents) === 0): ?>
        <p>Aucun autre agent disponible.</p>
		
    <?php else: ?>
        <ul>
            <?php foreach ($other_agents as $agent): ?>
                <li>
                    <?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?>
                    - <a href="discussion.php?contact_id=<?= $agent['id_utilisateur'] ?>">Contacter</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="<?= $role === 'Client' ? 'compte_client.php' : 'compte_agent.php' ?>">Retour à mon compte</a></p>
</body>
</html>
