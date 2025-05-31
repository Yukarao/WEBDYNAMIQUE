<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $pdo->query("SELECT a.id_agent,u.id_utilisateur, u.nom, u.prenom, u.email, a.specialite
    FROM utilisateur u
    LEFT JOIN agent a ON u.id_utilisateur = a.id_utilisateur
    WHERE u.role = 'Agent'");
$agents = $stmt->fetchAll();

function sanitizeFileName($str) {      
                $str = strtolower($str);                        
                $str = preg_replace('/[^a-z0-9]/', '_', $str);  
                return $str;
				}	
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
				<th>Photo</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
				<th>Spécialité</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($agents as $agent): ?>
                <tr>
					<td>
						<?php 
						if ($agent['prenom'] === 'Marie') {
                echo '<img src="images/marie.jpg" width="100">';
            } elseif ($agent['prenom'] === 'Jean') {
                echo '<img src="images/jean.jpg" width="100">';
            } elseif ($agent['prenom'] === 'Claire') {
                echo '<img src="images/claire.jpg" width="100">';
            } elseif ($agent['prenom'] === 'Alexandre') {
                echo '<img src="images/alexandre.jpg" width="100">';
            } elseif ($agent['prenom'] === 'Sophie') {
                echo '<img src="images/sophie.jpg" width="100">';
            } else {
                echo '<em>Pas de photo</em>';
            }
			?>
					</td>	
                    <td><?= htmlspecialchars($agent['nom']) ?></td>
                    <td><?= htmlspecialchars($agent['prenom']) ?></td>
                    <td><?= htmlspecialchars($agent['email']) ?></td>
					<td><?= htmlspecialchars($agent['specialite']?? 'Non renseignée') ?></td>
                    <td>
						<a href="cv_agent.php?id_agent=<?= $agent['id_agent'] ?>">Voir son CV</a>
                        <a href="prise_rdv.php?id_agent=<?= $agent['id_utilisateur'] ?>">Prendre rendez-vous</a>
                    </td>
                </tr>
			<?php endforeach; ?>
	</table>
	<?php endif; ?>
	

    <p><a href="compte_client.php">Retour à mon compte</a></p>
</body>
</html>