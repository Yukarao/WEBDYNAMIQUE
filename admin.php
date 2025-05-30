<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "");

// Utilisateurs
$stmt1 = $pdo->query("SELECT * FROM utilisateur");
$utilisateurs = $stmt1->fetchAll();

// Biens
$nb_biens = $pdo->query("SELECT COUNT(*) FROM propriete")->fetchColumn();
$biens = $pdo->query("
    SELECT p.*, u.nom AS agent_nom, u.prenom AS agent_prenom
    FROM propriete p
    LEFT JOIN utilisateur u ON p.id_agent = u.id_utilisateur
")->fetchAll(PDO::FETCH_ASSOC);

// RDV
$nb_rdv = $pdo->query("SELECT COUNT(*) FROM rendezvous")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Admin - Omnes Immobilier</title>
</head>
<body>

<h2>Bienvenue Administrateur <?php echo $_SESSION['prenom']; ?> </h2>
<?php if (isset($_GET['message']) && $_GET['message'] === 'ajout_reussi'): ?>
    <p style="color: green; font-weight: bold;"> Utilisateur ajouté avec succès !</p>
<?php endif; ?>
<section>
	<h2>Gestion des utilisateurs</h2>

<?php if (count($utilisateurs) > 0): ?>
	<table border="1" cellpadding="8">
		<tr>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Email</th>
			<th>Rôle</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($utilisateurs as $user): ?>
		<tr>
			<td><?= htmlspecialchars($user['nom']) ?></td>
			<td><?= htmlspecialchars($user['prenom']) ?></td>
			<td><?= htmlspecialchars($user['email']) ?></td>
			<td><?= htmlspecialchars($user['role']) ?></td>
			<td>
				<a href="modifier_utilisateur.php?id=<?= $user['id_utilisateur'] ?>">Modifier</a>
				&nbsp;|&nbsp;
				<form action="supprimer_utilisateur.php" method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
				<input type="hidden" name="id_utilisateur" value="<?= $user['id_utilisateur'] ?>">
				<button type="submit">Supprimer</button>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php else: ?>
		<p>Aucun utilisateur enregistré.</p>
	<?php endif; ?>

	<br>
	<a href="ajouter_utilisateur.php" >Ajouter un utilisateur</a>
</section>

<section>

	<h2>Gestion des biens immobiliers</h2>	
<?php if (count($biens) > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Titre</th>
                <th>Adresse</th>
                <th>Ville</th>
                <th>Prix (€)</th>
                <th>Type</th>
                <th>Superficie (m²)</th>
				<th>Agent référent</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($biens as $bien): ?>
                <tr>
                    <td><?= htmlspecialchars($bien['titre']) ?></td>
                    <td><?= htmlspecialchars($bien['adresse']) ?></td>
                    <td><?= htmlspecialchars($bien['ville']) ?></td>
                    <td><?= htmlspecialchars($bien['prix']) ?></td>
                    <td><?= htmlspecialchars($bien['type_bien']) ?></td>
                    <td><?= htmlspecialchars($bien['superficie']) ?></td>
					<td><?= htmlspecialchars($bien['agent_prenom'] ?? 'Non renseigné') ?> <?= htmlspecialchars($bien['agent_nom'] ?? '') ?></td
				</tr>
                    <td>
                        <a href="modifier_propriete.php?id=<?= $bien['id_propriete'] ?>">Modifier</a>
                        &nbsp;|&nbsp;
                        <form action="supprimer_propriete.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette propriété ?');" style="display:inline;">
						<input type="hidden" name="id_propriete" value="<?= $bien['id_propriete'] ?>">
						<button type="submit">Supprimer</button>
						</form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
<?php else: ?>
    <p>Aucune propriété enregistrée.</p>
<?php endif; ?>
<a href="ajouter_propriete.php">Ajouter une propriété</a>
</section>

<section>
	<h2>Gestion des rendez-vous</h2>
    <ul>
        <li><a href="liste_rdv.php">Voir tous les rendez-vous</a></li>
    </ul>
</section>
<section>
    <h2>Gestion des créneaux des agents</h2>
    <p><a href="modifier_creneau.php">Ajouter ou modifier des créneaux</a></p>

<?php
    $creneaux = $pdo->query("
        SELECT c.*, u.nom, u.prenom
        FROM creneau c
        LEFT JOIN agent a ON c.id_agent = a.id_agent
        LEFT JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
        ORDER BY c.date, c.heure_debut
    ")->fetchAll(PDO::FETCH_ASSOC);
?>

    <?php if (count($creneaux) > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Agent</th>
                <th>Date</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Disponible</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($creneaux as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                    <td><?= htmlspecialchars($c['date']) ?></td>
                    <td><?= htmlspecialchars($c['heure_debut']) ?></td>
                    <td><?= htmlspecialchars($c['heure_fin']) ?></td>
                    <td><?= $c['disponible'] ? 'Oui' : 'Non' ?></td>
                    <td>
                        <form action="supprimer_creneau.php" method="POST" onsubmit="return confirm('Supprimer ce créneau ?');" style="display:inline;">
                            <input type="hidden" name="id_creneau" value="<?= $c['id_creneau'] ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucun créneau enregistré.</p>
    <?php endif; ?>
</section>
<section>
	<h2>Mon profil</h2>
    <p>Nom : <?php echo $_SESSION['nom']; ?></p>
	<p>Prénom : <?php echo $_SESSION['prenom']; ?></p>
    <p>Email : <?php echo $_SESSION['email']; ?></p>
    <a href="modifier_profil.php">Modifier mes informations</a>
</section>

<footer>
	<a href="deconnexion.php">Déconnexion</a>
</footer>
</body>
</html>


