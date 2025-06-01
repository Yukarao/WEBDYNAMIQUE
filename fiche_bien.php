<link rel="stylesheet" href="style.css">
<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

if (!isset($_GET['id_propriete'])) {
    die("ID de la propriété manquant.");
}

$id_propriete = (int) $_GET['id_propriete'];

// Enregistrement dans les biens consultés
if (!isset($_SESSION['biens_consultes'])) {
    $_SESSION['biens_consultes'] = [];
}
if (!in_array($id_propriete, $_SESSION['biens_consultes'])) {
    $_SESSION['biens_consultes'][] = $id_propriete;
    if (count($_SESSION['biens_consultes']) > 5) {
        array_shift($_SESSION['biens_consultes']); // Supprime le plus ancien
    }
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);

$stmt = $pdo->prepare("
    SELECT p.*, u.nom AS agent_nom, u.prenom AS agent_prenom, a.id_utilisateur AS id_agent_utilisateur
    FROM propriete p
    LEFT JOIN agent a ON p.id_agent = a.id_agent
    LEFT JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
    WHERE p.id_propriete = ?
");

$stmt->execute([$id_propriete]);
$bien = $stmt->fetch();
if (!$bien) {
    die("Propriété introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($bien['titre']) ?></title>
</head>
<body>
    <h2><?= htmlspecialchars($bien['titre']) ?></h2>

    <?php
    $imagePath = 'images/' . $bien['id_propriete'] . '.jpg';
    if (file_exists($imagePath)): ?>
        <img src="<?= $imagePath ?>" alt="Photo du bien" width="400">
    <?php else: ?>
        <p><em>Aucune image pour ce bien.</em></p>
    <?php endif; ?>
    <p><strong>Type :</strong> <?= htmlspecialchars($bien['type_bien']) ?></p>
    <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($bien['adresse']) ?></p>
    <p><strong>Superficie :</strong> <?= htmlspecialchars($bien['superficie']) ?> m²</p>
    <p><strong>Prix :</strong> <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>
    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($bien['description'])) ?></p>

    <h3>Agent référent :</h3>
    <p><?= htmlspecialchars($bien['agent_prenom'] ?? 'Agent inconnu') . ' ' . htmlspecialchars($bien['agent_nom'] ?? '') ?></p>
	
	
	<?php if ($_SESSION['role'] === 'Client'): ?>
    <?php if ($bien['type_bien'] === 'Immobilier en vente par enchère'): ?>
        <form action="participer_enchere.php" method="GET">
            <input type="hidden" name="id_propriete" value="<?= $bien['id_propriete'] ?>">
            <button type="submit">Faire une offre</button>
        </form>
    <?php else: ?>
        <form action="paiement.php" method="GET">
            <input type="hidden" name="id_propriete" value="<?= $bien['id_propriete'] ?>">
            <input type="hidden" name="prix" value="<?= $bien['prix'] ?>">
            <button type="submit">Acheter</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

	<a href="liste_agents.php">Voir les détails de nos agents</a>
    <a href="prise_rdv.php?id_agent=<?= $bien['id_agent_utilisateur'] ?>">Prendre rendez-vous</a>
    <br><br>
    <a href="liste_biens.php">Retour à la liste</a>
</body>
</html>