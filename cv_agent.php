
<?php
if (!isset($_GET['id_agent'])) {
    echo "Agent non spécifié.";
    exit;
}

$id_agent = intval($_GET['id_agent']);

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $pdo->prepare("SELECT a.*, u.nom, u.prenom, u.email
    FROM agent a 
    JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur 
    WHERE a.id_agent = ?");
	
$stmt->execute([$id_agent]);
$agent = $stmt->fetch();

if (!$agent) {
    echo "Agent introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV de <?= htmlspecialchars($agent['nom']) ?></title>
</head>
<body>
    <h1>CV de <?= htmlspecialchars($agent['nom']) ?></h1>

    <h2>Informations de base</h2>
    <p><strong>Nom :</strong> <?= htmlspecialchars($agent['nom']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($agent['email']) ?></p>
    <p><strong>Spécialité :</strong> <?= htmlspecialchars($agent['specialite']) ?></p>

    <h2>Formations</h2>
    <ul>
        <li>Bachelor Immobilier – École Supérieure d’Immobilier</li>
        <li>Certificat de négociation immobilière – CCI Formation</li>
    </ul>

    <h2>Expériences</h2>
    <ul>
        <li>Agent immobilier chez Century 21 (2019 - 2022)</li>
        <li>Responsable de secteur chez Orpi (2022 - aujourd’hui)</li>
    </ul>

    <p><a href="liste_agents.php"> Retour aux agents</a></p>
</body>
</html>
