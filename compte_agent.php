<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Agent') {
    header("Location: connexion.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_utilisateur = $_SESSION['id_utilisateur'];

$stmt = $pdo->prepare("SELECT id_agent FROM agent WHERE id_utilisateur = ?");
$stmt->execute([$id_utilisateur]); 
$agent = $stmt->fetch();

if (!$agent){
    die("Erreur : agent non trouvé.");
}
$id_agent = $agent['id_agent'];
$stmtInfo = $pdo->prepare("SELECT telephone, agence, specialite FROM agent WHERE id_utilisateur = ?");
$stmtInfo->execute([$id_utilisateur]);
$agentInfos = $stmtInfo->fetch(PDO::FETCH_ASSOC);

//Creneaux
$stmt = $pdo->prepare("SELECT id_creneau , date, heure_debut, heure_fin, disponible FROM creneau WHERE id_agent = ? AND disponible = 1 ORDER BY date, heure_debut");
$stmt->execute([$id_agent]);
$creneaux_disponibles = $stmt->fetchAll();

// RDV
$stmt1 = $pdo->prepare("
    SELECT r.*, c.nom AS client_nom, c.prenom AS client_prenom
    FROM rendezvous r
    JOIN utilisateur c ON r.id_client = c.id_utilisateur
    WHERE r.id_agent = ?
");
$stmt1->execute([$id_agent]);
$rdvs = $stmt1->fetchAll();

// Biens
$stmt2 = $pdo->prepare("SELECT * FROM propriete WHERE id_agent = ?");
$stmt2->execute([$id_agent]);
$biens = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Agent - Omnes Immobilier</title>
</head>
<body>

<h2>Bienvenue Agent <?php echo $_SESSION['prenom']; ?> </h2>

<section>
	<h2>Mes informations</h2>
    <p>Nom : <?php echo  $_SESSION['nom']; ?></p>
	<p>Prenom : <?php echo $_SESSION['prenom']; ?></p>
    <p>Email : <?php echo  $_SESSION['email']; ?></p>
	<p>Spécialité : <?= htmlspecialchars($agentInfos['specialite'] ?? 'Non renseignée') ?></p>
	<p>Agence : <?= htmlspecialchars($agentInfos['agence'] ?? 'Non renseignée') ?></p>
	<p>Téléphone : <?= htmlspecialchars($agentInfos['telephone'] ?? 'Non renseigné') ?></p>
    <a href="modifier_profil.php">Modifier mes informations</a>
</section>

<section>
	<h2>Mes rendez-vous</h2>
    <a href="liste_rdv.php">Voir mes rendez-vous </a>
</section>

<section>
	<h2>Mes propriétés</h2>
    <?php if (count($biens) > 0): ?>
		<table border="1" cellpadding="8">
			<tr>
				<th>ID</th>
				<th>Titre</th>
				<th>Ville</th>
				<th>Adresse</th>
				<th>Type</th>
				<th>Superficie</th>
				<th>Prix (€)</th>
				<th>Actions</th>
			</tr>

			<?php foreach ($biens as $bien): ?>
				<tr>
					<td><?= htmlspecialchars($bien['id_propriete']) ?></td>
					<td><?= htmlspecialchars($bien['titre']) ?></td>
					<td><?= htmlspecialchars($bien['ville']) ?></td>
					<td><?= htmlspecialchars($bien['adresse']) ?></td>
					<td><?= htmlspecialchars($bien['type_bien']) ?></td>
					<td><?= htmlspecialchars($bien['superficie']) ?> m²</td>
					<td><?= number_format($bien['prix'], 2, ',', ' ') ?> €</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p>Aucune propriété assignée pour le moment.</p>
	<?php endif; ?>

</section>
<footer>
<a href="accueil.php">Retour à l’accueil</a><br><br>
	<a href="deconnexion.php">Déconnexion</a>
</footer>
</body>
</html>