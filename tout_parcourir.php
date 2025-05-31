<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$categories = ['Immobilier résidentiel','Immobilier commercial','Terrain','Appartement à louer','Immobilier en vente par enchère'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tout Parcourir - Omnes Immobilier</title>
</head>
<body>

	<h1>Nos biens par catégorie</h1>
	<?php foreach ($categories as $categorie): ?>
	<h2><?= htmlspecialchars($categorie) ?></h2>

	<?php
	$stmt = $pdo->prepare("SELECT * FROM propriete WHERE type_bien = ?");
	$stmt->execute([$categorie]);
	$biens = $stmt->fetchAll();
	?>


<?php if (count($biens)): ?>
    <ul><?php foreach ($biens as $bien): ?>
        <li><?= htmlspecialchars($bien['titre']) ?> à <?= htmlspecialchars($bien['ville']) ?> —
        <a href="fiche_bien.php?id_propriete=<?= $bien['id_propriete'] ?>">Voir le bien</a>
        </li>
        <?php endforeach; ?></ul>
		
<?php else: ?>
    <p>Aucun bien disponible dans cette catégorie.</p>
<?php endif; ?>

    <h3>Nos agents spécialisés en <?= htmlspecialchars($categorie) ?> :</h3>
    <?php
	$stmt = $pdo->prepare("SELECT u.nom, u.prenom, u.email, u.id_utilisateur
        FROM utilisateur u
        JOIN agent a ON u.id_utilisateur = a.id_utilisateur
        WHERE a.specialite = ? AND u.role = 'Agent'");
    $stmt->execute([$categorie]);
    $agents = $stmt->fetchAll();?>

    <?php if (!empty($agents) && count($agents)): ?>
        <ul><?php foreach ($agents as $agent): ?>
            <li><?= htmlspecialchars($agent['prenom']) . ' ' . htmlspecialchars($agent['nom']) ?>
			– <a href="prise_rdv.php?id_agent=<?= $agent['id_utilisateur'] ?>">Prendre RDV</a>
            </li>
            <?php endforeach; ?></ul>
			
    <?php else: ?>
    <p>Aucun agent référent actuellement pour cette catégorie.</p>
    <?php endif; ?>
<?php endforeach; ?>

</body>
</html>