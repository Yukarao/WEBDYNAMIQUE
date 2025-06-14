<link rel="stylesheet" href="style.css">
<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: connexion.php");
    exit;
}

$client_id = $_SESSION['id_utilisateur'];


// Connexion BDD
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "");
$biens_consultes = [];
if (isset($_SESSION['biens_consultes']) && !empty($_SESSION['biens_consultes'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['biens_consultes']), '?'));
    $stmt = $pdo->prepare("SELECT * FROM propriete WHERE id_propriete IN ($placeholders)");
    $stmt->execute($_SESSION['biens_consultes']);
    $biens_consultes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Recup RDV clients
$stmt = $pdo->prepare("
    SELECT r.id_rdv,r.jour, c.heure_debut, c.heure_fin, a.agence, a.telephone, u.nom, u.prenom 
    FROM rendezvous r
    JOIN creneau c ON r.id_creneau = c.id_creneau
    JOIN agent a ON c.id_agent = a.id_agent
    JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
    WHERE r.id_client = ?
    ORDER BY r.jour DESC ");
	
$stmt->execute([$client_id]);
$rendezvous = $stmt->fetchAll();

//Si bouton supprimer active
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_rdv'])) {
    $id_rdv = (int) $_POST['supprimer_rdv'];
	
	$stmt = $pdo->prepare("SELECT id_creneau FROM rendezvous WHERE id_rdv = ? AND id_client = ?");
    $stmt->execute([$id_rdv, $_SESSION['id_utilisateur']]);
    $rdv = $stmt->fetch();
	
	if ($rdv) {
        $id_creneau = $rdv['id_creneau'];
		$stmt = $pdo->prepare("UPDATE creneau SET disponible = 1 WHERE id_creneau = ?");
        $stmt->execute([$id_creneau]);

		//Creneau remis dispo
		$stmt = $pdo->prepare("UPDATE creneau SET disponible = 1 WHERE id_creneau = ( SELECT id_creneau FROM rendezvous WHERE id_rdv = ? )");
		$stmt->execute([$id_rdv]);
		
		//Supression rdv
		$stmt = $pdo->prepare("DELETE FROM rendezvous WHERE id_rdv = ?");
        $stmt->execute([$id_rdv]);
		$_SESSION['message'] = "Rendez-vous supprimé avec succès.";
		} else{
        $_SESSION['message'] = "Erreur : rendez-vous introuvable ou déjà supprimé.";
    }
    header("Location: compte_client.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Client - Omnes Immobilier</title>
</head>
<body>


<h2>Bonjour <?php echo $_SESSION['prenom']; ?> </h2>

<section>
	<h2>Mes informations</h2>
    <p>Nom : <?php echo  $_SESSION['nom']; ?></p>
    <p>Prenom : <?php echo  $_SESSION['prenom']; ?></p>
	<p>Email : <?php echo  $_SESSION['email']; ?></p>
    <a href="modifier_profil.php">Modifier mes informations</a>
</section>


<?php if (!empty($_SESSION['message'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_SESSION['message']) ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<h3>Vos rendez-vous :</h3>

<?php if ($rendezvous): ?>
<ul>

<?php foreach ($rendezvous as $rdv): ?>
    <p> Le <?= htmlspecialchars($rdv['jour']) ?> de <?= htmlspecialchars($rdv['heure_debut'])?> à <?=htmlspecialchars($rdv['heure_fin'])?>; 
		<br> avec l’agent <?= htmlspecialchars($rdv['prenom']) ?> <?= htmlspecialchars($rdv['nom']) ?>
		<br>(Agence : <?= htmlspecialchars($rdv['agence']?? 'Non renseignée') ?> – Tél : <?= htmlspecialchars($rdv['telephone']?? 'Non renseigné') ?>)
    </p>
	<form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous?');">
        <input type="hidden" name="supprimer_rdv" value="<?= htmlspecialchars($rdv['id_rdv']) ?>">
        <button type="submit">Supprimer ce rendez-vous</button>
    </form>
	
    <hr>
<?php endforeach; ?>

</ul>
<?php else: ?>
        <p>Vous n’avez aucun rendez-vous pour l’instant.</p>
    <?php endif; ?>
	<section>
    <h2>Mes biens récemment consultés</h2>
    <?php if (count($biens_consultes) > 0): ?>
        <ul>
            <?php foreach ($biens_consultes as $bien): ?>
                <li>
                    <a href="fiche_bien.php?id_propriete=<?= $bien['id_propriete'] ?>">
                        <?= htmlspecialchars($bien['titre']) ?> - <?= htmlspecialchars($bien['ville']) ?> - <?= number_format($bien['prix'], 2, ',', ' ') ?> €
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Vous n'avez encore consulté aucun bien.</p>
    <?php endif; ?>
</section>
<h2>Actions rapides</h2>
    <ul>
		<li><a href="mes_achats.php">Voir mes achats</a></li>
		<li><a href="messagerie.php">Ecrire à un agent</a></li>
        <li><a href="tout_parcourir.php">Consulter les différentes catégories de biens</a></li>
        <li><a href="liste_agents.php">Voir nos agents immobiliers</a></li>
		<li><a href="accueil.php">Retour à l’accueil</a><br><br></li>
        <li><a href="deconnexion.php">Se déconnecter</a></li>
    </ul>
</body>
</html>